<?php

namespace App\Controllers;

use App\Models\Database;

class TransactionController
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getTransaction($merchantID, $referencesID)
    {

        $result = $this->db->getByMerchantIDReferencesID($merchantID, $referencesID);

        return json_encode($result);
    }

    public function store($data)
    {
        $result = $this->db->insert($data);

        if ($result != "false") {
            $singleResult = $this->db->getByID($result);

            return json_encode($singleResult);
        } else {
            return json_encode(['message' => 'Failed insert data']);
        }
    }

    public function update($referencesID, $status)
    {
        $result = $this->db->update($referencesID, $status);
        if ($result) {
            echo "Updated status successfull";
        } else {
            echo "Updated status failed";
        }
    }
}
