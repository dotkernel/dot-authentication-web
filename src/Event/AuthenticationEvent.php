<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-authentication-web
 * @author: n3vrax
 * Date: 6/17/2016
 * Time: 3:49 PM
 */

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
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @param IdentityInterface $identity
     * @return AuthenticationEvent
     */
    public function setIdentity(IdentityInterface $identity)
    {
        $this->identity = $identity;
        return $this;
    }

    /**
     * @return AuthenticationResult
     */
    public function getAuthenticationResult()
    {
        return $this->result;
    }

    /**
     * @param AuthenticationResult $result
     * @return AuthenticationEvent
     */
    public function setAuthenticationResult(AuthenticationResult $result)
    {
        $this->result = $result;
        return $this;
    }

    /**
     * @return AuthenticationInterface
     */
    public function getAuthenticationService()
    {
        return $this->authentication;
    }

    /**
     * @param AuthenticationInterface $authentication
     * @return AuthenticationEvent
     */
    public function setAuthenticationService(AuthenticationInterface $authentication)
    {
        $this->authentication = $authentication;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param mixed $error
     * @return AuthenticationEvent
     */
    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

}