<?php

namespace PatroNet\Core\Event;


/**
 * Interface for event managers
 */
interface EventMediator
{
    
    /**
     * Executes actions associated to the type of the given event
     *
     * @param \PatroNet\Core\Event\Event $oEvent
     */
    public function fireEvent(Event $oEvent);
    
}

