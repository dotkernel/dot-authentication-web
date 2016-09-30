<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/17/2016
 * Time: 7:36 PM
 */

namespace Dot\Authentication\Web\Listener;

use Dot\Authentication\Web\Event\AuthenticationEvent;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Dot\Authentication\Web\RouteOptionParserTrait;
use N3vrax\DkAuthentication\AuthenticationInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Expressive\Helper\UrlHelper;

class DefaultLogoutListener extends AbstractListenerAggregate
{
    use RouteOptionParserTrait;

    /** @var  AuthenticationInterface */
    protected $authentication;

    /** @var  WebAuthenticationOptions */
    protected $options;

    /** @var  UrlHelper */
    protected $urlHelper;

    /**
     * DefaultLogoutListener constructor.
     * @param AuthenticationInterface $authentication
     * @param UrlHelper $urlHelper
     * @param WebAuthenticationOptions $options
     */
    public function  __construct(
        AuthenticationInterface $authentication,
        UrlHelper $urlHelper,
        WebAuthenticationOptions $options)
    {
        $this->authentication = $authentication;
        $this->options = $options;
        $this->urlHelper = $urlHelper;
    }

    /**
     * @param EventManagerInterface $events
     * @param int $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            AuthenticationEvent::EVENT_LOGOUT,
            [$this, 'logout'],
            1
        );

        $this->listeners[] = $events->attach(
            AuthenticationEvent::EVENT_LOGOUT,
            [$this, 'logoutPost'],
            -1000
        );
    }

    /**
     * @param AuthenticationEvent $e
     */
    public function logout(AuthenticationEvent $e)
    {
        $this->authentication->clearIdentity();
    }

    /**
     * @param AuthenticationEvent $e
     * @return RedirectResponse
     * @throws \Exception
     */
    public function logoutPost(AuthenticationEvent $e)
    {
        $uri = $this->getUri($this->options->getAfterLogoutRoute(), $this->urlHelper);
        return new RedirectResponse($uri);
    }

    /**
     * @return AuthenticationInterface
     */
    public function getAuthentication()
    {
        return $this->authentication;
    }

    /**
     * @param AuthenticationInterface $authentication
     * @return DefaultLogoutListener
     */
    public function setAuthentication($authentication)
    {
        $this->authentication = $authentication;
        return $this;
    }

    /**
     * @return WebAuthenticationOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param WebAuthenticationOptions $options
     * @return DefaultLogoutListener
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return UrlHelper
     */
    public function getUrlHelper()
    {
        return $this->urlHelper;
    }

    /**
     * @param UrlHelper $urlHelper
     * @return DefaultLogoutListener
     */
    public function setUrlHelper($urlHelper)
    {
        $this->urlHelper = $urlHelper;
        return $this;
    }


}