<?php

define("OK", 0);
define("ERR_UNKNOWN_ERROR", 10);
define("ERR_REQUEST_WRONG_PARAMETER", 11);
define("ERR_DB_CONNECTION_FAILED", 12);
define("ERR_CURL", 13);

class Message
{
    static public $messages = [
        OK => [
            'log' => 'OK',
            'code' => 'OK',
            'BG' => 'OK',
        ],
        ERR_UNKNOWN_ERROR => [
            'log' => 'Неизвестна грешка',
            'code' => 'ERR_UNKNOWN_ERROR',
            'BG' => 'Неизвестна грешка',
        ],
        ERR_REQUEST_WRONG_PARAMETER => [
            'log' => 'Грешка в параметрите на запроса',
            'code' => 'ERR_REQUEST_WRONG_PARAMETER',
            'BG' => 'Грешка при обработка на данни',
        ],
        ERR_DB_CONNECTION_FAILED => [
            'log' => 'Грешка в базата данни',
            'code' => 'ERR_DB_CONNECTION_FAILED',
            'BG' => 'Грешка в базата данни',
        ],
        ERR_CURL => [
            'log' => 'Мрежова грешка при извикване на CURL',
            'code' => 'ERR_CURL',
            'BG' => 'Мрежова грешка. Опитайте отново по-късно',
        ],
    ];
}
