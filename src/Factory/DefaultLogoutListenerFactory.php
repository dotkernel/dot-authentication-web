<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/17/2016
 * Time: 8:12 PM
 */

namespace Dot\Authentication\Web\Factory;

use Dot\Authentication\Web\Listener\DefaultLogoutListener;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Interop\Container\ContainerInterface;
use N3vrax\DkAuthentication\AuthenticationInterface;
use Zend\Expressive\Helper\UrlHelper;

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
        $urlHelper = $container->get(UrlHelper::class);

        return new DefaultLogoutListener($authentication, $urlHelper, $options);
    }
}