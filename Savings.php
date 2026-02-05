<?php
class Savings {
    private $pdo;

    public function __construct() {
        $this->pdo = getCoopDB();
    }

    // Get anggota id from user id (assuming 1:1 for now)
    private function getAnggotaId($userId) {
        $stmt = executeQuery($this->pdo, "SELECT id FROM anggota WHERE user_id = ?", [$userId]);
        $row = $stmt->fetch();
        return $row ? $row['id'] : null;
    }

    // Get savings balance for user
    public function getBalance($userId) {
        $anggotaId = $this->getAnggotaId($userId);
        if (!$anggotaId) return 0;

        $stmt = executeQuery($this->pdo, 
            "SELECT SUM(CASE WHEN transaction_type = 'deposit' THEN amount ELSE -amount END) as balance 
             FROM simpanan_transactions WHERE anggota_id = ?",
            [$anggotaId]
        );
        $row = $stmt->fetch();
        return $row['balance'] ?: 0;
    }

    // Deposit savings
    public function deposit($userId, $typeId, $amount) {
        $anggotaId = $this->getAnggotaId($userId);
        if (!$anggotaId) return ['success' => false, 'message' => 'Anggota tidak ditemukan'];

        $balance = $this->getBalance($userId) + $amount;

        executeQuery($this->pdo, 
            "INSERT INTO simpanan_transactions (anggota_id, type_id, amount, transaction_type, balance_after) 
             VALUES (?, ?, ?, 'deposit', ?)",
            [$anggotaId, $typeId, $amount, $balance]
        );

        return ['success' => true, 'message' => 'Setoran berhasil', 'balance' => $balance];
    }

    // Withdraw savings
    public function withdraw($userId, $amount) {
        $anggotaId = $this->getAnggotaId($userId);
        if (!$anggotaId) return ['success' => false, 'message' => 'Anggota tidak ditemukan'];

        $balance = $this->getBalance($userId);
        if ($balance < $amount) return ['success' => false, 'message' => 'Saldo tidak cukup'];

        $newBalance = $balance - $amount;

        executeQuery($this->pdo, 
            "INSERT INTO simpanan_transactions (anggota_id, type_id, amount, transaction_type, balance_after) 
             VALUES (?, 1, ?, 'withdraw', ?)",  // Assume type_id 1 for withdraw
            [$anggotaId, $amount, $newBalance]
        );

        return ['success' => true, 'message' => 'Penarikan berhasil', 'balance' => $newBalance];
    }

    // Get transaction history
    public function getTransactions($userId, $limit = 10) {
        $anggotaId = $this->getAnggotaId($userId);
        if (!$anggotaId) return [];

        $stmt = executeQuery($this->pdo, 
            "SELECT st.*, stt.name as type_name FROM simpanan_transactions st 
             JOIN simpanan_types stt ON st.type_id = stt.id 
             WHERE st.anggota_id = ? ORDER BY st.transaction_date DESC LIMIT ?",
            [$anggotaId, $limit]
        );
        return $stmt->fetchAll();
    }

    // Get savings types
    public function getSavingsTypes() {
        $stmt = executeQuery($this->pdo, "SELECT * FROM simpanan_types WHERE status = 'active'");
        return $stmt->fetchAll();
    }
}
?>
