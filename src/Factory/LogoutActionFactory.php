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
use Dot\Authentication\Web\Action\LogoutAction;
use Dot\Authentication\Web\Listener\DefaultLogoutListener;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Dot\Helpers\Route\RouteOptionHelper;
use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

/**
 * Class LogoutActionFactory
 * @package Dot\Authentication\Web\Factory
 */
class LogoutActionFactory
{
    /**
     * @param ContainerInterface $container
     * @return LogoutAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $eventManager = $container->has(EventManagerInterface::class)
            ? $container->get(EventManagerInterface::class)
            : new EventManager();

        $defaultListeners = $container->get(DefaultLogoutListener::class);
        $defaultListeners->attach($eventManager);

        $action = new LogoutAction(
            $container->get(AuthenticationInterface::class),
            $container->get(RouteOptionHelper::class),
            $container->get(WebAuthenticationOptions::class)
        );

        $action->setEventManager($eventManager);

        return $action;
    }
}
