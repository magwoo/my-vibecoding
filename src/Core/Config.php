<?php

namespace App\Core;

class Config
{
    private static $config = null;
    
    private static function loadConfig()
    {
        if (self::$config === null) {
            self::$config = require SRC_DIR . '/config/config.php';
        }
    }
    
    public static function get($key)
    {
        self::loadConfig();
        
        $parts = explode(".", $key);
        
        $value = self::$config;
        foreach ($parts as $part) {
            if (!isset($value[$part])) {
                return null;
            }
            $value = $value[$part];
        }
        
        return $value;
    }
}
