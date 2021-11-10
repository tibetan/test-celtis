<?php

class SettingManager
{
    private static $sm;

    private function __construct($settingsPath)
    {
        $settings = file_get_contents($settingsPath);
        $this->settingsObject = json_decode($settings, true);
    }

    public static function instance($settingsPath = '/../../config/setting.json')
    {
        if (is_null(self::$sm)) {
            $dir = __DIR__;
            self::$sm = new SettingManager($dir . $settingsPath);
        }
        return self::$sm;
    }

    public function get($property)
    {
        if (array_key_exists($property, $this->settingsObject)) {
            return $this->settingsObject[$property];
        }
        return null;
    }

    public function getChildById($property, $value) {
        $setting = $this->get($property);

        $child = NULL;
        foreach ($setting as $item) {
            if ($item->id === $value) {
                $child = $item;
            }
        }

        return $child;
    }
}
