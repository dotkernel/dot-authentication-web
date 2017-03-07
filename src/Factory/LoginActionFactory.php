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
use Dot\Authentication\Web\Action\LoginAction;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Helpers\Route\RouteOptionHelper;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class LoginActionFactory
 * @package Dot\Authentication\Web\Factory
 */
class LoginActionFactory extends BaseActionFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @return LoginAction
     */
    public function __invoke(ContainerInterface $container, string $requestedName): LoginAction
    {
        $config = $container->get('config');
        $debug = $config['debug'] ?? false;

        /** @var LoginAction $action */
        $action = new $requestedName(
            $container->get(AuthenticationInterface::class),
            $container->get(TemplateRendererInterface::class),
            $container->get(RouteOptionHelper::class),
            $container->get(WebAuthenticationOptions::class),
            $container->get(FlashMessengerInterface::class)
        );

        $action->setDebug($debug);

        $this->attachListeners($container, $action->getEventManager());
        $action->attach($action->getEventManager(), 1000);

        return $action;
    }
}
