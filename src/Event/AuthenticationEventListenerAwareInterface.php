<?php
/**
 * @copyright: DotKernel
 * @library: dot-authentication-web
 * @author: n3vrax
 * Date: 1/30/2017
 * Time: 8:57 PM
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web\Event;

/**
 * Interface AuthenticationEventListenerAwareInterface
 * @package Dot\Authentication\Web\Event
 */
interface AuthenticationEventListenerAwareInterface
{
    /**
     * @param AuthenticationEventListenerInterface $listener
     * @param int $priority
     * @param string $eventName
     */
    public function attachListener(
        AuthenticationEventListenerInterface $listener,
        $priority = 1,
        string $eventName = ''
    );

    /**
     * @param AuthenticationEventListenerInterface $listener
     */
    public function detachListener(AuthenticationEventListenerInterface $listener);

    /**
     * Detach and clears all listeners
     */
    public function clearListeners();
}
