<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 5/1/2016
 * Time: 3:15 PM
 */

namespace Dot\Authentication\Web\Factory;

use Dot\Authentication\Web\Event\AuthenticationEvent;
use Interop\Container\ContainerInterface;
use N3vrax\DkAuthentication\AuthenticationInterface;
use N3vrax\DkWebAuthentication\Listener\DefaultUnauthorizedListener;
use N3vrax\DkWebAuthentication\UnauthorizedHandler;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

class UnauthorizedHandlerFactory
{
    /**
     * @param ContainerInterface $container
     * @return UnauthorizedHandler
     */
    public function __invoke(ContainerInterface $container)
    {
        $handler = new UnauthorizedHandler();

        $eventManager = $container->has(EventManagerInterface::class)
            ? $container->get(EventManagerInterface::class)
            : new EventManager();

        $event = new AuthenticationEvent();
        $event->setAuthenticationService($container->get(AuthenticationInterface::class));

        $defaultListener = $container->get(DefaultUnauthorizedListener::class);
        $eventManager->attach(AuthenticationEvent::EVENT_UNAUTHORIZED, $defaultListener, 1);

        $handler->setEventManager($eventManager);
        $handler->setEvent(new AuthenticationEvent());

        return $handler;
    }
}