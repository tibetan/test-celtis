<?php
require_once('../../config/include.php');
require_once('shared/common.php');
require_once('service/IndexService.php');

$log = LogHelper::init('cron');
$log->addNotice('start');

$start_time = microtime(true);

$indexSrv = new IndexService();

$res = $indexSrv->handleImagesFromUrls();
if ($res->type !== OperationResultType::ok) {
    OperationResult::makeBusinessFail($res->data['code'], $res->data, true);
    return;
}

$finish_time = microtime(true);

$log->addNotice('finish', [
    'start_time' => DateHelper::dateUnixToString($start_time),
    'finish_time' => DateHelper::dateUnixToString($finish_time),
    'execution_time' => ($finish_time - $start_time)
]);