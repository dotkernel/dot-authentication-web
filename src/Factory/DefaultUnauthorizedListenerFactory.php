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
        $config = $container->get('config');
        $debug = isset($config['debug']) ? $config['debug'] : false;

        $routeHelper = $container->get(RouteOptionHelper::class);
        $flashMessenger = $container->get(FlashMessengerInterface::class);
        $options = $container->get(WebAuthenticationOptions::class);

        $listener = new DefaultUnauthorizedListener($routeHelper, $flashMessenger, $options);
        $listener->setDebug($debug);

        return $listener;
    }
}