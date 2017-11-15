<?php

namespace PatroNet\SimpleTaskManager;


use PatroNet\Core\Application\AbstractApplication;
use PatroNet\Core\Database\ConnectionManager;

/**
 * Simple task manager application class
 */
class Application extends AbstractApplication
{
	
	private static $oConnection = null;
    
	private static $configs = [];
	
	/**
	 * Gets root directory
	 *
	 * @return string
	 */
	static public function root()
	{
	    return preg_replace('@(/[^/]+){4}$@', '', __DIR__);
	}
	
	/**
	 * Gets default connection
	 *
	 * @param string $name
	 * @return \PatroNet\Core\Database\Connection
	 */
	static public function conn($name = "default")
	{
		if (is_null(self::$oConnection)) {
		    $connectionString = self::config("database")["connectionString"];
			self::$oConnection = (new ConnectionManager())->create($connectionString);
			self::$oConnection->open();
		}
		return self::$oConnection;
	}
	
	/**
	 * Gets some config data
	 *
	 * @param string $name
	 * @throws \InvalidArgumentException when $name contains illegal characters
	 * @return array
	 */
	static public function config($name)
	{
	    if (!preg_match('@^[a-zA-Z0-9\\-\\._]+$@', $name)) {
	        throw new \InvalidArgumentException("Invalid config name: '{$name}'");
	    }
	    
	    if (!array_key_exists($name, self::$configs)) {
	        $configDirs = self::getConfigDirs();
	        
	        $config = [];
	        foreach ($configDirs as $configDir) {
	            $configFile = "{$configDir}/{$name}.yaml";
	            if (file_exists($configFile)) {
	                $config = array_merge($config, Util::yaml($configFile));
    	        }
	        }
	        
	        self::$configs[$name] = $config;
	    }
	    
	    return self::$configs[$name];
	}
	
	static private function getConfigDirs()
	{
	    return [
	        self::root() . "/config-default",
	        self::root() . "/config",
        ];
	}
	
}