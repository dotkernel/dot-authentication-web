<?php
/**
 * @see https://github.com/dotkernel/dot-authentication-web/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-authentication-web/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web;

use Dot\Authentication\Web\Action\LoginAction;
use Dot\Authentication\Web\Action\LogoutAction;
use Dot\Authentication\Web\ErrorHandler\UnauthorizedHandler;
use Dot\Authentication\Web\Factory\LoginActionFactory;
use Dot\Authentication\Web\Factory\LogoutActionFactory;
use Dot\Authentication\Web\Factory\UnauthorizedHandlerFactory;
use Dot\Authentication\Web\Factory\WebAuthenticationOptionsFactory;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependenciesConfig(),

            'dot_authentication' => [
                'web' => []
            ]
        ];
    }

    public function getDependenciesConfig(): array
    {
        return [
            'factories' => [
                WebAuthenticationOptions::class => WebAuthenticationOptionsFactory::class,
                LoginAction::class => LoginActionFactory::class,
                LogoutAction::class => LogoutActionFactory::class,
                UnauthorizedHandler::class => UnauthorizedHandlerFactory::class,
            ]
        ];
    }
}
