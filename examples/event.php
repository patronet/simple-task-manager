<?php

namespace PatroNet\Core\Event;

$oActionEventMediator = new ActionEventMediator();

$oActionEventMediator->registerAction("teszt", new CallbackAction(function(Event $oEvent){
    echo "HELLO<br />\n";
}));

$oActionEventMediator->registerAction("teszt", new CallbackAction(function(Event $oEvent){
    echo "BELLO<br />\n";
    $oEvent->cancel();
}));

$oActionEventMediator->registerAction("teszt", new CallbackAction(function(Event $oEvent){
    echo "ASDF<br />\n";
}));

$oActionEventMediator->fireEvent(new Event("teszt"));
