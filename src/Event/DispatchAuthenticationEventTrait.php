<?php
/**
 * @copyright: DotKernel
 * @library: dot-authentication-web
 * @author: n3vrax
 * Date: 2/21/2017
 * Time: 12:00 AM
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web\Event;

use Psr\Http\Message\ResponseInterface;
use Zend\EventManager\EventManagerAwareTrait;

/**
 * Class DispatchAuthenticationEventTrait
 * @package Dot\Authentication\Web\Event
 */
trait DispatchAuthenticationEventTrait
{
    use EventManagerAwareTrait;

    /**
     * @param string $name
     * @param array $params
     * @param null $target
     * @return AuthenticationEvent|mixed
     */
    public function dispatchEvent(string $name, array $params = [], $target = null)
    {
        if ($target === null) {
            $target = $this;
        }

        $event = new AuthenticationEvent($name, $target, $params);
        $r = $this->getEventManager()->triggerEventUntil(function ($r) {
            return ($r instanceof ResponseInterface);
        }, $event);

        if ($r->stopped()) {
            return $r->last();
        }

        return $event;
    }
}
