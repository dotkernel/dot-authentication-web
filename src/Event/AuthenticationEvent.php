<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-authentication-web
 * @author: n3vrax
 * Date: 6/17/2016
 * Time: 3:49 PM
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web\Event;

use Dot\Event\Event;

/**
 * Class AuthenticationEvent
 * @package Dot\Authentication\Web\Event
 */
class AuthenticationEvent extends Event
{
    const EVENT_BEFORE_AUTHENTICATION = 'event.beforeAuthentication';
    const EVENT_AFTER_AUTHENTICATION = 'event.afterAuthentication';
    const EVENT_AUTHENTICATION_SUCCESS = 'event.authenticationSuccess';
    const EVENT_AUTHENTICATION_ERROR = 'event.authenticationError';
    const EVENT_AUTHENTICATION_BEFORE_RENDER = 'event.authenticationBeforeRender';

    const EVENT_BEFORE_LOGOUT = 'event.beforeLogout';
    const EVENT_AFTER_LOGOUT = 'event.afterLogout';

    const EVENT_UNAUTHORIZED = 'event.unauthorized';
}
