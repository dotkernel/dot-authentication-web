<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-authentication-web
 * @author: n3vrax
 * Date: 6/17/2016
 * Time: 8:12 PM
 */

namespace Dot\Authentication\Web\Factory;

use Dot\Authentication\AuthenticationInterface;
use Dot\Authentication\Web\Listener\DefaultLogoutListener;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Dot\Helpers\Route\RouteOptionHelper;
use Interop\Container\ContainerInterface;

/**
 * Class DefaultLogoutListenerFactory
 * @package Dot\Authentication\Web\Factory
 */
class DefaultLogoutListenerFactory
{
    /**
     * @param ContainerInterface $container
     * @return DefaultLogoutListener
     */
    public function __invoke(ContainerInterface $container)
    {
        $options = $container->get(WebAuthenticationOptions::class);
        $authentication = $container->get(AuthenticationInterface::class);
        $routeHelper = $container->get(RouteOptionHelper::class);

        return new DefaultLogoutListener($authentication, $routeHelper, $options);
    }
}
