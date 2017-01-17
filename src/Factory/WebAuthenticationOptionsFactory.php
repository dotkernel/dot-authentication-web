<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-authentication-web
 * @author: n3vrax
 * Date: 5/1/2016
 * Time: 2:50 PM
 */

namespace Dot\Authentication\Web\Factory;

use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Interop\Container\ContainerInterface;

/**
 * Class WebAuthenticationOptionsFactory
 * @package Dot\Authentication\Web\Factory
 */
class WebAuthenticationOptionsFactory
{
    /**
     * @param ContainerInterface $container
     * @return WebAuthenticationOptions
     */
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config')['dot_authentication']['web'];
        return new WebAuthenticationOptions($config);
    }
}
