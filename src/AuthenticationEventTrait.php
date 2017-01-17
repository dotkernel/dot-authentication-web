<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-authentication-web
 * @author: n3vrax
 * Date: 9/30/2016
 * Time: 6:34 PM
 */

namespace Dot\Authentication\Web;

use Dot\Authentication\AuthenticationInterface;
use Dot\Authentication\Web\Event\AuthenticationEvent;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class AuthenticationEventTrait
 * @package Dot\Authentication\Web
 */
trait AuthenticationEventTrait
{
    /**
     * @param AuthenticationInterface $authentication
     * @param $error
     * @param string $name
     * @param array $eventParams
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return AuthenticationEvent
     */
    protected function createAuthenticationEventWithError(
        AuthenticationInterface $authentication,
        $error,
        $name = AuthenticationEvent::EVENT_AUTHENTICATION_UNAUTHORIZED,
        array $eventParams = [],
        ServerRequestInterface $request = null,
        ResponseInterface $response = null
    ) {

        $event = $this->createAuthenticationEvent($authentication, $name, $eventParams, $request, $response);
        $event->setError($error);

        return $event;
    }

    /**
     * @param AuthenticationInterface $authentication
     * @param string $name
     * @param array $eventParams
     * @param ServerRequestInterface|null $request
     * @param ResponseInterface|null $response
     * @return AuthenticationEvent
     */
    protected function createAuthenticationEvent(
        AuthenticationInterface $authentication,
        $name = AuthenticationEvent::EVENT_AUTHENTICATION_AUTHENTICATE,
        array $eventParams = [],
        ServerRequestInterface $request = null,
        ResponseInterface $response = null
    ) {
        $event = new AuthenticationEvent();
        $event->setName($name);
        $event->setTarget($this);
        $event->setAuthenticationService($authentication);
        if ($request) {
            $event->setRequest($request);
        }
        if ($response) {
            $event->setResponse($response);
        }
        $event->setParams(array_merge($event->getParams(), $eventParams));

        return $event;
    }
}
