<?php
/**
 * @copyright: DotKernel
 * @library: dot-authentication-web
 * @author: n3vrax
 * Date: 1/30/2017
 * Time: 9:49 PM
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web;

use Dot\Authentication\Web\Event\AuthenticationEventListenerAwareInterface;
use Dot\Authentication\Web\Event\AuthenticationEventListenerInterface;
use Dot\Authentication\Web\Exception\RuntimeException;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Interop\Container\ContainerInterface;

/**
 * Class AuthenticationEventListenerAwareFactoryTrait
 * @package Dot\Authentication\Web
 */
trait AuthenticationEventListenerAwareFactoryTrait
{
    /**
     * @param ContainerInterface $container
     * @param AuthenticationEventListenerAwareInterface $action
     * @param string $eventName
     */
    protected function attachAuthenticationListeners(
        ContainerInterface $container,
        AuthenticationEventListenerAwareInterface $action,
        string $eventName
    ) {
        /** @var WebAuthenticationOptions $moduleOptions */
        $moduleOptions = $container->get(WebAuthenticationOptions::class);

        $authenticationListeners = $moduleOptions->getEventListeners();
        if (isset($authenticationListeners[$eventName])
            && is_array($authenticationListeners[$eventName])
        ) {
            foreach ($authenticationListeners[$eventName] as $listenerConfig) {
                if (is_array($listenerConfig)) {
                    $listener = $listenerConfig['type'] ?? '';
                    $priority = (int)($listenerConfig['priority'] ?? 1);

                    $listener = $this->getListenerObject($container, $listener);
                    $action->attachListener(
                        $listener,
                        $priority,
                        $eventName
                    );
                } elseif (is_string($listenerConfig)) {
                    $type = $listenerConfig;
                    $priority = -2000;

                    $listener = $this->getListenerObject($container, $type);
                    $action->attachListener($listener, $priority, $eventName);
                }
            }
        }
    }

    protected function getListenerObject(
        ContainerInterface $container,
        $listenerType
    ): AuthenticationEventListenerInterface {
        $listener = $listenerType;
        if ($container->has($listener)) {
            $listener = $container->get($listener);
        }

        if (is_string($listener) && class_exists($listener)) {
            $listener = new $listener();
        }

        if (!$listener instanceof AuthenticationEventListenerInterface) {
            throw new RuntimeException(sprintf(
                'Authentication event listener must be an instance of %s, but %s was given',
                AuthenticationEventListenerInterface::class,
                is_object($listener) ? get_class($listener) : gettype($listener)
            ));
        }

        return $listener;
    }
}
