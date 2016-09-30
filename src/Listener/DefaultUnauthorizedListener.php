<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/17/2016
 * Time: 7:50 PM
 */

namespace Dot\Authentication\Web\Listener;

use Dot\Authentication\Web\Event\AuthenticationEvent;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Dot\Authentication\Web\RouteOptionParserTrait;
use N3vrax\DkSession\FlashMessenger\FlashMessengerInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\Uri;
use Zend\Expressive\Helper\UrlHelper;

class DefaultUnauthorizedListener
{
    use RouteOptionParserTrait;

    /** @var  UrlHelper */
    protected $urlHelper;

    /** @var  WebAuthenticationOptions */
    protected $options;

    /** @var  FlashMessengerInterface */
    protected $flashMessenger;

    /**
     * DefaultUnauthorizedListener constructor.
     * @param UrlHelper $urlHelper
     * @param FlashMessengerInterface $flashMessenger
     * @param WebAuthenticationOptions $options
     */
    public function __construct(
        UrlHelper $urlHelper,
        FlashMessengerInterface $flashMessenger,
        WebAuthenticationOptions $options
    )
    {
        $this->urlHelper = $urlHelper;
        $this->options = $options;
        $this->flashMessenger = $flashMessenger;
    }

    /**
     * @param AuthenticationEvent $e
     * @return RedirectResponse
     * @throws \Exception
     */
    public function __invoke(AuthenticationEvent $e)
    {
        $request = $e->getRequest();

        $messages = [];
        $error = $e->getParam('error', null);
        if(is_string($error)) {
            $messages[] = $error;
        }

        if(is_array($error)) {
            $messages = $error;
        }

        if($error instanceof \Exception) {
            $messages[] = $error->getMessage();
        }

        if(empty($messages)) {
            $messages = ['You are not authorized to access this content'];
        }

        /** @var Uri $uri */
        $uri = $this->getUri($this->options->getLoginRoute(), $this->urlHelper);
        $query = $uri->getQuery();
        $arr = [];
        if($this->options->isAllowRedirect())
        {
            if (!empty($query)) {
                parse_str($query, $arr);
            }

            $query = http_build_query(
                array_merge($arr, [$this->options->getRedirectQueryName() => urlencode($request->getUri())]));

            if (!empty($query))
                $uri = $uri->withQuery($query);
        }

        //add a flash message in case the login page displays errors
        if ($this->flashMessenger) {
            foreach ($messages as $message) {
                $this->flashMessenger->addError($message);
            }
        }

        return new RedirectResponse($uri);
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
     * @return DefaultUnauthorizedListener
     */
    public function setUrlHelper($urlHelper)
    {
        $this->urlHelper = $urlHelper;
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
     * @return DefaultUnauthorizedListener
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return FlashMessengerInterface
     */
    public function getFlashMessenger()
    {
        return $this->flashMessenger;
    }

    /**
     * @param FlashMessengerInterface $flashMessenger
     * @return DefaultUnauthorizedListener
     */
    public function setFlashMessenger($flashMessenger)
    {
        $this->flashMessenger = $flashMessenger;
        return $this;
    }


}