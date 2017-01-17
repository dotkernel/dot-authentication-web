<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-authentication-web
 * @author: n3vrax
 * Date: 4/30/2016
 * Time: 9:36 PM
 */

namespace Dot\Authentication\Web\Options;

use Dot\Authentication\Web\Exception\InvalidArgumentException;
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
    protected $allowRedirectParam = true;

    /** @var string */
    protected $redirectParamName = 'redirect';

    /** @var  MessagesOptions */
    protected $messagesOptions;

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
    public function isAllowRedirectParam()
    {
        return $this->allowRedirectParam;
    }

    /**
     * @param boolean $allowRedirectParam
     * @return WebAuthenticationOptions
     */
    public function setAllowRedirectParam($allowRedirectParam)
    {
        $this->allowRedirectParam = $allowRedirectParam;
        return $this;
    }

    /**
     * @return string
     */
    public function getRedirectParamName()
    {
        return $this->redirectParamName;
    }

    /**
     * @param string $redirectParamName
     * @return WebAuthenticationOptions
     */
    public function setRedirectParamName($redirectParamName)
    {
        $this->redirectParamName = $redirectParamName;
        return $this;
    }

    /**
     * @return MessagesOptions
     */
    public function getMessagesOptions()
    {
        if (!$this->messagesOptions) {
            $this->setMessagesOptions([]);
        }
        return $this->messagesOptions;
    }

    /**
     * @param MessagesOptions|array $messagesOptions
     * @return WebAuthenticationOptions
     */
    public function setMessagesOptions($messagesOptions)
    {
        if (is_array($messagesOptions)) {
            $this->messagesOptions = new MessagesOptions($messagesOptions);
        } elseif ($messagesOptions instanceof MessagesOptions) {
            $this->messagesOptions = $messagesOptions;
        } else {
            throw new InvalidArgumentException(sprintf(
                'MessageOptions should be an array or an %s object. %s provided.',
                MessagesOptions::class,
                is_object($messagesOptions) ? get_class($messagesOptions) : gettype($messagesOptions)
            ));
        }
        return $this;
    }
}
