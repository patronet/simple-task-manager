<?php

namespace PatroNet\Core\Event;


/**
 * Mediator between events and handler actions
 */
class ActionEventMediator implements EventMediator
{
    
    protected $actions = [];
    
    /**
     * Registers an action for an event type
     *
     * @param string $name
     * @param \PatroNet\Core\Event\Action $oAction
     */
    public function registerAction($name, Action $oAction)
    {
        if (!array_key_exists($name, $this->actions)) {
            $this->actions[$name] = [];
        }
        $this->actions[$name][] = $oAction;
    }
    
    /**
     * Executes actions associated to the type of the given event
     *
     * @param \PatroNet\Core\Event\Event $oEvent
     */
    public function fireEvent(Event $oEvent)
    {
        $eventName = $oEvent->getName();
        if (array_key_exists($eventName, $this->actions)) {
            foreach ($this->actions[$eventName] as $oAction) {
                $oAction->trigger($oEvent);
                if ($oEvent->isCancelled()) {
                    break;
                }
            }
        }
    }
    
}

