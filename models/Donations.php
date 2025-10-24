<?php
class Donation {
    protected $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($name, $phone, $amount, $payment_method) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO donations (name, phone, amount, payment_method) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$name, $phone, $amount, $payment_method]);
        return $this->pdo->lastInsertId();
    }
}
