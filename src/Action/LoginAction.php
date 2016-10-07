<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-authentication-web
 * @author: n3vrax
 * Date: 4/30/2016
 * Time: 8:37 PM
 */

namespace Dot\Authentication\Web\Action;

use Dot\Authentication\AuthenticationInterface;
use Dot\Authentication\Web\AuthenticationEventTrait;
use Dot\Authentication\Web\Event\AuthenticationEvent;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Dot\Helpers\Route\RouteOptionHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\EventManager\EventManagerAwareTrait;

/**
 * Class LoginAction
 * @package Dot\Authentication\Web\Action
 */
class LoginAction
{
    use EventManagerAwareTrait;
    use AuthenticationEventTrait;

    /** @var  AuthenticationInterface */
    protected $authentication;

    /** @var  RouteOptionHelper */
    protected $routeHelper;

    /** @var  WebAuthenticationOptions */
    protected $options;

    /**
     * LoginAction constructor.
     * @param AuthenticationInterface $authentication
     * @param RouteOptionHelper $routeHelper
     * @param WebAuthenticationOptions $options
     */
    public function __construct(
        AuthenticationInterface $authentication,
        RouteOptionHelper $routeHelper,
        WebAuthenticationOptions $options
    ) {
        $this->authentication = $authentication;
        $this->options = $options;
        $this->routeHelper = $routeHelper;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable|null $next
     * @return RedirectResponse
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        if ($this->authentication->hasIdentity()) {
            return new RedirectResponse($this->routeHelper->getUri($this->options->getAfterLoginRoute()));
        }

        $data = [];
        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
        }

        $result = $this->triggerAuthenticationEvent($request, $response, $data);
        if ($result instanceof ResponseInterface) {
            return $result;
        }

        error_log(
            sprintf(
                'Authentication event handlers should return a ResponseInterface, "%s" returned',
                is_object($result) ? get_class($result) : gettype($result)
            ),
            E_USER_WARNING
        );

        return $next($request, $response);
    }

    public function triggerAuthenticateEvent(ServerRequestInterface $request, ResponseInterface $response, $data)
    {
        $event = $this->createAuthenticationEvent(
            $this->authentication,
            AuthenticationEvent::EVENT_AUTHENTICATION_AUTHENTICATE,
            $data, $request, $response
        );

        $result = $this->getEventManager()->triggerEventUntil(function ($r) {
            return ($r instanceof ResponseInterface);
        }, $event);

        $result = $result->last();
        return $result;
    }
}