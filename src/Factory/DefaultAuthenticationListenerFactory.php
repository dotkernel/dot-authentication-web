<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/17/2016
 * Time: 8:05 PM
 */

namespace Dot\Authentication\Web\Factory;

use Dot\Authentication\Web\Listener\DefaultAuthenticationListener;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Interop\Container\ContainerInterface;
use N3vrax\DkAuthentication\AuthenticationInterface;
use N3vrax\DkSession\FlashMessenger\FlashMessengerInterface;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Template\TemplateRendererInterface;

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
        $urlHelper = $container->get(UrlHelper::class);
        $flashMessenger = $container->get(FlashMessengerInterface::class);

        $listener = new DefaultAuthenticationListener(
            $authentication,
            $template,
            $urlHelper,
            $flashMessenger,
            $options
        );

        return $listener;
    }
}