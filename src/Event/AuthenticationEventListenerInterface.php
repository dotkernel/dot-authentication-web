<?php
/**
 * @copyright: DotKernel
 * @library: dot-authentication-web
 * @author: n3vrax
 * Date: 1/30/2017
 * Time: 8:31 PM
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web\Event;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * Interface AuthenticationEventListenerInterface
 * @package Dot\Authentication\Web\Event
 */
interface AuthenticationEventListenerInterface extends ListenerAggregateInterface
{
    /**
     * @param EventManagerInterface $events
     * @param int $priority
     * @param string $eventName
     */
    public function attach(EventManagerInterface $events, $priority = 1, string $eventName = '');

    /**
     * @param AuthenticationEvent $e
     */
    public function onAuthenticate(AuthenticationEvent $e);

    /**
     * @param AuthenticationEvent $e
     */
    public function onLogout(AuthenticationEvent $e);

    /**
     * @param AuthenticationEvent $e
     */
    public function onUnauthorized(AuthenticationEvent $e);
}
