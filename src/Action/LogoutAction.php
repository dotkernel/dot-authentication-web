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
 * Class LogoutAction
 * @package Dot\Authentication\Web\Action
 */
class LogoutAction
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
     * LogoutActionFactory constructor.
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
        $this->routeHelper = $routeHelper;
        $this->options = $options;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable|null $next
     * @return RedirectResponse|ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        if (!$this->authentication->hasIdentity()) {
            return new RedirectResponse($this->routeHelper->getUri($this->options->getAfterLogoutRoute()));
        }

        $result = $this->triggerLogoutEvent($request, $response);
        if ($result instanceof ResponseInterface) {
            return $result;
        }

        error_log(
            sprintf(
                'Logout event listeners should return a ResponseInterface, "%s" returned',
                is_object($result) ? get_class($result) : gettype($result)
            ),
            E_USER_WARNING
        );

        return $next($request, $response);
    }

    public function triggerLogoutEvent(ServerRequestInterface $request, ResponseInterface $response)
    {
        $event = $this->createAuthenticationEvent(
            $this->authentication,
            AuthenticationEvent::EVENT_AUTHENTICATION_LOGOUT,
            [], $request, $response);

        $result = $this->getEventManager()->triggerEventUntil(function ($r) {
            return ($r instanceof ResponseInterface);
        }, $event);

        $result = $result->last();
        return $result;
    }
}