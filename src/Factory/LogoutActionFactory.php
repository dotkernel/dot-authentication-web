<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-authentication-web
 * @author: n3vrax
 * Date: 4/30/2016
 * Time: 8:40 PM
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web\Factory;

use Dot\Authentication\AuthenticationInterface;
use Dot\Authentication\Web\Action\LogoutAction;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Dot\Helpers\Route\RouteOptionHelper;
use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

/**
 * Class LogoutActionFactory
 * @package Dot\Authentication\Web\Factory
 */
class LogoutActionFactory extends BaseActionFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @return LogoutAction
     */
    public function __invoke(ContainerInterface $container, string $requestedName): LogoutAction
    {
        /** @var LogoutAction $action */
        $action = new $requestedName(
            $container->get(AuthenticationInterface::class),
            $container->get(RouteOptionHelper::class),
            $container->get(WebAuthenticationOptions::class)
        );

        $eventManager = $container->has(EventManagerInterface::class)
            ? $container->get(EventManagerInterface::class)
            : new EventManager();

        $action->setEventManager($eventManager);

        $this->attachListeners($container, $eventManager);
        $action->attach($eventManager, 1000);

        return $action;
    }
}
