<?php
/**
 * @see https://github.com/dotkernel/dot-authentication-web/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-authentication-web/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web\Factory;

use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Psr\Container\ContainerInterface;

/**
 * Class WebAuthenticationOptionsFactory
 * @package Dot\Authentication\Web\Factory
 */
class WebAuthenticationOptionsFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @return WebAuthenticationOptions
     */
    public function __invoke(ContainerInterface $container, string $requestedName)
    {
        $config = $container->get('config')['dot_authentication']['web'];
        return new $requestedName($config);
    }
}
