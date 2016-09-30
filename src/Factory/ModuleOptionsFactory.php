<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 5/1/2016
 * Time: 2:50 PM
 */

namespace Dot\Authentication\Web\Factory;

use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Interop\Container\ContainerInterface;

class ModuleOptionsFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config')['dk_authentication']['web'];
        return new WebAuthenticationOptions($config);
    }
}