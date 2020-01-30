<?php
/**
 * @see https://github.com/dotkernel/dot-authentication-web/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-authentication-web/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web\Event;

use Laminas\EventManager\AbstractListenerAggregate;

/**
 * Class AbstractAuthenticationEventListener
 * @package Dot\Authentication\Web\Event
 */
abstract class AbstractAuthenticationEventListener extends AbstractListenerAggregate implements
    AuthenticationEventListenerInterface
{
    use AuthenticationEventListenerTrait;
}
