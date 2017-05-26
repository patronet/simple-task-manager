<?php

namespace PatroNet\SimpleTaskManager;


use PatroNet\Core\Application\AbstractApplication;
use PatroNet\Core\Database\ConnectionManager;
use PatroNet\SimpleTaskManager\Build\BuildRunner;

/**
 * Simple task manager application class
 */
class Application extends AbstractApplication
{
	
	// XXX
	private static $oConnection = null;

	// XXX
	private static $assetMap = null;
	
	// TODO
	public function hello()
	{
		echo "Hello!";
	}

	/**
	 * Shorthand for get default connection
	 *
	 * @param string $name
	 * @return \PatroNet\Core\Database\Connection
	 */
	static public function conn($name = "default")
	{
		// XXX / TODO
		if (!isset(self::$oConnection)) {
			$connectionString = "pdo.mysql://root:PNabc123@localhost/taskmanager?charset=utf8";
			self::$oConnection = (new ConnectionManager())->create($connectionString);
			self::$oConnection->open();
		}
		return self::$oConnection;
	}
	
}