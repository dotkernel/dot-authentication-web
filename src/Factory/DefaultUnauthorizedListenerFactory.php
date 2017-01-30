<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-authentication-web
 * @author: n3vrax
 * Date: 6/17/2016
 * Time: 8:17 PM
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web\Factory;

use Dot\Authentication\Web\Listener\DefaultUnauthorizedListener;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Helpers\Route\RouteOptionHelper;
use Interop\Container\ContainerInterface;

/**
 * Class DefaultUnauthorizedListenerFactory
 * @package N3vrax\DkWebAuthentication\Factory
 */
class DefaultUnauthorizedListenerFactory
{
    /**
     * @param ContainerInterface $container
     * @return DefaultUnauthorizedListener
     */
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config');
        $debug = $config['debug'] ?? false;

        $listener = new DefaultUnauthorizedListener(
            $container->get(RouteOptionHelper::class),
            $container->get(FlashMessengerInterface::class),
            $container->get(WebAuthenticationOptions::class)
        );

        $listener->setDebug($debug);
        return $listener;
    }
}
