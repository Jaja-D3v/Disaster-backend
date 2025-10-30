<?php
class Donation {
    protected $pdo;
    protected $encryptionKey;

    public function __construct(PDO $pdo, string $encryptionKey) {
        $this->pdo = $pdo;
        $this->encryptionKey = base64_decode($encryptionKey); // decode base64 key
    }

    private function encrypt(string $data): string {
        $cipher = 'aes-256-cbc';
        $ivLength = openssl_cipher_iv_length($cipher); // 16 bytes
        $iv = openssl_random_pseudo_bytes($ivLength);  // generate secure IV
        $encrypted = openssl_encrypt($data, $cipher, $this->encryptionKey, OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $encrypted); // store IV + ciphertext together
    }

    public function decrypt(string $data): string {
        $cipher = 'aes-256-cbc';
        $raw = base64_decode($data);
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = substr($raw, 0, $ivLength);
        $encrypted = substr($raw, $ivLength);
        return openssl_decrypt($encrypted, $cipher, $this->encryptionKey, OPENSSL_RAW_DATA, $iv);
    }

    public function create(string $paymentIntentId, string $name, string $email, int $amount, string $currency = "PHP", ?string $paymentMethod = null, string $status = "pending"): int {
        $stmt = $this->pdo->prepare("
            INSERT INTO donations (payment_intent_id, donor_name, donor_email, amount, currency, payment_method, status)
            VALUES (:payment_intent_id, :donor_name, :donor_email, :amount, :currency, :payment_method, :status)
        ");

        $encryptedName = $this->encrypt($name);
        $encryptedEmail = $this->encrypt($email);

        $stmt->execute([
            ':payment_intent_id' => $paymentIntentId,
            ':donor_name'        => $encryptedName,
            ':donor_email'       => $encryptedEmail,
            ':amount'            => $amount,
            ':currency'          => $currency,
            ':payment_method'    => $paymentMethod,
            ':status'            => $status
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function getDonationById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM donations WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        $row['donor_name']  = $this->decrypt($row['donor_name']);
        $row['donor_email'] = $this->decrypt($row['donor_email']);

        return $row;
    }


    public function getAllDonations(): array {
        $stmt = $this->pdo->query("SELECT * FROM donations ORDER BY created_at DESC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$row) {
            $row['donor_name']  = $this->decrypt($row['donor_name']);
            $row['donor_email'] = $this->decrypt($row['donor_email']);
        }

        return $rows;
    }
}
