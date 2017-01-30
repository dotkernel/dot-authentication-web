<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-authentication-web
 * @author: n3vrax
 * Date: 6/17/2016
 * Time: 8:05 PM
 */

declare(strict_types = 1);

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
        $listener = new DefaultAuthenticationListener(
            $container->get(AuthenticationInterface::class),
            $container->get(TemplateRendererInterface::class),
            $container->get(RouteOptionHelper::class),
            $container->get(FlashMessengerInterface::class),
            $container->get(WebAuthenticationOptions::class)
        );

        $config = $container->get('config');
        $debug = $config['debug'] ?? false;
        $listener->setDebug($debug);
        return $listener;
    }
}
