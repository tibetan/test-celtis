<?php

require_once('SettingManager.php');
require_once('DateHelper.php');
require_once('OperationResult.php');
require_once('utils.php');

class ExceptionHelper
{
    public static function handler($ex)
    {
        $localLog = LogHelper::log()->withName('ExceptionHelper::handler()');
        $localLog->addDebug('start');

        $error_msg = "{$ex->getMessage()} in {$ex->getFile()} @ {$ex->getLine()}";
        $error_trace = "{$ex->getTraceAsString()}";
        $localLog->addError($error_msg);
        $localLog->addError($error_trace);
        $localLog->addDebug('finish');
        exit(1);
    }

    public static function init()
    {
        set_exception_handler('ExceptionHelper::handler');
    }

}

ExceptionHelper::init();
