<?php
/**
 * @see https://github.com/dotkernel/dot-authentication-web/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-authentication-web/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web\Event;

use Zend\EventManager\ListenerAggregateInterface;

/**
 * Interface AuthenticationEventListenerInterface
 * @package Dot\Authentication\Web\Event
 */
interface AuthenticationEventListenerInterface extends ListenerAggregateInterface
{
    public function onBeforeAuthentication(AuthenticationEvent $e);

    public function onAfterAuthentication(AuthenticationEvent $e);

    public function onAuthenticationSuccess(AuthenticationEvent $e);

    public function onAuthenticationError(AuthenticationEvent $e);

    public function onAuthenticationBeforeRender(AuthenticationEvent $e);

    public function onBeforeLogout(AuthenticationEvent $e);

    public function onAfterLogout(AuthenticationEvent $e);

    public function onUnauthorized(AuthenticationEvent $e);
}
