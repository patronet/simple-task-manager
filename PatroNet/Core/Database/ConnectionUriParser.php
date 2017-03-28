<?php

namespace PatroNet\Core\Database;


/**
 * ConnectionURI parse utility
 */
class ConnectionUriParser
{
    
    const PATTERN_DELIMITER = '#';
    const PATTERN_DRIVER = '[^\\$&\\+,/:;=\\?@ <>\\#\\\\~\\.\\(\\)\\[\\]\\{\\}]+';
    const PATTERN_VALUE = '[^\\$&\\+,/:;=\\?@ <>\\#\\\\~]+';
    const PATTERN_ACCESS_DELIMITER = '@';
    const PATTERN_QUERY_DELIMITER = '&';
    const PATTERN_COMMENT_DELIMITER = '\\#';
    
    /**
     * Gets the connection driver name from the given URI
     *
     * @return string
     */
    static public function getConnectionDriverName($uri)
    {
        if (preg_match(
            self::PATTERN_DELIMITER . '^(' . self::PATTERN_DRIVER . ')([\\.\\(\\:]|$)' . self::PATTERN_DELIMITER,
            $uri,
            $match
        )) {
            return urldecode($match[1]);
        } else {
            return false;
        }
    }
    
    /**
     * Checks the syntax of the given URI
     *
     * @return boolean
     */
    static public function checkSyntax($uri)
    {
        $pattern =
            self::PATTERN_DELIMITER . '^' .
            self::PATTERN_DRIVER .
            '(\\.' . self::PATTERN_DRIVER . ')?' .
            '(\\(' . self::PATTERN_DRIVER . '\\))?' .
            '(' .
                '://' .
                '(' .
                    '(' .
                        self::PATTERN_VALUE .
                        '(:(' . self::PATTERN_VALUE . ')?)?' .
                    ')?' .
                    '(' . self::PATTERN_ACCESS_DELIMITER . self::PATTERN_VALUE . ')?' .
                    '(:\\d+)?' .
                ')?((' . self::PATTERN_VALUE . ')?/[^\\?]*)?' .
            ')?' .
            '(\\?(' .
                self::PATTERN_VALUE . '=' . self::PATTERN_VALUE .
                '(' . self::PATTERN_QUERY_DELIMITER . self::PATTERN_VALUE . '=' . self::PATTERN_VALUE . ')*' .
                ')?' .
            ')?(' . self::PATTERN_COMMENT_DELIMITER . '.*)?' .
            '$' . self::PATTERN_DELIMITER
        ;
        return !!preg_match($pattern, $uri);
    }
    
    /**
     * Parses the given URI
     *
     * @return array
     */
    static public function parse($uri)
    {
        if (!self::checkSyntax($uri)) {
            return false;
        }
        
        $result = [];
        
        $withoutComment = $uri;
        $commentPos = strpos($uri, "#");
        if ($commentPos !== false) {
            $withoutComment = substr($uri, 0, $commentPos);
            $result["comment"] = urldecode(substr($uri, $commentPos+1));
        }
        
        $withoutOptions = $withoutComment;
        $optionsPos = strpos($uri, "?");
        if ($optionsPos !== false) {
            $withoutOptions = substr($withoutComment, 0, $optionsPos);
            parse_str(substr($withoutComment, $optionsPos+1), $result["options"]);
        }
        
        $driverPart = $withoutOptions;
        $accessPos = strpos($withoutOptions, "://");
        if ($accessPos !== false) {
            $driverPart = substr($withoutOptions, 0, $accessPos);
            $accessPart = substr($withoutOptions, $accessPos+3);
            
            $pathPos = strpos($accessPart, "/");
            if ($pathPos === 0) {
                $serverPart = "";
                $pathPart = $accessPart;
            } elseif ($pathPos === false) {
                $serverPart = $accessPart;
                $pathPart = "";
            } else {
                $serverPart = substr($accessPart, 0, $pathPos);
                $pathPart = substr($accessPart, $pathPos+1);
            }
            
            if ($pathPart !== "") {
                $result["database"] = urldecode($pathPart);
            }
            
            if ($serverPart !== "") {
                $atPos = strpos($serverPart, "@");
                if ($atPos === 0) {
                    $authPart = "";
                    $addressPart = substr($serverPart, 1);
                } elseif ($atPos === false) {
                    if (preg_match(self::PATTERN_DELIMITER . '^' . self::PATTERN_VALUE . '(:\\d+)?$' . self::PATTERN_DELIMITER, $serverPart)) {
                        $authPart = "";
                        $addressPart = $serverPart;
                    } else {
                        $authPart = $serverPart;
                        $addressPart = "";
                    }
                } else {
                    $authPart = substr($serverPart, 0, $atPos);
                    $addressPart = substr($serverPart, $atPos+1);
                }
                
                if ($authPart !== "") {
                    $authTokens = explode(":", $authPart);
                    if (count($authTokens)>1) {
                        $result["password"] = urldecode($authTokens[1]);
                    }
                    $result["username"] = urldecode($authTokens[0]);
                }
                
                if ($addressPart !== "") {
                    $addressTokens = explode(":", $addressPart);
                    if (count($addressTokens)>1) {
                        $result["port"] = urldecode($addressTokens[1]);
                    }
                    $result["host"] = urldecode($addressTokens[0]);
                }
            }
        }
        
        $connectionAndPlatform = $driverPart;
        $sqlPos = strpos($driverPart, "(");
        if ($sqlPos !== false) {
            $connectionAndPlatform = substr($driverPart, 0, $sqlPos);
            $result["sql"] = urldecode(substr($driverPart, $sqlPos+1, -1));
        }
        
        $cpTokens = explode(".", $connectionAndPlatform);
        if (count($cpTokens)>1) {
            $result["platform"] = urldecode($cpTokens[1]);
        }
        
        $result["driver"] = urldecode($cpTokens[0]);
        
        return $result;
    }
    
}
