<?php

namespace PatroNet\Core\Common;


/**
 * General nullable implementation
 */
class NullObject implements
    \IteratorAggregate,
    \ArrayAccess,
    \Serializable,
    \Countable,
    Nullable
{
    
    /**
     * Returns with a fake iterator
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        return new \EmptyIterator();
    }
    
    /**
     * Fake array element setter
     */
    public function offsetSet($key, $value)
    {
    }
    
    /**
     * Fake array element remover
     */
    public function offsetUnset($key)
    {
    }
    
    /**
     * Fake array element getter
     *
     * @return self
     */
    public function offsetGet($key)
    {
        return new self();
    }
    
    /**
     * Fake array element checker
     *
     * Always returns with false.
     *
     * @return boolean
     */
    public function offsetExists($key)
    {
        return false;
    }
    
    // FIXME: ??
    /**
     * Fake serialize method
     *
     * @return string
     */
    public function serialize()
    {
        return serialize(null);
    }
    
    /**
     * Fake unserialize method
     */
    public function unserialize($data)
    {
    }
    
    /**
     * Fake counter method
     *
     * Always returns 0
     *
     * @return int
     */
    public function count()
    {
        return 0;
    }
    
    /**
     * Fake null checker method
     *
     * Always returns true
     *
     * @return boolean
     */
    public function isNull()
    {
        return true;
    }
    
    /**
     * Fake object member getter
     *
     * @return self
     */
    public function __get($key)
    {
        return new self();
    }
    
    /**
     * Fake object member setter
     */
    public function __set($key, $value)
    {
    }
    
    /**
     * Fake method call handler
     *
     * @param callback $name
     * @param array $arguments
     * @return self
     */
    public function __call($name, $arguments)
    {
        return new self();
    }
    
    /**
     * Fake invokation handler
     *
     * @param array $arguments
     * @return self
     */
    public function __invoke($arguments)
    {
        return new self();
    }
    
}
