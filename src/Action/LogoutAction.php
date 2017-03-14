<?php
/**
 * @see https://github.com/dotkernel/dot-authentication-web/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-authentication-web/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web\Action;

use Dot\Authentication\AuthenticationInterface;
use Dot\Authentication\Web\Event\AuthenticationEvent;
use Dot\Authentication\Web\Event\AuthenticationEventListenerInterface;
use Dot\Authentication\Web\Event\AuthenticationEventListenerTrait;
use Dot\Authentication\Web\Event\DispatchAuthenticationEventTrait;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Dot\Helpers\Route\RouteOptionHelper;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\RedirectResponse;

/**
 * Class LogoutAction
 * @package Dot\Authentication\Web\Action
 */
class LogoutAction implements MiddlewareInterface, AuthenticationEventListenerInterface
{
    use AuthenticationEventListenerTrait;
    use DispatchAuthenticationEventTrait;

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
     * @param DelegateInterface $delegate
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate): ResponseInterface
    {
        if (!$this->authentication->hasIdentity()) {
            return new RedirectResponse($this->routeHelper->getUri($this->options->getAfterLogoutRoute()));
        }
        $event = $this->dispatchEvent(AuthenticationEvent::EVENT_BEFORE_LOGOUT, [
            'request' => $request,
            'authenticationService' => $this->authentication
        ]);
        if ($event instanceof ResponseInterface) {
            return $event;
        }

        $this->authentication->clearIdentity();

        $this->dispatchEvent(AuthenticationEvent::EVENT_AFTER_LOGOUT, [
            'request' => $request,
            'authenticationService' => $this->authentication
        ]);

        $uri = $this->routeHelper->getUri($this->options->getAfterLogoutRoute());
        return new RedirectResponse($uri);
    }
}
