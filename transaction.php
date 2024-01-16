<?php
require_once __DIR__.'/vendor/autoload.php';

use App\Controllers\TransactionController;

try {
    $controller = new TransactionController();

    if ($argc !== 3) {
        echo "Format Usage: php transaction.php {references_id} {status}\n";
        exit(1);
    }

    $referencesID = $argv[1];
    $status = $argv[2];

    $validate = validation($status, $referencesID);
    if (!empty($validate)) {
        echo $validate;
        exit;
    }

    $controller->update($referencesID, $status);

} catch (\PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

function validation($status, $referencesID) {
    $arrayStatus = ['Pending', 'Paid', 'Failed', 'Expired'];
    if (!in_array($status, $arrayStatus)) {
        return "Status is not valid";
    }

    if (!empty($referencesID)) {
        http_response_code(400);
        $isUuid = (bool) filter_var($referencesID, FILTER_VALIDATE_REGEXP, array(
            "options" => array(
                "regexp" => "/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i"
            )
        ));
    
        return !$isUuid ? "references_id is invalid": "";
    }

    return null;
}
