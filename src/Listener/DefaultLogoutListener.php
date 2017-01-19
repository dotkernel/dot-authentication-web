<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-authentication-web
 * @author: n3vrax
 * Date: 6/17/2016
 * Time: 7:36 PM
 */

namespace Dot\Authentication\Web\Listener;

use Dot\Authentication\AuthenticationInterface;
use Dot\Authentication\Web\Event\AuthenticationEvent;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Dot\Helpers\Route\RouteOptionHelper;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

/**
 * Class DefaultLogoutListener
 * @package Dot\Authentication\Web\Listener
 */
class DefaultLogoutListener extends AbstractListenerAggregate
{
    /** @var  AuthenticationInterface */
    protected $authentication;

    /** @var  WebAuthenticationOptions */
    protected $options;

    /** @var  RouteOptionHelper */
    protected $routeHelper;

    /**
     * DefaultLogoutListener constructor.
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
     * @param EventManagerInterface $events
     * @param int $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            AuthenticationEvent::EVENT_AUTHENTICATION_LOGOUT,
            [$this, 'logout'],
            1
        );

        $this->listeners[] = $events->attach(
            AuthenticationEvent::EVENT_AUTHENTICATION_LOGOUT,
            [$this, 'logoutPost'],
            -1000
        );
    }

    /**
     * @param AuthenticationEvent $e
     */
    public function logout(AuthenticationEvent $e)
    {
        $this->authentication->clearIdentity();
    }

    /**
     * @param AuthenticationEvent $e
     * @return RedirectResponse
     * @throws \Exception
     */
    public function logoutPost(AuthenticationEvent $e)
    {
        $uri = $this->routeHelper->getUri($this->options->getAfterLogoutRoute());
        return new RedirectResponse($uri);
    }
}
