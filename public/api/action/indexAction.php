<?php

require_once 'service/IndexService.php';

function fetchImagesInfo($date)
{
    $indexService = new IndexService();
    $result = $indexService->fetchImagesInfo($date);

    return $result;
}
