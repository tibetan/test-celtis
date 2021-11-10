<?php

require_once('shared/DataBaseManager.php');
require_once('service/IndexManager.php');

class IndexService
{
    private $db = [];

    public function __construct()
    {
        $this->db['celtisLocalhost'] = new DataBaseManager("celtisLocalhost");
    }

    public function getDB($key = 'celtisLocalhost')
    {
        return $this->db[$key];
    }

    public function fetchImagesInfo($date)
    {
        $localLog = LogHelper::log()->withName('IndexService::fetchImagesInfo()');
        $localLog->addInfo('start: parameters', ['date' => $date]);

        $indexManager = new IndexManager($this->getDB());
        $result = $indexManager->fetchImagesInfoByDate($date);

        return $result;
    }

    public function handleImagesFromUrls()
    {
        $localLog = LogHelper::log()->withName('IndexService::handleImagesFromUrls()');
        $localLog->addInfo('start');

        $indexManager = new IndexManager($this->getDB());
        $urlsRes = $indexManager->fetchUrls();

        if ($urlsRes->type !== OperationResultType::ok) {
            return $urlsRes;
        }

        $urls = [];
        foreach ($urlsRes->data as $val) {
            $urls[] = ['id' => $val['id'], 'url' => $val['url']];
        }

        $countImagesRes = $indexManager->countImages($urls);

        if ($countImagesRes->type !== OperationResultType::ok) {
            return $countImagesRes;
        }

        $countImagesByDate = [];
        $today = date('Y-m-d');
        foreach ($countImagesRes->data as $val) {
            $countImagesByDate[] = [
                'urlId' => $val['urlId'],
                'countImages' => $val['countImages'],
                'date' => $today,
            ];
        }

        $result = $indexManager->insertImagesInfoToDB($countImagesByDate);
        return $result;
    }

}
