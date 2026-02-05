<?php
// Accounting Class
class Accounting {
    private $coopDB;
    
    public function __construct() {
        $app = App::getInstance();
        $this->coopDB = $app->getCoopDB();
    }
    
    // Get Chart of Accounts
    public function getChartOfAccounts($parentId = null) {
        try {
            $sql = "SELECT * FROM chart_of_accounts WHERE is_active = 1";
            $params = [];
            
            if ($parentId !== null) {
                $sql .= " AND parent_id = ?";
                $params[] = $parentId;
            }
            
            $sql .= " ORDER BY code";
            
            $stmt = $this->coopDB->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Get Account by ID
    public function getAccount($id) {
        try {
            $stmt = $this->coopDB->prepare("SELECT * FROM chart_of_accounts WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }
    
    // Create Account
    public function createAccount($code, $name, $type, $parentId = null) {
        try {
            $validTypes = ['asset', 'liability', 'equity', 'revenue', 'expense'];
            if (!in_array($type, $validTypes)) {
                return ['success' => false, 'message' => 'Invalid account type'];
            }
            
            // Check if code already exists
            $checkStmt = $this->coopDB->prepare("SELECT id FROM chart_of_accounts WHERE code = ?");
            $checkStmt->execute([$code]);
            if ($checkStmt->fetch()) {
                return ['success' => false, 'message' => 'Account code already exists'];
            }
            
            $stmt = $this->coopDB->prepare("
                INSERT INTO chart_of_accounts (code, name, type, parent_id) 
                VALUES (?, ?, ?, ?)
            ");
            $result = $stmt->execute([$code, $name, $type, $parentId]);
            
            if ($result) {
                $accountId = $this->coopDB->lastInsertId();
                return ['success' => true, 'account_id' => $accountId];
            }
            
            return ['success' => false, 'message' => 'Failed to create account'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Create Journal Entry
    public function createJournalEntry($date, $description, $details, $referenceNumber = null) {
        try {
            $this->coopDB->beginTransaction();
            
            // Validate debit = credit
            $totalDebit = array_sum(array_column($details, 'debit'));
            $totalCredit = array_sum(array_column($details, 'credit'));
            
            if (abs($totalDebit - $totalCredit) > 0.01) {
                $this->coopDB->rollBack();
                return ['success' => false, 'message' => 'Total debit must equal total credit'];
            }
            
            // Insert journal entry
            $stmt = $this->coopDB->prepare("
                INSERT INTO journal_entries (entry_date, description, reference_number, status, posted_by) 
                VALUES (?, ?, ?, 'posted', ?)
            ");
            $userId = $_SESSION['user_id'] ?? null;
            $stmt->execute([$date, $description, $referenceNumber, $userId]);
            $journalEntryId = $this->coopDB->lastInsertId();
            
            // Insert journal entry details
            $detailStmt = $this->coopDB->prepare("
                INSERT INTO journal_entry_details (journal_entry_id, account_id, debit, credit, description) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            foreach ($details as $detail) {
                $detailStmt->execute([
                    $journalEntryId,
                    $detail['account_id'],
                    $detail['debit'],
                    $detail['credit'],
                    $detail['description'] ?? ''
                ]);
            }
            
            // Update general ledger
            $this->updateGeneralLedger($journalEntryId, $date, $details);
            
            $this->coopDB->commit();
            
            return ['success' => true, 'journal_entry_id' => $journalEntryId];
        } catch (Exception $e) {
            $this->coopDB->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Get Journal Entries
    public function getJournalEntries($startDate = null, $endDate = null, $limit = 50, $offset = 0) {
        try {
            $sql = "SELECT je.*, u.nama as posted_by_name 
                    FROM journal_entries je 
                    LEFT JOIN people_db.users u ON je.posted_by = u.id";
            $params = [];
            
            if ($startDate && $endDate) {
                $sql .= " WHERE je.entry_date BETWEEN ? AND ?";
                $params[] = $startDate;
                $params[] = $endDate;
            }
            
            $sql .= " ORDER BY je.entry_date DESC, je.id DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            
            $stmt = $this->coopDB->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Get Journal Entry with Details
    public function getJournalEntry($id) {
        try {
            // Get journal entry
            $stmt = $this->coopDB->prepare("
                SELECT je.*, u.nama as posted_by_name 
                FROM journal_entries je 
                LEFT JOIN people_db.users u ON je.posted_by = u.id 
                WHERE je.id = ?
            ");
            $stmt->execute([$id]);
            $journalEntry = $stmt->fetch();
            
            if ($journalEntry) {
                // Get details
                $detailStmt = $this->coopDB->prepare("
                    SELECT jed.*, coa.code, coa.name as account_name, coa.type
                    FROM journal_entry_details jed
                    JOIN chart_of_accounts coa ON jed.account_id = coa.id
                    WHERE jed.journal_entry_id = ?
                    ORDER BY jed.id
                ");
                $detailStmt->execute([$id]);
                $journalEntry['details'] = $detailStmt->fetchAll();
            }
            
            return $journalEntry;
        } catch (Exception $e) {
            return null;
        }
    }
    
    // Update General Ledger
    private function updateGeneralLedger($journalEntryId, $date, $details) {
        try {
            // Get period (first day of month)
            $period = date('Y-m-01', strtotime($date));
            
            foreach ($details as $detail) {
                $accountId = $detail['account_id'];
                $debit = $detail['debit'];
                $credit = $detail['credit'];
                
                // Get current ledger balance
                $stmt = $this->coopDB->prepare("
                    SELECT ending_balance FROM general_ledger 
                    WHERE account_id = ? AND period = ?
                ");
                $stmt->execute([$accountId, $period]);
                $currentLedger = $stmt->fetch();
                
                if ($currentLedger) {
                    // Update existing ledger
                    $updateStmt = $this->coopDB->prepare("
                        UPDATE general_ledger 
                        SET debit_total = debit_total + ?, 
                            credit_total = credit_total + ?,
                            ending_balance = ending_balance + ? - ?
                        WHERE account_id = ? AND period = ?
                    ");
                    $updateStmt->execute([$debit, $credit, $debit, $credit, $accountId, $period]);
                } else {
                    // Get beginning balance from previous period
                    $previousPeriod = date('Y-m-01', strtotime($date . ' -1 month'));
                    $prevStmt = $this->coopDB->prepare("
                        SELECT ending_balance FROM general_ledger 
                        WHERE account_id = ? AND period = ?
                    ");
                    $prevStmt->execute([$accountId, $previousPeriod]);
                    $prevLedger = $prevStmt->fetch();
                    $beginningBalance = $prevLedger ? $prevLedger['ending_balance'] : 0.00;
                    
                    // Insert new ledger
                    $insertStmt = $this->coopDB->prepare("
                        INSERT INTO general_ledger 
                        (account_id, period, beginning_balance, debit_total, credit_total, ending_balance) 
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");
                    $endingBalance = $beginningBalance + $debit - $credit;
                    $insertStmt->execute([$accountId, $period, $beginningBalance, $debit, $credit, $endingBalance]);
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    // Generate Trial Balance
    public function getTrialBalance($asOfDate) {
        try {
            $period = date('Y-m-01', strtotime($asOfDate));
            
            $stmt = $this->coopDB->prepare("
                SELECT coa.id, coa.code, coa.name, coa.type,
                       COALESCE(gl.ending_balance, 0) as balance
                FROM chart_of_accounts coa
                LEFT JOIN general_ledger gl ON coa.id = gl.account_id AND gl.period = ?
                WHERE coa.is_active = 1
                ORDER BY coa.code
            ");
            $stmt->execute([$period]);
            $accounts = $stmt->fetchAll();
            
            $trialBalance = [
                'assets' => [],
                'liabilities' => [],
                'equity' => [],
                'revenue' => [],
                'expenses' => [],
                'total_debit' => 0,
                'total_credit' => 0
            ];
            
            foreach ($accounts as $account) {
                $balance = floatval($account['balance']);
                $debit = 0;
                $credit = 0;
                
                // Determine debit or credit balance based on account type
                if (in_array($account['type'], ['asset', 'expense'])) {
                    $debit = $balance > 0 ? $balance : 0;
                    $credit = $balance < 0 ? abs($balance) : 0;
                } else {
                    $credit = $balance > 0 ? $balance : 0;
                    $debit = $balance < 0 ? abs($balance) : 0;
                }
                
                $accountData = [
                    'code' => $account['code'],
                    'name' => $account['name'],
                    'debit' => $debit,
                    'credit' => $credit
                ];
                
                $trialBalance[$account['type'] . 's'][] = $accountData;
                $trialBalance['total_debit'] += $debit;
                $trialBalance['total_credit'] += $credit;
            }
            
            return $trialBalance;
        } catch (Exception $e) {
            return null;
        }
    }
}
