<?php
/**
 * @see https://github.com/dotkernel/dot-authentication-web/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-authentication-web/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web\Factory;

use Dot\Authentication\AuthenticationInterface;
use Dot\Authentication\Web\Action\LogoutAction;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Dot\Helpers\Route\RouteHelper;
use Psr\Container\ContainerInterface;

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
            $container->get(RouteHelper::class),
            $container->get(WebAuthenticationOptions::class)
        );

        $this->attachListeners($container, $action->getEventManager());
        $action->attach($action->getEventManager(), 1000);

        return $action;
    }
}
