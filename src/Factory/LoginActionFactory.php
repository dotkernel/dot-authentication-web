<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-authentication-web
 * @author: n3vrax
 * Date: 4/30/2016
 * Time: 8:40 PM
 */

namespace Dot\Authentication\Web\Factory;

use Dot\Authentication\AuthenticationInterface;
use Dot\Authentication\Web\Action\LoginAction;
use Dot\Authentication\Web\Listener\DefaultAuthenticationListener;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Dot\Helpers\Route\RouteOptionHelper;
use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

/**
 * Class LoginActionFactory
 * @package Dot\Authentication\Web\Factory
 */
class LoginActionFactory
{
    /**
     * @param ContainerInterface $container
     * @return LoginAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $eventManager = $container->has(EventManagerInterface::class)
            ? $container->get(EventManagerInterface::class)
            : new EventManager();

        /** @var DefaultAuthenticationListener $defaultListeners */
        $defaultListeners = $container->get(DefaultAuthenticationListener::class);
        $defaultListeners->attach($eventManager);

        $authentication = $container->get(AuthenticationInterface::class);

        $action = new LoginAction(
            $authentication,
            $container->get(RouteOptionHelper::class),
            $container->get(WebAuthenticationOptions::class)
        );

        $action->setEventManager($eventManager);

        return $action;
    }
}