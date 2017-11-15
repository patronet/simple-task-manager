<?php

namespace PatroNet\Core\Common;


/**
 * Utility class for easier access tree structure arrays
 *
 * $varname parameter supports dot separated names with escape support:
 * foo => $arr["foo"]
 * foo.bar => $arr["foo"]["bar"]
 * foo\.bar => $arr["foo.bar"]
 * foo\\.bar => $arr["foo\\"]["bar"]
 */
class TreeUtil
{
    
    /**
     * Gets an element from a tree
     *
     * @param array|mixed $arr
     * @param string $varname
     * @return mixed
     */
    static public function getTreeElement($arr, $varname = "")
    {
        list($name, $subname) = self::cutTreeVarname($varname);
        if ($name == '') {
            return $arr;
        } elseif (!is_array($arr) || !array_key_exists($name, $arr)) {
            return NULL;
        } elseif ($subname == '') {
            return $arr[$name];
        } else {
            return self::getTreeElement($arr[$name], $subname);
        }
    }
    
    /**
     * Sets an element in a tree
     *
     * @param array|mixed $arr
     * @param string $varname
     * @param mixed $value
     */
    static public function setTreeElement(&$arr, $varname, $value)
    {
        list($name, $subname) = self::cutTreeVarname($varname);
        if ($name == '') {
            $arr = $value;
        } elseif (!is_array($arr) && !is_null($arr)) {
            // nothing to do
        } elseif ($subname == '') {
            $arr[$name] = $value;
        } else {
            return self::setTreeElement($arr[$name], $subname, $value);
        }
    }
    
    /**
     * Removes an element from a tree
     *
     * @param array|mixed $arr
     * @param string $varname
     */
    static public function unsetTreeElement(&$arr, $varname)
    {
        list($name, $subname) = self::cutTreeVarname($varname);
        if ($name == '') {
            $arr = null;
        } elseif (!is_array($arr) || !array_key_exists($name, $arr)) {
            // nothing to do
        } elseif ($subname == '') {
            unset($arr[$name]);
        } else {
            return self::unsetTreeElement($arr[$name], $subname);
        }
    }
    
    /**
     * Escapes a string for safely using as a component in a varname
     *
     * @param string $varnameComponent
     */
    static public function escapeComponent($varnameComponent)
    {
        return str_replace(['\\', '.'], ['\\\\', '\\.'], $varnameComponent);
    }
    
    static protected function cutTreeVarname($varname)
    {
        if ($varname == '') {
            return ['', ''];
        }
        preg_match('/^([^\\.\\\\]|\\\\\\\\|\\\\\\.)+/', $varname, $match);
        $rawname = $match[0];
        $name = preg_replace('/\\\\(.)/', '$1', $rawname);
        if ($rawname == $varname) {
            return [$name, ''];
        }
        $subname = substr($varname, strlen($rawname) + 1);
        return [$name, $subname];
    }
    
}
