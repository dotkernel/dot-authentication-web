<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-authentication-web
 * @author: n3vrax
 * Date: 5/19/2016
 * Time: 12:54 AM
 */

namespace Dot\Authentication\Web;

use Dot\Authentication\Web\Action\LoginAction;
use Dot\Authentication\Web\Action\LogoutAction;
use Dot\Authentication\Web\ErrorHandler\UnauthorizedHandler;
use Dot\Authentication\Web\Factory\DefaultAuthenticationListenerFactory;
use Dot\Authentication\Web\Factory\DefaultLogoutListenerFactory;
use Dot\Authentication\Web\Factory\DefaultUnauthorizedListenerFactory;
use Dot\Authentication\Web\Factory\LoginActionFactory;
use Dot\Authentication\Web\Factory\LogoutActionFactory;
use Dot\Authentication\Web\Factory\WebAuthenticationOptionsFactory;
use Dot\Authentication\Web\Factory\UnauthorizedHandlerFactory;
use Dot\Authentication\Web\Listener\DefaultAuthenticationListener;
use Dot\Authentication\Web\Listener\DefaultLogoutListener;
use Dot\Authentication\Web\Listener\DefaultUnauthorizedListener;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;

class ConfigProvider
{
    public function __invoke()
    {
        return [
            'dependencies' => [
                'factories' => [
                    WebAuthenticationOptions::class => WebAuthenticationOptionsFactory::class,

                    LoginAction::class => LoginActionFactory::class,
                    LogoutAction::class => LogoutActionFactory::class,

                    UnauthorizedHandler::class => UnauthorizedHandlerFactory::class,
                    
                    DefaultAuthenticationListener::class => DefaultAuthenticationListenerFactory::class,
                    DefaultLogoutListener::class => DefaultLogoutListenerFactory::class,
                    DefaultUnauthorizedListener::class => DefaultUnauthorizedListenerFactory::class,
                ]
            ],

            'middleware_pipeline' => [
                'error' => [
                    'middleware' => [
                        UnauthorizedHandler::class,
                    ],
                    'error'    => true,
                    'priority' => -10000,
                ],
            ],

            //default routes
            'routes' => [
                'login_route' => [
                    'name' => 'login',
                    'path' => '/login',
                    'middleware' => LoginAction::class,
                    'allowed_methods' => ['GET', 'POST']
                ],
                'logout_route' => [
                    'name' => 'logout',
                    'path' => '/logout',
                    'middleware' => LogoutAction::class,
                    'allowed_methods' => ['GET']
                ],
            ],

            'dot_authentication' => [
                'web' => [
                    'login_route' => 'login',
                    'logout_route' => 'logout',

                    'message_options' => [],
                ]
            ]
        ];
    }
}