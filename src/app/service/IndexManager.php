<?php

class IndexManager
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function fetchImagesInfoByDate($date)
    {
        $localLog = LogHelper::log()->withName('IndexManager::fetchImagesInfoByDate()');
        $localLog->addInfo('start: parameters', ['date' => $date]);

        $stmt = 'SELECT urls.url, img.count, img.date FROM urls_images img' .
            ' INNER JOIN urls ON img.url_id = urls.id' .
            ' WHERE img.date = :date';
        $imagesInfo = $this->db->select($stmt, ['date' => $date]);

        if ($imagesInfo === FALSE) {
            return OperationResult::makeSystemFail(ERR_DB_CONNECTION_FAILED, $stmt);
        }

        return OperationResult::makeOk($imagesInfo);
    }

    public function fetchUrls()
    {
        $localLog = LogHelper::log()->withName('IndexManager::fetchUrls()');
        $localLog->addInfo('start');

        $stmt = 'SELECT * FROM urls';
        $urls = $this->db->select($stmt);

        if ($urls === FALSE) {
            return OperationResult::makeSystemFail(ERR_DB_CONNECTION_FAILED, $stmt);
        }

        return OperationResult::makeOk($urls);
    }

    public function countImages($urls)
    {
        $localLog = LogHelper::log()->withName('IndexManager::countImages()');
        $localLog->addInfo('start: parameters', ['urls' => $urls]);

        $imagesInUrls = [];
        foreach ($urls as $url) {
            $options = [
                'verbose' => 1,
                'autoreferer' => false,
                'referer' => $url['url'],
            ];
            list($response, $error, $status, $request) = fetchCurl('get', $url['url'], $options);

            if ($error !== FALSE) {
                return OperationResult::makeSystemFail(ERR_CURL, ['request' => $request, 'response' => $response, 'error' => $error, 'status' => $status]);
            }

            $countImages = substr_count($response, '<img');
            $imagesInUrls[] = ['urlId' => $url['id'], 'url' => $url['url'], 'countImages' => $countImages];
        }

        return OperationResult::makeOk($imagesInUrls);
    }

    public function insertImagesInfoToDB($countImagesByDate)
    {
        $localLog = LogHelper::log()->withName('IndexManager::insertImagesInfo()');
        $localLog->addInfo('start: parameters', ['countImagesByDate' => $countImagesByDate]);

        $stmt = 'INSERT INTO urls_images (url_id, count, date) VALUES (:urlId, :countImages, :date)';
        if ($this->db->insert($stmt, $countImagesByDate) === FALSE) {
            return OperationResult::makeSystemFail(ERR_DB_CONNECTION_FAILED, $stmt);
        }

        return OperationResult::makeOk();
    }
}
