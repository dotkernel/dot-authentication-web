<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-authentication-web
 * @author: n3vrax
 * Date: 5/1/2016
 * Time: 3:15 PM
 */

namespace Dot\Authentication\Web\Factory;

use Dot\Authentication\AuthenticationInterface;
use Dot\Authentication\Web\ErrorHandler\UnauthorizedHandler;
use Dot\Authentication\Web\Event\AuthenticationEvent;
use Dot\Authentication\Web\Listener\DefaultUnauthorizedListener;
use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

/**
 * Class UnauthorizedHandlerFactory
 * @package Dot\Authentication\Web\Factory
 */
class UnauthorizedHandlerFactory
{
    /**
     * @param ContainerInterface $container
     * @return UnauthorizedHandler
     */
    public function __invoke(ContainerInterface $container)
    {
        $eventManager = $container->has(EventManagerInterface::class)
            ? $container->get(EventManagerInterface::class)
            : new EventManager();

        $defaultListener = $container->get(DefaultUnauthorizedListener::class);
        $eventManager->attach(AuthenticationEvent::EVENT_AUTHENTICATION_UNAUTHORIZED, $defaultListener, 1);

        $handler = new UnauthorizedHandler($container->get(AuthenticationInterface::class));
        $handler->setEventManager($eventManager);

        return $handler;
    }
}