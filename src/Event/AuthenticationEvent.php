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

use Dot\Authentication\AuthenticationInterface;
use Dot\Authentication\AuthenticationResult;
use Dot\Authentication\Identity\IdentityInterface;
use Dot\Event\Event;

/**
 * Class AuthenticationEvent
 * @package Dot\Authentication\Web\Event
 */
class AuthenticationEvent extends Event
{
    const EVENT_AUTHENTICATION_AUTHENTICATE = 'event.authentication.authenticate';
    const EVENT_AUTHENTICATION_LOGOUT = 'event.authentication.logout';
    const EVENT_AUTHENTICATION_UNAUTHORIZED = 'event.authentication.unauthorized';

    /** @var  IdentityInterface */
    protected $identity;

    /** @var  AuthenticationResult */
    protected $result;

    /** @var  AuthenticationInterface */
    protected $authentication;

    /** @var  mixed */
    protected $error;

    /**
     * @return IdentityInterface
     */
    public function getIdentity(): ?IdentityInterface
    {
        return $this->identity;
    }

    /**
     * @param IdentityInterface $identity
     */
    public function setIdentity(IdentityInterface $identity)
    {
        $this->identity = $identity;
    }

    /**
     * @return AuthenticationResult
     */
    public function getAuthenticationResult(): ?AuthenticationResult
    {
        return $this->result;
    }

    /**
     * @param AuthenticationResult $result
     */
    public function setAuthenticationResult(AuthenticationResult $result)
    {
        $this->result = $result;
    }

    /**
     * @return AuthenticationInterface
     */
    public function getAuthenticationService(): AuthenticationInterface
    {
        return $this->authentication;
    }

    /**
     * @param AuthenticationInterface $authentication
     */
    public function setAuthenticationService(AuthenticationInterface $authentication)
    {
        $this->authentication = $authentication;
    }

    /**
     * @return mixed
     */
    public function getError(): ?mixed
    {
        return $this->error;
    }

    /**
     * @param mixed|null $error
     */
    public function setError(mixed $error = null)
    {
        $this->error = $error;
    }
}
