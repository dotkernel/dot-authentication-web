<?php
/**
 * @see https://github.com/dotkernel/dot-authentication-web/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-authentication-web/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web\Event;

use Dot\Event\Event;
use Laminas\EventManager\ResponseCollection;
use Psr\Http\Message\ResponseInterface;
use Laminas\EventManager\EventManagerAwareTrait;

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
    public function dispatchEvent(string $name, array $params = [], mixed $target = null): Event|ResponseCollection
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
