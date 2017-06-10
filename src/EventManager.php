<?php

namespace Event;

use Event\EventManager\EventManagerInterface;
use Event\ListenerQueue;

class EventManager implements EventManagerInterface
{
    private $events = [];
    /**
     * Attaches a listener to an event
     *
     * @param string $event the event to attach too
     * @param callable $callback a callable function
     * @param int $priority the priority at which the $callback executed
     * @return bool true on success false on failure
     */
    public function attach($event, $callback, $priority = 0)
    {
        if(!array_key_exists($event, $this->events))
            $this->events[$event] = new ListenerQueue;

        $this->events[$event]->listen($callback, $priority);
    }

    /**
     * Detaches a listener from an event
     *
     * @param string $event the event to attach too
     * @param callable $callback a callable function
     * @return bool true on success false on failure
     */
    public function detach($event, $callback)
    {
        $flag = false;

        if(array_key_exists($event, $this->events))
        {
            $flag = $this->events[$event]->extract($callback);
        }

        return $flag;
    }

    /**
     * Clear all listeners for a given event
     *
     * @param  string $event
     * @return void
     */
    public function clearListeners($event)
    {
        $this->events[$event]->clear();
    }

    /**
     * Trigger an event
     *
     * Can accept an EventInterface or will create one if not passed
     *
     * @param  string|EventInterface $event
     * @param  object|string $target
     * @param  array|object $argv
     * @return mixed
     */
    public function trigger($event, $target = null, $argv = [])
    {
        $event = $this->events[$event];

        while($event->valid())
        {
            call_user_func_array(
                $event->top(), 
                $argv
            );
            $event->extract();
        }
    }
}