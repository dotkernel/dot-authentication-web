<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-authentication-web
 * @author: n3vrax
 * Date: 9/30/2016
 * Time: 6:34 PM
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web;

use Dot\Authentication\AuthenticationInterface;
use Dot\Authentication\Web\Event\AuthenticationEvent;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class AuthenticationEventTrait
 * @package Dot\Authentication\Web
 */
trait AuthenticationEventTrait
{
    /**
     * @param AuthenticationInterface $authentication
     * @param mixed $error
     * @param string $name
     * @param array $eventParams
     * @param ServerRequestInterface $request
     * @return AuthenticationEvent
     */
    protected function createAuthenticationEventWithError(
        AuthenticationInterface $authentication,
        mixed $error,
        string $name = AuthenticationEvent::EVENT_AUTHENTICATION_UNAUTHORIZED,
        array $eventParams = [],
        ServerRequestInterface $request = null
    ): AuthenticationEvent {

        $event = $this->createAuthenticationEvent($authentication, $name, $eventParams, $request);
        $event->setError($error);

        return $event;
    }

    /**
     * @param AuthenticationInterface $authentication
     * @param string $name
     * @param array $eventParams
     * @param ServerRequestInterface|null $request
     * @return AuthenticationEvent
     */
    protected function createAuthenticationEvent(
        AuthenticationInterface $authentication,
        string $name = AuthenticationEvent::EVENT_AUTHENTICATION_AUTHENTICATE,
        array $eventParams = [],
        ServerRequestInterface $request = null
    ): AuthenticationEvent {
        $event = new AuthenticationEvent();

        $event->setName($name);
        $event->setTarget($this);
        $event->setAuthenticationService($authentication);

        if ($request) {
            $event->setRequest($request);
        }

        $event->setParams(array_merge($event->getParams(), $eventParams));

        return $event;
    }
}
