<?php

require_once 'vendor/autoload.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

use App\Controllers\TransactionController;
use Ramsey\Uuid\Uuid;

$request_uri = $_SERVER['REQUEST_URI'];

$request_uri = explode('?', $request_uri);

if ($request_uri[0] == '/transactions') {
    $transactionController = new TransactionController();
    if (isset($_SERVER['REQUEST_METHOD']) &&
        $_SERVER['REQUEST_METHOD'] === 'GET'
    ) {
        $resultValidate = validationQuery($_GET);
        if (!empty($resultValidate)) {
            echo json_encode([
                'message' => $resultValidate
            ]);

            return;
        }

        echo $transactionController->getTransaction($_GET['merchant_id'], $_GET['references_id']);
        return;
    } elseif (isset($_SERVER['REQUEST_METHOD']) &&
        $_SERVER['REQUEST_METHOD'] === 'POST'
    ) {
        $status = 'Pending';
        if (isset($_POST['status'])) {
            $status = $_POST['status'];
        }

        $resultValidate = validationInput($_POST);

        if (!empty($resultValidate)) {
            echo json_encode([
                'message'=> $resultValidate
            ]);
            return;
        }

        $numberVa = null;
        if ($_POST['payment_type'] == 'virtual_account') {
            $numberVa = generateRandomString();
        }

        $dataRequest = [
            'invoice_id' => $_POST['invoice_id'],
            'item_name' => $_POST['item_name'],
            'merchant_id' => $_POST['merchant_id'],
            'amount' => $_POST['amount'],
            'payment_type' => $_POST['payment_type'],
            'customer_name' => $_POST['customer_name'],
            'number_va' => $numberVa,
            'references_id' => Uuid::uuid4(),
            'status' => $status
        ];

        http_response_code(201);
        echo $transactionController->store($dataRequest);
        return;
    }
}

function generateRandomString($length = 5) {
    $bytes = random_bytes($length);
    return bin2hex($bytes);
}

function validationInput($field) {
    $arrayPaymentType = ['virtual_account', 'credit_card'];
    $arrayStatus = ['Pending', 'Paid', 'Failed', 'Expired'];
    if (empty($field['invoice_id'])) {
        http_response_code(442);
        return "invoice_id is required";
    }

    if (empty($field['item_name'])) {
        http_response_code(442);
        return "item_name is required";
    }

    if (empty($field['amount'])) {
        http_response_code(442);
        return "amount is required";
    }

    if (empty($field['payment_type'])) {
        http_response_code(442);
        return "payment_type is required";
    }

    if (empty($field['customer_name'])) {
        http_response_code(442);
        return "customer_name is required";
    }

    if (empty($field['merchant_id'])) {
        http_response_code(442);
        return "merchant_id is required";
    }

    if (!in_array($field["payment_type"], $arrayPaymentType)) {
        http_response_code(400);
        return "payment type is not valid";
    }

    if (!empty($_POST['status']) &&
        !in_array($field["status"], $arrayStatus)
    ) {
        http_response_code(400);
        return "status is not valid";
    }

    if (strlen($field["merchant_id"]) > 64) {
        http_response_code(400);
        return "merchant_id is not valid";
    }

    if (strlen($field["invoice_id"]) > 50) {
        http_response_code(400);
        return "invoice_id is not valid";
    }

    if (!is_numeric($field["amount"])) {
        http_response_code(400);
        return "amount is not number";
    }
}

function validationQuery($field) {
    if (empty($field['merchant_id']) || empty($field['references_id'])) {
        http_response_code(442);
        return "merchant_id / references_id is required";
    }

    if (strlen($field["merchant_id"]) > 64) {
        http_response_code(400);
        return "merchant_id is not valid";
    }

    if (!empty($field['references_id'])) {
        http_response_code(400);
        $isUuid = (bool) filter_var($field['references_id'], FILTER_VALIDATE_REGEXP, array(
            "options" => array(
                "regexp" => "/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i"
            )
        ));
    
        return !$isUuid ? "references_id is invalid": "";
    }
}