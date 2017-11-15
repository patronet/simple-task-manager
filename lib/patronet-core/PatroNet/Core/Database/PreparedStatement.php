<?php

namespace PatroNet\Core\Database;


/**
 * Interface for prepared queries
 */
interface PreparedStatement
{
    
    const PARAM_AUTO = "auto";
    const PARAM_INT = "int";
    const PARAM_STR = "str";
    
    /**
     * Executes the query with the given values
     *
     * @param array $binds
     * @return \PatroNet\Core\Database\Result;
     */
    public function execute($binds = []);
    
    /**
     * Binds a value
     *
     * @param string $name
     * @param string $value
     * @param string $type
     * @return self
     */
    public function bind($name, $value, $type = self::PARAM_AUTO);
    
}
