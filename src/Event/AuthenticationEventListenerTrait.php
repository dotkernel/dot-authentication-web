<?php
/**
 * @copyright: DotKernel
 * @library: dot-authentication-web
 * @author: n3vrax
 * Date: 2/20/2017
 * Time: 11:11 PM
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web\Event;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateTrait;

/**
 * Class AuthenticationEventListenerTrait
 * @package Dot\Authentication\Web\Event
 */
trait AuthenticationEventListenerTrait
{
    use ListenerAggregateTrait;

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            AuthenticationEvent::EVENT_AUTHENTICATION_BEFORE_RENDER,
            [$this, 'onAuthenticationBeforeRender'],
            $priority
        );
        $this->listeners[] = $events->attach(
            AuthenticationEvent::EVENT_BEFORE_AUTHENTICATION,
            [$this, 'onBeforeAuthentication'],
            $priority
        );
        $this->listeners[] = $events->attach(
            AuthenticationEvent::EVENT_AFTER_AUTHENTICATION,
            [$this, 'onAfterAuthentication'],
            $priority
        );
        $this->listeners[] = $events->attach(
            AuthenticationEvent::EVENT_AUTHENTICATION_ERROR,
            [$this, 'onAuthenticationError'],
            $priority
        );
        $this->listeners[] = $events->attach(
            AuthenticationEvent::EVENT_BEFORE_LOGOUT,
            [$this, 'onBeforeLogout'],
            $priority
        );
        $this->listeners[] = $events->attach(
            AuthenticationEvent::EVENT_AFTER_LOGOUT,
            [$this, 'onAfterLogout'],
            $priority
        );
        $this->listeners[] = $events->attach(
            AuthenticationEvent::EVENT_UNAUTHORIZED,
            [$this, 'onUnauthorized'],
            $priority
        );
    }

    public function onBeforeAuthentication(AuthenticationEvent $e)
    {
        // no-op
    }

    public function onAfterAuthentication(AuthenticationEvent $e)
    {
        // no-op
    }

    public function onAuthenticationError(AuthenticationEvent $e)
    {
        //no-op
    }

    public function onAuthenticationBeforeRender(AuthenticationEvent $e)
    {
        //no-op
    }

    public function onBeforeLogout(AuthenticationEvent $e)
    {
        // no-op
    }

    public function onAfterLogout(AuthenticationEvent $e)
    {
        // no-op
    }

    public function onUnauthorized(AuthenticationEvent $e)
    {
        //no-op
    }
}
