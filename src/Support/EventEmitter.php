<?php

namespace Gargantua\Support;

class EventEmitter {

  private $listeners = [];

  public function on($event, callable $listener) {
    if (!isset($this->listeners[$event])) {
      $this->listeners[$event] = [];
    }

    $this->listeners[$event][] = $listener;
  }

  public function once($event, callable $listener)
  {
      $onceListener = function () use (&$onceListener, $event, $listener) {
        $this->removeListener($event, $onceListener);
        $listener(...func_get_args());
      };

      $this->on($event, $onceListener);
  }

  public function removeListener($event, callable $listener) {
    if (isset($this->listeners[$event])) {
      $index = array_search($listener, $this->listeners[$event], true);
      if (false !== $index) {
        unset($this->listeners[$event][$index]);
      }
    }
  }

  public function emit($event, ...$arguments) {
    foreach ($this->listeners($event) as $listener) {
      call_user_func_array($listener, $arguments);
    }
  }
}