<?php
/**
 * @copyright: DotKernel
 * @library: dot-authentication-web
 * @author: n3vrax
 * Date: 1/30/2017
 * Time: 8:31 PM
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
