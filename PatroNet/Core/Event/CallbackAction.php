<?php

namespace PatroNet\Core\Event;


/**
 * Action with a user defined callback
 */
class CallbackAction implements Action
{
    
    protected $callback;
    
    /**
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }
    
    /**
     * Executes the action in context of an event
     *
     * @param \PatroNet\Core\Event\Event $oEvent
     */
    public function trigger(Event $oEvent)
    {
        call_user_func($this->callback, $oEvent);
    }
    
    /**
     * Executes the action in context of an event
     *
     * @param \PatroNet\Core\Event\Event $oEvent
     */
    public function __invoke(Event $oEvent)
    {
        $this->trigger($oEvent);
    }
    
}

