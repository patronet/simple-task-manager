<?php

namespace PatroNet\Core\Common;


/**
 * Utility class for string operations
 */
class StringUtil
{
    
    const CASE_CAMEL = "camel";
    
    const CASE_PASCAL = "pascal";
    
    const CASE_LOWER = "lower";
    
    const CASE_UPPER = "upper";
    
    /**
     * Splits text with escape support
     *
     * @param string $delimiter
     * @param string $escaper
     * @param string $text
     * @return array
     */
    static public function splitEscaped($delimiter, $escaper, $text)
    {
        $d = preg_quote($delimiter, "~");
        $e = preg_quote($escaper, "~");
        $tokens = preg_split(
            '~' . $e . '(' . $e . '|' . $d . ')(*SKIP)(*FAIL)|' . $d . '~',
            $text
        );
        return preg_replace(
            array('~' . $e . $e . '~', '~' . $e . $d . '~'),
            array($escaper, $delimiter), // FIXME: escape backreferences?
            $tokens
        );
    }
    
    /**
     * Joins text items with escape support
     *
     * @param string $delimiter
     * @param string $escaper
     * @param array $items
     * @return string
     */
    static public function joinEscaped($delimiter, $escaper, $items)
    {
        return implode($delimiter, array_map(function($item) use ($delimiter, $escaper) {
            return str_replace(
                [$escaper, $delimiter],
                [$escaper . $escaper, $escaper . $delimiter],
                $item
            );
        }, $items));
    }
    
    /**
     * Changes case style of a string
     * 
     * @param string $input
     * @param string $separator Set null to auto
     * @return string
     */
    static public function transformTokenCase($input, $case = self::CASE_CAMEL, $separator = null)
    {
        $tokens = preg_split('#[^a-zA-Z]+|(?<=[a-z])(?=[A-Z])#', $input);
        $transformedTokens = [];
        foreach ($tokens as $i => $token) {
            $transformedToken = $token;
            if ($case == self::CASE_CAMEL) {
                $transformedToken = strtolower($token);
                if ($i > 0) {
                    $transformedToken = ucfirst($transformedToken);
                }
            } else if ($case == self::CASE_PASCAL) {
                $transformedToken = ucfirst(strtolower($transformedToken));
            } else if ($case == self::CASE_LOWER) {
                $transformedToken = strtolower($token);
            } else if ($case == self::CASE_UPPER) {
                $transformedToken = strtoupper($token);
            }
            $transformedTokens[] = $transformedToken;
        }
        if (is_null($separator)) {
            if ($case == self::CASE_LOWER) {
                $separator = "-";
            } else if ($case == self::CASE_UPPER) {
                $separator = "_";
            } else {
                $separator = "";
            }
        }
        return implode($separator, $transformedTokens);
    }
    
}

