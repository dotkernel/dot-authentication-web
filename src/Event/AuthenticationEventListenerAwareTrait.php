<?php
/**
 * @copyright: DotKernel
 * @library: dot-authentication-web
 * @author: n3vrax
 * Date: 1/30/2017
 * Time: 8:59 PM
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web\Event;

use Zend\EventManager\EventManagerAwareTrait;

/**
 * Class AuthenticationEventListenerAwareTrait
 * @package Dot\Authentication\Web\Event
 */
trait AuthenticationEventListenerAwareTrait
{
    use EventManagerAwareTrait;

    /** @var AuthenticationEventListenerInterface[] */
    protected $listeners = [];

    /**
     * @param AuthenticationEventListenerInterface $listener
     * @param int $priority
     * @param string $eventName
     */
    public function attachListener(
        AuthenticationEventListenerInterface $listener,
        $priority = 1,
        string $eventName = ''
    ) {
        $listener->attach($this->getEventManager(), $priority, $eventName);
        $this->listeners[] = $listener;
    }

    /**
     * @param AuthenticationEventListenerInterface $listener
     */
    public function detachListener(AuthenticationEventListenerInterface $listener)
    {
        $listener->detach($this->getEventManager());
        $idx = 0;
        foreach ($this->listeners as $l) {
            if ($l === $listener) {
                break;
            }
            $idx++;
        }
        unset($this->listeners[$idx]);
    }

    /**
     * Clears all listeners
     */
    public function clearListeners()
    {
        foreach ($this->listeners as $listener) {
            $listener->detach($this->getEventManager());
        }
        $this->listeners = [];
    }
}
