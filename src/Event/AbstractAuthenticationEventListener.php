<?php
/**
 * @copyright: DotKernel
 * @library: dot-authentication-web
 * @author: n3vrax
 * Date: 1/30/2017
 * Time: 8:33 PM
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web\Event;

use Zend\EventManager\AbstractListenerAggregate;

abstract class AbstractAuthenticationEventListener extends AbstractListenerAggregate implements
    AuthenticationEventListenerInterface
{
    use AuthenticationEventListenerTrait;
}
