<?php
class Accounting {
    private $pdo;

    public function __construct() {
        $this->pdo = getCoopDB();
    }

    // Add journal entry
    public function addJournalEntry($date, $description, $entries) {
        // Insert header
        executeQuery($this->pdo, 
            "INSERT INTO journal_entries (entry_date, description, status) VALUES (?, ?, 'posted')",
            [$date, $description]
        );
        $entryId = $this->pdo->lastInsertId();

        // Insert details
        foreach ($entries as $entry) {
            executeQuery($this->pdo, 
                "INSERT INTO journal_entry_details (journal_entry_id, account_id, debit, credit) 
                 VALUES (?, ?, ?, ?)",
                [$entryId, $entry['account_id'], $entry['debit'] ?? 0, $entry['credit'] ?? 0]
            );
        }

        // Update general ledger
        $this->updateGeneralLedger($entryId);

        return ['success' => true, 'message' => 'Journal entry posted'];
    }

    // Update general ledger
    private function updateGeneralLedger($entryId) {
        $stmt = executeQuery($this->pdo, 
            "SELECT jed.account_id, SUM(jed.debit) as total_debit, SUM(jed.credit) as total_credit, je.entry_date
             FROM journal_entry_details jed
             JOIN journal_entries je ON jed.journal_entry_id = je.id
             WHERE jed.journal_entry_id = ?
             GROUP BY jed.account_id, je.entry_date",
            [$entryId]
        );

        while ($row = $stmt->fetch()) {
            $period = date('Y-m-01', strtotime($row['entry_date']));
            $stmt2 = executeQuery($this->pdo, 
                "SELECT * FROM general_ledger WHERE account_id = ? AND period = ?",
                [$row['account_id'], $period]
            );
            $ledger = $stmt2->fetch();

            if ($ledger) {
                $newDebit = $ledger['debit_total'] + $row['total_debit'];
                $newCredit = $ledger['credit_total'] + $row['total_credit'];
                $newBalance = $ledger['beginning_balance'] + $newDebit - $newCredit;
                executeQuery($this->pdo, 
                    "UPDATE general_ledger SET debit_total = ?, credit_total = ?, ending_balance = ? 
                     WHERE account_id = ? AND period = ?",
                    [$newDebit, $newCredit, $newBalance, $row['account_id'], $period]
                );
            } else {
                // Assume beginning balance 0 for simplicity
                $balance = $row['total_debit'] - $row['total_credit'];
                executeQuery($this->pdo, 
                    "INSERT INTO general_ledger (account_id, period, beginning_balance, debit_total, credit_total, ending_balance) 
                     VALUES (?, ?, 0, ?, ?, ?)",
                    [$row['account_id'], $period, $row['total_debit'], $row['total_credit'], $balance]
                );
            }
        }
    }

    // Get account balance
    public function getAccountBalance($accountId, $period = null) {
        if (!$period) $period = date('Y-m-01');

        $stmt = executeQuery($this->pdo, 
            "SELECT ending_balance FROM general_ledger WHERE account_id = ? AND period <= ? 
             ORDER BY period DESC LIMIT 1",
            [$accountId, $period]
        );
        $row = $stmt->fetch();
        return $row ? $row['ending_balance'] : 0;
    }

    // Get chart of accounts
    public function getChartOfAccounts() {
        $stmt = executeQuery($this->pdo, "SELECT * FROM chart_of_accounts WHERE is_active = 1 ORDER BY code");
        return $stmt->fetchAll();
    }

    // Get journal entries
    public function getJournalEntries($limit = 50) {
        $stmt = executeQuery($this->pdo, 
            "SELECT * FROM journal_entries ORDER BY entry_date DESC LIMIT ?",
            [$limit]
        );
        return $stmt->fetchAll();
    }

    // Get profit/loss (simplified)
    public function getProfitLoss($startDate, $endDate) {
        // Sum revenue and expenses
        $stmt = executeQuery($this->pdo, 
            "SELECT 
                SUM(CASE WHEN coa.type = 'revenue' THEN gl.ending_balance ELSE 0 END) as revenue,
                SUM(CASE WHEN coa.type = 'expense' THEN gl.ending_balance ELSE 0 END) as expenses
             FROM general_ledger gl
             JOIN chart_of_accounts coa ON gl.account_id = coa.id
             WHERE gl.period BETWEEN ? AND ?",
            [$startDate, $endDate]
        );
        $row = $stmt->fetch();
        return [
            'revenue' => $row['revenue'] ?: 0,
            'expenses' => $row['expenses'] ?: 0,
            'profit' => ($row['revenue'] ?: 0) - ($row['expenses'] ?: 0)
        ];
    }
}
?>
