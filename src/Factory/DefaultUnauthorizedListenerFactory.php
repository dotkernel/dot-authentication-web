<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/17/2016
 * Time: 8:17 PM
 */

namespace N3vrax\DkWebAuthentication\Factory;

use Dot\Authentication\Web\Listener\DefaultUnauthorizedListener;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Interop\Container\ContainerInterface;
use N3vrax\DkSession\FlashMessenger\FlashMessengerInterface;
use Zend\Expressive\Helper\UrlHelper;

class DefaultUnauthorizedListenerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $urlHelper = $container->get(UrlHelper::class);
        $flashMessenger = $container->get(FlashMessengerInterface::class);
        $options = $container->get(WebAuthenticationOptions::class);

        return new DefaultUnauthorizedListener($urlHelper, $flashMessenger, $options);
    }
}