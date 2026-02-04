<?php
class Loans {
    private $pdo;

    public function __construct() {
        $this->pdo = getCoopDB();
    }

    // Get anggota id from user id
    private function getAnggotaId($userId) {
        $stmt = executeQuery($this->pdo, "SELECT id FROM anggota WHERE user_id = ?", [$userId]);
        $row = $stmt->fetch();
        return $row ? $row['id'] : null;
    }

    // Apply for loan
    public function applyLoan($userId, $amount, $interestRate, $termMonths) {
        $anggotaId = $this->getAnggotaId($userId);
        if (!$anggotaId) return ['success' => false, 'message' => 'Anggota tidak ditemukan'];

        executeQuery($this->pdo, 
            "INSERT INTO pinjaman (anggota_id, amount, interest_rate, term_months, status) 
             VALUES (?, ?, ?, ?, 'pending')",
            [$anggotaId, $amount, $interestRate, $termMonths]
        );

        return ['success' => true, 'message' => 'Pengajuan pinjaman berhasil'];
    }

    // Get user's loans
    public function getLoans($userId) {
        $anggotaId = $this->getAnggotaId($userId);
        if (!$anggotaId) return [];

        $stmt = executeQuery($this->pdo, 
            "SELECT * FROM pinjaman WHERE anggota_id = ? ORDER BY created_at DESC",
            [$anggotaId]
        );
        return $stmt->fetchAll();
    }

    // Get loan details with installments
    public function getLoanDetails($loanId, $userId) {
        $anggotaId = $this->getAnggotaId($userId);
        $stmt = executeQuery($this->pdo, 
            "SELECT * FROM pinjaman WHERE id = ? AND anggota_id = ?",
            [$loanId, $anggotaId]
        );
        $loan = $stmt->fetch();
        if (!$loan) return null;

        $stmt = executeQuery($this->pdo, 
            "SELECT * FROM pinjaman_angsuran WHERE pinjaman_id = ? ORDER BY due_date",
            [$loanId]
        );
        $loan['installments'] = $stmt->fetchAll();

        return $loan;
    }

    // Pay installment (simplified)
    public function payInstallment($loanId, $installmentId, $amount, $userId) {
        $anggotaId = $this->getAnggotaId($userId);
        $stmt = executeQuery($this->pdo, 
            "SELECT * FROM pinjaman_angsuran WHERE id = ? AND pinjaman_id IN (SELECT id FROM pinjaman WHERE anggota_id = ?)",
            [$installmentId, $anggotaId]
        );
        $installment = $stmt->fetch();
        if (!$installment) return ['success' => false, 'message' => 'Angsuran tidak ditemukan'];

        if ($installment['paid_amount'] + $amount > $installment['total_amount']) {
            return ['success' => false, 'message' => 'Jumlah pembayaran melebihi yang dibutuhkan'];
        }

        $newPaid = $installment['paid_amount'] + $amount;
        $paid = $newPaid >= $installment['total_amount'] ? date('Y-m-d H:i:s') : null;

        executeQuery($this->pdo, 
            "UPDATE pinjaman_angsuran SET paid_amount = ?, paid_at = ? WHERE id = ?",
            [$newPaid, $paid, $installmentId]
        );

        return ['success' => true, 'message' => 'Pembayaran angsuran berhasil'];
    }

    // Approve loan (admin function, simplified)
    public function approveLoan($loanId) {
        executeQuery($this->pdo, 
            "UPDATE pinjaman SET status = 'approved', approved_at = NOW() WHERE id = ?",
            [$loanId]
        );
        // Generate installments
        $this->generateInstallments($loanId);
        return ['success' => true, 'message' => 'Pinjaman disetujui'];
    }

    // Generate loan installments
    private function generateInstallments($loanId) {
        $stmt = executeQuery($this->pdo, "SELECT * FROM pinjaman WHERE id = ?", [$loanId]);
        $loan = $stmt->fetch();
        if (!$loan) return;

        $principal = $loan['amount'];
        $rate = $loan['interest_rate'] / 100 / 12; // monthly
        $months = $loan['term_months'];
        $monthlyPayment = $principal * ($rate * pow(1 + $rate, $months)) / (pow(1 + $rate, $months) - 1);

        $remainingBalance = $principal;
        for ($i = 1; $i <= $months; $i++) {
            $interest = $remainingBalance * $rate;
            $principalPayment = $monthlyPayment - $interest;
            $dueDate = date('Y-m-d', strtotime("+{$i} months", strtotime($loan['approved_at'])));

            executeQuery($this->pdo, 
                "INSERT INTO pinjaman_angsuran (pinjaman_id, installment_number, due_date, principal_amount, interest_amount, total_amount) 
                 VALUES (?, ?, ?, ?, ?, ?)",
                [$loanId, $i, $dueDate, $principalPayment, $interest, $monthlyPayment]
            );

            $remainingBalance -= $principalPayment;
        }
    }
}
?>
