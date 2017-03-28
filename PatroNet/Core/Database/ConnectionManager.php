<?php

namespace PatroNet\Core\Database;


/**
 * Database connection manager
 */
class ConnectionManager
{
    
    static protected $connectionClassMap = [
        "pdo" => "Pn\\Core\\Database\\ConnectionDriver\\Pdo\\Connection",
    ];
    
    static protected $platformClassMap = [
        "mysql" => "Pn\\Core\\Database\\PlatformDriver\\Mysql\\Platform",
    ];
    
    static protected $queryBuilderClassMap = [
        "mysql" => "Pn\\Core\\Database\\QueryBuilderDriver\\Mysql\\QueryBuilder",
    ];
    
    protected $connections = [];
    
    /**
     * Associates a connection class with the given name
     *
     * @param string $name
     * @param string $classname
     */
    static public function registerConnectionClass($name, $classname)
    {
        self::$connectionClassMap[$name] = $classname;
    }
    
    /**
     * Gets connection class associated with the given name
     *
     * @param string $name
     * @return string
     */
    static public function getConnectionClass($name)
    {
        if (!isset(self::$connectionClassMap[$name])) {
            throw new Exception("Connection driver not found: '{$name}");
        }
        return self::$connectionClassMap[$name];
    }
    
    /**
     * Associates a platform class with the given name
     *
     * @param string $name
     * @param string $classname
     */
    static public function registerPlatformClass($name, $classname)
    {
        self::$platformClassMap[$name] = $classname;
    }
    
    /**
     * Gets platform class associated with the given name
     *
     * @param string $name
     * @return string
     */
    static public function getPlatformClass($name)
    {
        if (!isset(self::$platformClassMap[$name])) {
            throw new Exception("Platform driver not found: '{$name}");
        }
        return self::$platformClassMap[$name];
    }
    
    /**
     * Associates a query builder class with the given name
     *
     * @param string $name
     * @param string $classname
     */
    static public function registerQueryBuilderClass($name, $classname)
    {
        self::$queryBuilderClassMap[$name] = $classname;
    }
    
    /**
     * Gets query builder class associated with the given name
     *
     * @param string $name
     * @return string
     */
    static public function getQueryBuilderClass($name)
    {
        if (!isset(self::$queryBuilderClassMap[$name])) {
            throw new Exception("Query builder driver not found: '{$name}");
        }
        return self::$queryBuilderClassMap[$name];
    }
    
    public function __construct()
    {
    }
    
    /**
     * Creates a new connection from the given URI
     *
     * @param string $uri
     * @param string $name
     */
    public function create($uri, $name = "default")
    {
        $connectionDriver = ConnectionUriParser::getConnectionDriverName($uri);
        $connectionClassname = self::getConnectionClass($connectionDriver);
        $oConnection = new $connectionClassname();
        $oConnection->init($uri);
        if ($name !== false) {
            $this->register($oConnection, $name);
        }
        return $oConnection;
    }
    
    /**
     * Registers a connection
     *
     * @param \PatroNet\Core\Database\Connection $oConnection
     * @param string $name
     */
    public function register(Connection $oConnection, $name = "default") 
    {
        $this->connections[$name] = $oConnection;
    }
    
    /**
     * Gets a connection
     *
     * @param string $name
     * @return \PatroNet\Core\Database\Connection
     */
    public function get($name="default")
    {
        if (isset($this->connections[$name])) {
            return $this->connections[$name];
        } else {
            return null;
        }
    }
    
}

