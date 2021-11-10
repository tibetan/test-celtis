<?php
require_once('ExceptionHelper.php');
require_once('SettingManager.php');

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\ErrorHandler;

$uniqUID = uniqid(time(), true);

class LogHelper
{
    private static $log = [];
    private static $defaultName = '';

    public static function init($name, $defaultName = false)
    {
        if ($defaultName || empty(self::$defaultName)) {
            self::$defaultName = $name;
        }

        if (key_exists($name, self::$log)) {
            return self::$log[$name];
        }

        self::$log[$name] = new Logger($name);

        $formatter = new LineFormatter(
            "[%datetime%] %level_name%.%channel%: %message% %context% %extra%\n", // Format of message in log, default [%datetime%] %channel%.%level_name%: %message% %context% %extra%\n
            null, // Datetime format
            true, // allowInlineLineBreaks option, default false
            true  // ignoreEmptyContextAndExtra option, default false
        );

        $settings = SettingManager::instance()->get('log');

        if (array_key_exists($name, $settings['logger'])) {
            $log = $settings['logger'][$name];
            $maxFiles = $log['maxFiles'];
            $level = $log['level'];
        } else {
            $maxFiles = $settings['maxFiles'];
            $level = $settings['level'];
        }

        if ($level === 'INFO') {
            $level = Logger::INFO;
        } else if ($level === 'NOTICE') {
            $level = Logger::NOTICE;
        } else if ($level === 'WARNING') {
            $level = Logger::WARNING;
        } else if ($level === 'ERROR') {
            $level = Logger::ERROR;
        } else {
            $level = Logger::DEBUG;
        }

        $handler = new RotatingFileHandler($settings['path'] . DIRECTORY_SEPARATOR . $name . '.log', $maxFiles, $level);
        $handler->setFormatter($formatter);
        self::$log[$name]->pushHandler($handler);
        self::$log[$name]->pushProcessor(function ($entry) {
            global $uniqUID;
            $entry['extra']['uid'] = $uniqUID;
            return $entry;
        });

        ErrorHandler::register(self::$log[$name]);

        return self::$log[$name];
    }

    public static function log($name = null)
    {
        if (empty($name)) {
            $name = self::$defaultName;
        }

        return self::$log[$name];
    }
}
