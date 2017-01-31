<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-authentication-web
 * @author: n3vrax
 * Date: 4/30/2016
 * Time: 10:45 PM
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web\ErrorHandler;

use Dot\Authentication\AuthenticationInterface;
use Dot\Authentication\Web\AuthenticationEventTrait;
use Dot\Authentication\Web\Event\AuthenticationEvent;
use Dot\Authentication\Web\Event\AuthenticationEventListenerAwareInterface;
use Dot\Authentication\Web\Event\AuthenticationEventListenerAwareTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class UnauthorizedHandler
 * @package Dot\Authentication\Web\ErrorHandler
 */
class UnauthorizedHandler implements AuthenticationEventListenerAwareInterface
{
    use AuthenticationEventListenerAwareTrait;
    use AuthenticationEventTrait;

    /** @var  AuthenticationInterface */
    protected $authenticationService;

    /** @var array */
    protected $statusCodes = [401, 407];

    /**
     * UnauthorizedHandler constructor.
     * @param AuthenticationInterface $authenticationService
     */
    public function __construct(AuthenticationInterface $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    /**
     * @param $error
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable|null $next
     * @return ResponseInterface
     */
    public function __invoke(
        $error,
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next = null
    ): ResponseInterface {
        if ($error instanceof \Exception && in_array($error->getCode(), $this->statusCodes)
            || in_array($response->getStatusCode(), $this->statusCodes)
        ) {
            $result = $this->triggerUnauthorizedEvent($request, $error);
            if ($result instanceof ResponseInterface) {
                return $result;
            }

            //if listeners did not return a response, send to next error handlers with an explicit status code
            if (!in_array($response->getStatusCode(), $this->statusCodes)) {
                $response = $response->withStatus(401);
            }
        }

        return $next($request, $response, $error);
    }

    public function triggerUnauthorizedEvent(ServerRequestInterface $request, $error): ?ResponseInterface
    {
        $event = $this->createAuthenticationEventWithError(
            $this->authenticationService,
            $error,
            AuthenticationEvent::EVENT_AUTHENTICATION_UNAUTHORIZED,
            [],
            $request
        );

        $result = $this->getEventManager()->triggerEventUntil(function ($r) {
            return ($r instanceof ResponseInterface);
        }, $event);

        $result = $result->last();
        return $result;
    }

    /**
     * @return AuthenticationInterface
     */
    public function getAuthenticationService(): AuthenticationInterface
    {
        return $this->authenticationService;
    }

    /**
     * @param AuthenticationInterface $authenticationService
     */
    public function setAuthenticationService(AuthenticationInterface $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }
}
