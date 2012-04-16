<?php
/**
 * Конфиги системы
 */
class Config
{
    const EXT = '.php';

    /**
     * @var array конфиги
     */
    protected static $_memory_cache = array();

    /**
     * Загрузить конфиг
     *
     * @static
     * @param $configName
     * @return array|boolean
     * @throws Exception_Framework
     */
    public static function load($configName)
    {
        if (isset(self::$_memory_cache[$configName]) != FALSE)
            return self::$_memory_cache[$configName];

        $fileName = Application::findFile($configName . self::EXT, 'config');

        if ($fileName != FALSE)
        {
            $config = require_once $fileName;
            self::$_memory_cache[$configName] = $config;

            return $config;
        }

        throw new Exception_Framework('Config ' . $configName . ' not found');

        return FALSE;
    }
}
