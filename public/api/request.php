<?php

require_once('../../src/config/include.php');
require_once('shared/common.php');

$log = LogHelper::init('api-request');
$log->addNotice('start');

$postData = file_get_contents('php://input');
$data = json_decode($postData, true);

if (empty($data)) {
    echo json_encode(OperationResult::makeBusinessFail(ERR_REQUEST_WRONG_PARAMETER, 'empty data'));
    exit;
}

$log->addNotice('parameters', array('data' => $data));

$action = isset($data['action']) ? $data['action'] : '';
$payload = isset($data['payload']) ? $data['payload'] : [];

if (!jsonHeader()) {
    exit;
}

LogHelper::init("api-${action}", TRUE);

switch ($action) {
    case 'fetchImagesInfo':
        $chosenDate = $payload['chosenDate'];

        require_once('action/indexAction.php');
        $res = fetchImagesInfo($chosenDate);
        break;
    default:
        $res = OperationResult::makeBusinessFail(ERR_UNKNOWN_ERROR);
        break;
}

echo $res->json();
