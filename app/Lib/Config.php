<?php
namespace App\Lib;

class Config
{
    use \App\Traits\Singleton;
    private static $config = [];

    private function __construct()
    {
    }

    /**
     * @param $keys
     * @param null $default
     * @return null|mixed
     */
    public function get($keys, $default = null)
    {
        $keys = explode('.', strtolower($keys));
        if (empty($keys)) {
            return null;
        }

        $file = array_shift($keys);

        if (empty(self::$config[$file])) {
            if (! is_file(CONFIG_PATH . $file . '.php')) {
                return null;
            }
            self::$config[$file] = include CONFIG_PATH . $file . '.php';
        }
        $config = self::$config[$file];

        while ($keys) {
            $key = array_shift($keys);
            if (! isset($config[$key])) {
                $config = $default;
                break;
            }
            $config = $config[$key];
        }

        return $config;
    }
}
