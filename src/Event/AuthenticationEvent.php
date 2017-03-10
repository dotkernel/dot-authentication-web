<?php
/**
 * @see https://github.com/dotkernel/dot-authentication-web/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-authentication-web/blob/master/LICENSE.md MIT License
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
