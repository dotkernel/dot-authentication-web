<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-authentication-web
 * @author: n3vrax
 * Date: 4/30/2016
 * Time: 9:36 PM
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class WebAuthenticationOptions
 * @package Dot\Authentication\Web\Options
 */
class WebAuthenticationOptions extends AbstractOptions
{
    /** @var string|array */
    protected $loginRoute = ['route_name' => 'login'];

    /** @var string|array */
    protected $logoutRoute = ['route_name' => 'logout'];

    /** @var string|array */
    protected $afterLoginRoute = ['route_name' => 'home'];

    /** @var string|array */
    protected $afterLogoutRoute = ['route_name' => 'login'];

    /** @var  string */
    protected $loginTemplate = '';

    /** @var bool */
    protected $enableWantedUrl = true;

    /** @var string */
    protected $wantedUrlName = 'redirect';

    /** @var array */
    protected $eventListeners = [];

    /** @var  MessagesOptions */
    protected $messagesOptions;

    /**
     * WebAuthenticationOptions constructor.
     * @param null $options
     */
    public function __construct($options = null)
    {
        $this->__strictMode__ = false;
        parent::__construct($options);
    }

    /**
     * @return array
     */
    public function getLoginRoute(): array
    {
        return $this->loginRoute;
    }

    /**
     * @param array $loginRoute
     */
    public function setLoginRoute(array $loginRoute)
    {
        $this->loginRoute = $loginRoute;
    }

    /**
     * @return array
     */
    public function getLogoutRoute(): array
    {
        return $this->logoutRoute;
    }

    /**
     * @param array $logoutRoute
     */
    public function setLogoutRoute(array $logoutRoute)
    {
        $this->logoutRoute = $logoutRoute;
    }

    /**
     * @return array
     */
    public function getAfterLoginRoute(): array
    {
        return $this->afterLoginRoute;
    }

    /**
     * @param array $afterLoginRoute
     */
    public function setAfterLoginRoute(array $afterLoginRoute)
    {
        $this->afterLoginRoute = $afterLoginRoute;
    }

    /**
     * @return array
     */
    public function getAfterLogoutRoute(): array
    {
        return $this->afterLogoutRoute;
    }

    /**
     * @param array|string $afterLogoutRoute
     */
    public function setAfterLogoutRoute(array $afterLogoutRoute)
    {
        $this->afterLogoutRoute = $afterLogoutRoute;
    }

    /**
     * @return string
     */
    public function getLoginTemplate(): string
    {
        return $this->loginTemplate ?? '';
    }

    /**
     * @param string $loginTemplate
     */
    public function setLoginTemplate(string $loginTemplate)
    {
        $this->loginTemplate = $loginTemplate;
    }

    /**
     * @return MessagesOptions
     */
    public function getMessagesOptions(): MessagesOptions
    {
        if (!$this->messagesOptions) {
            $this->setMessagesOptions([]);
        }
        return $this->messagesOptions;
    }

    /**
     * @param MessagesOptions|array $messagesOptions
     */
    public function setMessagesOptions(array $messagesOptions)
    {
        $this->messagesOptions = new MessagesOptions($messagesOptions);
    }

    /**
     * @return bool
     */
    public function isEnableWantedUrl(): bool
    {
        return $this->enableWantedUrl;
    }

    /**
     * @param bool $enableWantedUrl
     */
    public function setEnableWantedUrl(bool $enableWantedUrl)
    {
        $this->enableWantedUrl = $enableWantedUrl;
    }

    /**
     * @return string
     */
    public function getWantedUrlName(): string
    {
        return $this->wantedUrlName;
    }

    /**
     * @param string $wantedUrlName
     */
    public function setWantedUrlName(string $wantedUrlName)
    {
        $this->wantedUrlName = $wantedUrlName;
    }

    /**
     * @return array
     */
    public function getEventListeners(): array
    {
        return $this->eventListeners;
    }

    /**
     * @param array $eventListeners
     */
    public function setEventListeners(array $eventListeners)
    {
        $this->eventListeners = $eventListeners;
    }
}
