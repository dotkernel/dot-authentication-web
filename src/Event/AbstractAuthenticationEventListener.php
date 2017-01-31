<?php
/**
 * @copyright: DotKernel
 * @library: dot-authentication-web
 * @author: n3vrax
 * Date: 1/30/2017
 * Time: 8:33 PM
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web\Event;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

abstract class AbstractAuthenticationEventListener extends AbstractListenerAggregate implements
    AuthenticationEventListenerInterface
{
    /**
     * @param EventManagerInterface $events
     * @param int $priority
     * @param string $name
     */
    public function attach(EventManagerInterface $events, $priority = 1, string $name = '')
    {
        if (empty($name)) {
            return;
        }

        switch ($name) {
            case AuthenticationEvent::EVENT_AUTHENTICATION_AUTHENTICATE:
                $this->listeners[] = $events->attach(
                    $name,
                    [$this, 'onAuthenticate'],
                    $priority
                );
                break;

            case AuthenticationEvent::EVENT_AUTHENTICATION_LOGOUT:
                $this->listeners[] = $events->attach(
                    $name,
                    [$this, 'onLogout'],
                    $priority
                );
                break;

            case AuthenticationEvent::EVENT_AUTHENTICATION_UNAUTHORIZED:
                $this->listeners[] = $events->attach(
                    $name,
                    [$this, 'onUnauthorized'],
                    $priority
                );
                break;

            default:
                return;
        }
    }

    public function onAuthenticate(AuthenticationEvent $e)
    {
        // NOOP: defined by implementors
    }

    public function onLogout(AuthenticationEvent $e)
    {
        // NOOP: defined by implementors
    }

    public function onUnauthorized(AuthenticationEvent $e)
    {
        // NOOP: defined by implementors
    }


}
