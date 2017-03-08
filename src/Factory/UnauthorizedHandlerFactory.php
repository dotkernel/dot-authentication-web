<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-authentication-web
 * @author: n3vrax
 * Date: 5/1/2016
 * Time: 3:15 PM
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web\Factory;

use Dot\Authentication\AuthenticationInterface;
use Dot\Authentication\Web\ErrorHandler\UnauthorizedHandler;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Helpers\Route\RouteOptionHelper;
use Interop\Container\ContainerInterface;

/**
 * Class UnauthorizedHandlerFactory
 * @package Dot\Authentication\Web\Factory
 */
class UnauthorizedHandlerFactory extends BaseActionFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @return UnauthorizedHandler
     */
    public function __invoke(ContainerInterface $container, string $requestedName): UnauthorizedHandler
    {
        /** @var UnauthorizedHandler $handler */
        $handler = new $requestedName(
            $container->get(AuthenticationInterface::class),
            $container->get(RouteOptionHelper::class),
            $container->get(WebAuthenticationOptions::class),
            $container->get(FlashMessengerInterface::class)
        );

        $this->attachListeners($container, $handler->getEventManager());
        $handler->attach($handler->getEventManager(), 1000);

        return $handler;
    }
}
