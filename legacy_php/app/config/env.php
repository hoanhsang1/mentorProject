<?php
// app/config/env.php

class Env
{
    private static $env = [];
    
    public static function load($path = '.env')
    {
        if (!file_exists($path)) {
            return false;
        }
        
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Bỏ comment
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Parse key=value
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes nếu có
                if (preg_match('/^["\'].*["\']$/', $value)) {
                    $value = substr($value, 1, -1);
                }
                
                self::$env[$key] = $value;
            }
        }
        
        // Thêm vào biến môi trường
        foreach (self::$env as $key => $value) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
        
        return true;
    }
    
    public static function get($key, $default = null)
    {
        // Ưu tiên: getenv() → $_ENV → self::$env
        $value = getenv($key);
        
        if ($value === false) {
            $value = $_ENV[$key] ?? self::$env[$key] ?? $default;
        }
        
        return $value;
    }
}

// Tự động load .env file
Env::load(__DIR__ . '/../../.env');