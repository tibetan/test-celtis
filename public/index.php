<?php

require_once('../src/config/include.php');
require_once('shared/common.php');

$log = LogHelper::init('index');
$log->addNotice('start');

$start_time = microtime(true);

echo processTemplate('template/index.php');

$finish_time = microtime(true);

$log->addNotice('finish', [
    'start_time' => DateHelper::dateUnixToString($start_time),
    'finish_time' => DateHelper::dateUnixToString($finish_time),
    'execution_time' => ($finish_time - $start_time)
]);
