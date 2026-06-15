<?php 

namespace App;

use Exception;

class Cache
{    
    private static $basePath = "./storage/cache/";

    public function __construct(private String $group)
    {
        $path = self::$basePath . $this->group;

        if (!file_exists($path))
        {
            if (!mkdir($path, 0777, true))
            {
                throw new Exception("Fail To Create Folder");
            }
        }
    }

    public function put($key, $arg)
    {
        $path = self::$basePath . $this->group;

        if (!file_put_contents($path . "/" . $key, serialize($arg)))
        {
            throw new Exception("Fail To create File $key");
        }
    }

    public function get($key)
    {
        $path = self::$basePath . $this->group;
        
        if (file_exists($path . "/" . $key))
        {
            $data = file_get_contents($path . "/" . $key);
            return unserialize($data);
        }

        return null;
    }

    public function delete($key)
    {
        $path = self::$basePath . $this->group;;

        if (file_exists($path . "/" . $key))
        {
            if (!unlink($path . "/" . $key))
            {
                throw new Exception("Fail To Delete File $key");
            }
        }
    }

    public function flush()
    {
        $path = self::$basePath . $this->group;

        deleteDir($path);
    }
}