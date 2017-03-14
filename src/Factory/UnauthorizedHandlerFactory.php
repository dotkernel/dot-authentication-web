<?php
/**
 * @see https://github.com/dotkernel/dot-authentication-web/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-authentication-web/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web\Factory;

use Dot\Authentication\AuthenticationInterface;
use Dot\Authentication\Web\ErrorHandler\UnauthorizedHandler;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Helpers\Route\RouteOptionHelper;
use Psr\Container\ContainerInterface;

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
