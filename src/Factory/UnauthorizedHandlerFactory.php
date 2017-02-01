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
use Dot\Authentication\Web\AuthenticationEventListenerAwareFactoryTrait;
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
    use AuthenticationEventListenerAwareFactoryTrait;

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @return UnauthorizedHandler
     */
    public function __invoke(ContainerInterface $container, string $requestedName)
    {
        /** @var UnauthorizedHandler $handler */
        $handler = new $requestedName($container->get(AuthenticationInterface::class));

        $eventManager = $container->has(EventManagerInterface::class)
            ? $container->get(EventManagerInterface::class)
            : new EventManager();
        $handler->setEventManager($eventManager);

        $defaultListener = $container->get(DefaultUnauthorizedListener::class);
        $eventManager->attach(AuthenticationEvent::EVENT_UNAUTHORIZED, $defaultListener, 1);

        $this->attachAuthenticationListeners(
            $container,
            $handler,
            AuthenticationEvent::EVENT_UNAUTHORIZED
        );

        return $handler;
    }
}
