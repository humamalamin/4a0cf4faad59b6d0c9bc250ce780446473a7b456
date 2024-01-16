<?php

namespace App\Models;

use PDO;
use Ramsey\Uuid\Uuid;

class Database
{
    private $config;
    private $conn;

    public function __construct()
    {
        $this->config = require_once './config/config.php';

        try {
            $this->conn = new \PDO(
                "mysql:host={$this->config['mysql']['host']};port={$this->config['mysql']['port']};
                dbname={$this->config['mysql']['name']}",
                $this->config['mysql']['username'],
                $this->config['mysql']['password']
            );

            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $th) {
            echo "Connection failed: " . $th->getMessage();
            exit;
        }
    }

    public function getByMerchantIDReferencesID($merchantID, $referencesID)
    {
        $query = "SELECT references_id, status, invoice_id FROM transactions
            WHERE merchant_id = '$merchantID' AND references_id = '$referencesID'
            AND deleted_at IS NULL
            ORDER BY created_at DESC LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByID($id)
    {
        $query = "SELECT references_id, number_va, status FROM transactions
                WHERE id = $id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert($data)
    {
        try {
            $query = "INSERT INTO transactions (
                id,
                invoice_id,
                item_name,
                amount,
                merchant_id,
                payment_type,
                customer_name,
                number_va,
                references_id,
                status
            ) VALUES (
                :id,
                :invoice_id,
                :item_name,
                :amount,
                :merchant_id,
                :payment_type,
                :customer_name,
                :number_va,
                :references_id,
                :status
            ) ";
            $id = Uuid::uuid4();
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":item_name", $data['item_name']);
            $stmt->bindParam(":invoice_id", $data['invoice_id']);
            $stmt->bindParam(":amount", $data['amount']);
            $stmt->bindParam(":merchant_id", $data['merchant_id']);
            $stmt->bindParam(":payment_type", $data['payment_type']);
            $stmt->bindParam(":customer_name", $data['customer_name']);
            $stmt->bindParam(":number_va", $data['number_va']);
            $stmt->bindParam(":references_id", $data['references_id']);
            $stmt->bindParam(":status", $data['status']);

            $result = $stmt->execute();

            if ($result) {
                return $this->conn->lastInsertId();
            } else {
                return "false";
            }
        } catch (\PDOException $th) {
            throw $th;
        }
    }

    public function update($referencesID, $status)
    {
        try {
            $this->conn->beginTransaction();

            $query = "UPDATE transactions SET status= :status
                WHERE references_id = :references_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":references_id", $referencesID);
            $stmt->bindParam(":status", $status);
            $stmt->execute();

            $this->conn->commit();

            return true;
        } catch (\PDOException $th) {
            $this->conn->rollBack();
            error_log($th->getMessage());
            return false;
        }
    }
}
