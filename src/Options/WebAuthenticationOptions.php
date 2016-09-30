<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 4/30/2016
 * Time: 9:36 PM
 */

namespace Dot\Authentication\Web\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class WebAuthenticationOptions
 * @package Dot\Authentication\Web\Options
 */
class WebAuthenticationOptions extends AbstractOptions
{
    /** @var string|array */
    protected $loginRoute = 'login';

    /** @var string|array */
    protected $logoutRoute = 'logout';

    /** @var string|array */
    protected $afterLoginRoute = 'home';

    /** @var string|array */
    protected $afterLogoutRoute = 'login';

    /** @var  string */
    protected $loginTemplate;

    /** @var bool */
    protected $allowRedirect = true;

    /** @var string */
    protected $redirectQueryName = 'redirect';

    protected $__strictMode__ = false;

    /**
     * @return array|string
     */
    public function getLoginRoute()
    {
        return $this->loginRoute;
    }

    /**
     * @param array|string $loginRoute
     * @return WebAuthenticationOptions
     */
    public function setLoginRoute($loginRoute)
    {
        $this->loginRoute = $loginRoute;
        return $this;
    }

    /**
     * @return array|string
     */
    public function getLogoutRoute()
    {
        return $this->logoutRoute;
    }

    /**
     * @param array|string $logoutRoute
     * @return WebAuthenticationOptions
     */
    public function setLogoutRoute($logoutRoute)
    {
        $this->logoutRoute = $logoutRoute;
        return $this;
    }

    /**
     * @return array|string
     */
    public function getAfterLoginRoute()
    {
        return $this->afterLoginRoute;
    }

    /**
     * @param array|string $afterLoginRoute
     * @return WebAuthenticationOptions
     */
    public function setAfterLoginRoute($afterLoginRoute)
    {
        $this->afterLoginRoute = $afterLoginRoute;
        return $this;
    }

    /**
     * @return array|string
     */
    public function getAfterLogoutRoute()
    {
        return $this->afterLogoutRoute;
    }

    /**
     * @param array|string $afterLogoutRoute
     * @return WebAuthenticationOptions
     */
    public function setAfterLogoutRoute($afterLogoutRoute)
    {
        $this->afterLogoutRoute = $afterLogoutRoute;
        return $this;
    }

    /**
     * @return string
     */
    public function getLoginTemplate()
    {
        return $this->loginTemplate;
    }

    /**
     * @param string $loginTemplate
     * @return WebAuthenticationOptions
     */
    public function setLoginTemplate($loginTemplate)
    {
        $this->loginTemplate = $loginTemplate;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isAllowRedirect()
    {
        return $this->allowRedirect;
    }

    /**
     * @param boolean $allowRedirect
     * @return WebAuthenticationOptions
     */
    public function setAllowRedirect($allowRedirect)
    {
        $this->allowRedirect = $allowRedirect;
        return $this;
    }

    /**
     * @return string
     */
    public function getRedirectQueryName()
    {
        return $this->redirectQueryName;
    }

    /**
     * @param string $redirectQueryName
     * @return WebAuthenticationOptions
     */
    public function setRedirectQueryName($redirectQueryName)
    {
        $this->redirectQueryName = $redirectQueryName;
        return $this;
    }

}