<?php

namespace PatroNet\SimpleTaskManager\Rest;

use PatroNet\Core\Request\ResponseBuilder;

class RoutingJsonService implements JsonService
{
    
    private $routes = [];
    
    public function addRoute(JsonService\_Route $oRoute)
    {
        $this->routes[] = $oRoute;
    }
    
    public function handleJsonQuery($path, $method, $data, Credentials $oCredentials)
    {
        foreach ($this->routes as $oRoute) {
            if (!is_null($oResponse = $oRoute->tryHandle($path, $method, $data, $oCredentials))) {
                return $oResponse;
            }
        }
        return
            (new ResponseBuilder())
            ->initJson(["message" => "No such service!"])
            ->setHttpStatus(404)
            ->build()
        ;
    }
    
}


namespace PatroNet\SimpleTaskManager\Rest\JsonService;

use PatroNet\Core\Request\Response;
use PatroNet\SimpleTaskManager\Rest\Credentials;

class _Util
{
    
    public static function normalizeMethodList($methodOrMethods)
    {
        if (is_null($methodOrMethods) || $methodOrMethods === "all") {
            return null;
        } else if (is_string($methodOrMethods)) {
            return [strtolower($methodOrMethods)];
        } else {
            return array_map("strtolower", $methodOrMethods);
        }
    }
    
    public static function checkMethod($normalizedMethods, $method)
    {
        if ($normalizedMethods == null) {
            return true;
        } else {
            return in_array(strtolower($method), $normalizedMethods);
        }
    }
    
}

interface _Route
{
    
    /**
     * @param string $path
     * @param string $method
     * @param mixed $data
     * @param mixed $oCredentials
     * @return Response|null
     */
    public function tryHandle($path, $method, $data, Credentials $oCredentials);
    
}

class _DefaultRoute implements _Route
{
    
    private $methods;
    
    /**
     * @param string|string[]|null $methodOrMethods
     */
    public function __construct($methodOrMethods = null)
    {
        $this->methods = _Util::normalizeMethodList($methodOrMethods);
    }
    
    public function tryHandle($path, $method, $data, Credentials $oCredentials)
    {
        if (!_Util::checkMethod($this->methods, $method)) {
            return null;
        }
        
        $callback = $this->callback;
        return $callback($path, $method, $data, $oCredentials);
    }
    
}

class _ExactPathRoute implements _Route
{
    
    private $methods;
    private $path;
    private $callback;
    
    /**
     * @param string|string[]|null $methodOrMethods
     * @param string $path
     * @param callable $callback
     */
    public function __construct($methodOrMethods, $path, callable $callback)
    {
        $this->methods = _Util::normalizeMethodList($methodOrMethods);
        $this->path = $path;
        $this->callback = $callback;
    }
    
    public function tryHandle($path, $method, $data, Credentials $oCredentials)
    {
        if (!_Util::checkMethod($this->methods, $method)) {
            return null;
        }
        
        if ($path == $this->path) {
            $callback = $this->callback;
            return $callback($method, $data, $oCredentials);
        } else {
            return null;
        }
    }
    
}

class _MatchingPathRoute implements _Route
{
    
    private $methods;
    private $pathRegex;
    private $callback;
    
    /**
     * @param string|string[]|null $methodOrMethods
     * @param string $pathRegex a PCRE pattern
     * @param callable $callback
     */
    public function __construct($methodOrMethods, $pathRegex, callable $callback)
    {
        $this->methods = _Util::normalizeMethodList($methodOrMethods);
        $this->pathRegex = $pathRegex;
        $this->callback = $callback;
    }
    
    public function tryHandle($path, $method, $data, Credentials $oCredentials)
    {
        if (!_Util::checkMethod($this->methods, $method)) {
            return null;
        }
        
        if (preg_match($this->pathRegex, $path, $match)) {
            $callback = $this->callback;
            return $callback($match, $method, $data, $oCredentials);
        } else {
            return null;
        }
    }
    
}