<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-authentication-web
 * @author: n3vrax
 * Date: 6/17/2016
 * Time: 8:17 PM
 */

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
        $routeHelper = $container->get(RouteOptionHelper::class);
        $flashMessenger = $container->get(FlashMessengerInterface::class);
        $options = $container->get(WebAuthenticationOptions::class);

        return new DefaultUnauthorizedListener($routeHelper, $flashMessenger, $options);
    }
}