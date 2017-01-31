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
use Dot\Authentication\Web\AuthenticationEventListenerAwareFactoryTrait;
use Dot\Authentication\Web\Event\AuthenticationEvent;
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
    use AuthenticationEventListenerAwareFactoryTrait;

    public function __construct()
    {
        $this->eventListenersConfigKey = 'authenticate';
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @return LoginAction
     */
    public function __invoke(ContainerInterface $container, string $requestedName)
    {
        $eventManager = $container->has(EventManagerInterface::class)
            ? $container->get(EventManagerInterface::class)
            : new EventManager();

        /** @var LoginAction $action */
        $action = new $requestedName(
            $container->get(AuthenticationInterface::class),
            $container->get(RouteOptionHelper::class),
            $container->get(WebAuthenticationOptions::class)
        );

        $action->setEventManager($eventManager);
        /** @var DefaultAuthenticationListener $defaultListeners */
        $defaultListeners = $container->get(DefaultAuthenticationListener::class);
        $defaultListeners->attach($action->getEventManager());

        $this->attachAuthenticationListeners(
            $container,
            $action,
            AuthenticationEvent::EVENT_AUTHENTICATION_AUTHENTICATE
        );

        return $action;
    }


}
