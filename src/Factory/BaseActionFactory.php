<?php
/**
 * @see https://github.com/dotkernel/dot-authentication-web/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-authentication-web/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web\Factory;

use Dot\Authentication\Web\Event\AuthenticationEventListenerInterface;
use Dot\Authentication\Web\Exception\RuntimeException;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManagerInterface;

/**
 * Class BaseActionFactory
 * @package Dot\Authentication\Web\Factory
 */
abstract class BaseActionFactory
{
    /**
     * @param ContainerInterface $container
     * @param EventManagerInterface $eventManager
     */
    protected function attachListeners(
        ContainerInterface $container,
        EventManagerInterface $eventManager
    ) {
        /** @var WebAuthenticationOptions $options */
        $options = $container->get(WebAuthenticationOptions::class);

        if (!empty($options->getEventListeners())
            && is_array($options->getEventListeners())
        ) {
            $listeners = $options->getEventListeners();
            foreach ($listeners as $listener) {
                if (is_string($listener)) {
                    $l = $this->getListenerObject($container, $listener);
                    $p = 1;
                    $l->attach($eventManager, $p);
                } elseif (is_array($listener)) {
                    $l = $listener['type'] ?? '';
                    $p = $listener['priority'] ?? 1;

                    $l = $this->getListenerObject($container, $l);
                    $l->attach($eventManager, $p);
                }
            }
        }
    }

    /**
     * @param ContainerInterface $container
     * @param string $listener
     * @return AuthenticationEventListenerInterface
     */
    protected function getListenerObject(
        ContainerInterface $container,
        string $listener
    ): AuthenticationEventListenerInterface {
        if ($container->has($listener)) {
            $listener = $container->get($listener);
        }

        if (is_string($listener) && class_exists($listener)) {
            $listener = new $listener();
        }

        if (!$listener instanceof AuthenticationEventListenerInterface) {
            throw new RuntimeException('Authentication event listener is not an instance of '
                . AuthenticationEventListenerInterface::class);
        }

        return $listener;
    }
}
