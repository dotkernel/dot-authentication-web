<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-authentication-web
 * @author: n3vrax
 * Date: 6/17/2016
 * Time: 8:05 PM
 */

namespace Dot\Authentication\Web\Factory;

use Dot\Authentication\AuthenticationInterface;
use Dot\Authentication\Web\Listener\DefaultAuthenticationListener;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Helpers\Route\RouteOptionHelper;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class DefaultAuthenticationListenerFactory
 * @package Dot\Authentication\Web\Factory
 */
class DefaultAuthenticationListenerFactory
{
    /**
     * @param ContainerInterface $container
     * @return DefaultAuthenticationListener
     */
    public function __invoke(ContainerInterface $container)
    {
        $options = $container->get(WebAuthenticationOptions::class);
        $authentication = $container->get(AuthenticationInterface::class);
        $template = $container->get(TemplateRendererInterface::class);
        $routeHelper = $container->get(RouteOptionHelper::class);
        $flashMessenger = $container->get(FlashMessengerInterface::class);

        $listener = new DefaultAuthenticationListener(
            $authentication,
            $template,
            $routeHelper,
            $flashMessenger,
            $options
        );

        return $listener;
    }
}
