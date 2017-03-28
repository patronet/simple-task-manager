<?php

namespace PatroNet\Core\Common;


/**
 * Utility class for string operations
 */
class StringUtil
{
    
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
    
}

