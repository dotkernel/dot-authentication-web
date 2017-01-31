<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-authentication-web
 * @author: n3vrax
 * Date: 4/30/2016
 * Time: 8:37 PM
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web\Action;

use Dot\Authentication\AuthenticationInterface;
use Dot\Authentication\Web\AuthenticationEventTrait;
use Dot\Authentication\Web\Event\AuthenticationEvent;
use Dot\Authentication\Web\Event\AuthenticationEventListenerAwareInterface;
use Dot\Authentication\Web\Event\AuthenticationEventListenerAwareTrait;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Dot\Helpers\Route\RouteOptionHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\RedirectResponse;

/**
 * Class LogoutAction
 * @package Dot\Authentication\Web\Action
 */
class LogoutAction implements AuthenticationEventListenerAwareInterface
{
    use AuthenticationEventListenerAwareTrait;
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
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next = null
    ): ResponseInterface {
        if (!$this->authentication->hasIdentity()) {
            return new RedirectResponse($this->routeHelper->getUri($this->options->getAfterLogoutRoute()));
        }

        $result = $this->triggerLogoutEvent($request);
        if ($result instanceof ResponseInterface) {
            return $result;
        }

        return $next($request, $response);
    }

    public function triggerLogoutEvent(ServerRequestInterface $request): ?ResponseInterface
    {
        $event = $this->createAuthenticationEvent(
            $this->authentication,
            AuthenticationEvent::EVENT_AUTHENTICATION_LOGOUT,
            [],
            $request
        );

        $result = $this->getEventManager()->triggerEventUntil(function ($r) {
            return ($r instanceof ResponseInterface);
        }, $event);

        $result = $result->last();
        return $result;
    }
}
