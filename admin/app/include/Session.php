<?php
// 1. Set the duration (e.g., 30 days)
$duration = 30 * 24 * 60 * 60; 

// 2. Configure the session cookie
session_set_cookie_params([
    'lifetime' => $duration,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => true,     // Send cookie only over HTTPS
    'httponly' => true,   // Prevent JavaScript access (security best practice)
    'samesite' => 'Lax'
]);

$timeout = 2592000; // 30 days in seconds
ini_set('session.gc_maxlifetime', $timeout);
ini_set('session.cookie_lifetime', $timeout);

session_start();


class Session
{
    public static function write(String $key, $val)
    {
        $_SESSION[$key] = $val;
    }
    
    public static function read(Mixed $key = null)
    {
        if ($key)
        {
            return isset($_SESSION[$key]) ? $_SESSION[$key] : false;
        }
        else
        {
            return $_SESSION;
        }
    }
    
    public static function has(String $key)
    {
        return isset($_SESSION[$key]) ? true : false;
    }
    
    public static function hasFlash(String $key)
    {
        return self::has("flash." . $key);
    }
    
    public static function writeFlash(String $key, $val)
    {
        self::write("flash." . $key, $val);
    }
    
    public static function readFlash(String $key)
    {
        $key = "flash." . $key;
        
        $msg = self::read($key);
        
        unset($_SESSION[$key]);
        
        return $msg;
    }
}
