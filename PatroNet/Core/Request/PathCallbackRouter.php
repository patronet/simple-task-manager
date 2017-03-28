<?php

namespace PatroNet\Core\Request;


/**
 * Common router implementation based on request path pattern and closure
 */
class PathCallbackRouter implements Router
{
    
    const PATTERN_DELIMITER = '#';
    
    const DEFAULT_PATTERN = '[^/]+';
    
    protected $pathPattern;
    
    protected $callback;
    
    public function __construct($pathPattern, callable $callback)
    {
        $this->pathPattern = $pathPattern;
        $this->callback = $callback;
    }
    
    /**
     * Tries to handle the given request
     *
     * @param \PatroNet\Core\Request\Request
     * @return\PatroNet\Core\Request\Response
     */
    public function handleRequest(Request $oRequest)
    {
        $fullRegexPattern = self::PATTERN_DELIMITER . '^';
        preg_match_all("@\\{(\\w+|(\\w+\\s*)?(#([^#]*)#)?)\\}@", $this->pathPattern, $matches, PREG_OFFSET_CAPTURE); // FIXME: escape #
        $position = 0;
        foreach ($matches[0] as $i=>$match) {
            $token = $match[0];
            $offset = $match[1];
            $subPattern = self::DEFAULT_PATTERN;
            if (!empty($matches[4][$i])) {
                $subPattern = $matches[4][$i][0];
                $subPattern = preg_replace('#((^|[^\\\\])(\\\\\\\\)*)\\(#', '$2(?:', $subPattern);
            }
            $before = substr($this->pathPattern, $position, $offset - $position);
            $fullRegexPattern .= preg_quote($before, self::PATTERN_DELIMITER) . "(" . $subPattern . ")";
            $position = $offset + strlen($token);
        }
        $after = substr($this->pathPattern, $position);
        $fullRegexPattern .= preg_quote($after, self::PATTERN_DELIMITER) . '$' . self::PATTERN_DELIMITER;
        if (preg_match($fullRegexPattern, $oRequest->getPath(), $match)) {
            $parameters = $match;
            array_shift($parameters);
            return call_user_func_array($this->callback, $parameters);
        } else {
            return (new ResponseBuilder())->setHttpStatus(404)->setApplicableStatus(Response::APPLICABLE_CONTINUE)->build();
        }
    }
    
}
