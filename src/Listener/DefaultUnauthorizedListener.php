<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-authentication-web
 * @author: n3vrax
 * Date: 6/17/2016
 * Time: 7:50 PM
 */

namespace Dot\Authentication\Web\Listener;

use Dot\Authentication\Web\Event\AuthenticationEvent;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Helpers\Route\RouteOptionHelper;
use Dot\Helpers\Route\UriHelperTrait;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\Uri;

/**
 * Class DefaultUnauthorizedListener
 * @package Dot\Authentication\Web\Listener
 */
class DefaultUnauthorizedListener
{
    use UriHelperTrait;

    /** @var  RouteOptionHelper */
    protected $routeHelper;

    /** @var  WebAuthenticationOptions */
    protected $options;

    /** @var  FlashMessengerInterface */
    protected $flashMessenger;

    /**
     * DefaultUnauthorizedListener constructor.
     * @param RouteOptionHelper $routeHelper
     * @param FlashMessengerInterface $flashMessenger
     * @param WebAuthenticationOptions $options
     */
    public function __construct(
        RouteOptionHelper $routeHelper,
        FlashMessengerInterface $flashMessenger,
        WebAuthenticationOptions $options
    )
    {
        $this->routeHelper = $routeHelper;
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
        $error = $e->getError();
        if (is_array($error)) {
            foreach ($error as $e) {
                if (is_string($e)) {
                    $messages[] = $e;
                }
            }
        } else {
            if (is_string($error)) {
                $messages[] = $error;
            } else {
                if ($error instanceof \Exception) {
                    $messages[] = $error->getMessage();
                }
            }
        }

        if(empty($messages)) {
            $messages = [$this->options->getMessage(WebAuthenticationOptions::MESSAGE_DEFAULT_UNAUTHORIZED)];
        }

        //add a flash message in case the login page displays errors
        if ($this->flashMessenger) {
            foreach ($messages as $message) {
                $this->flashMessenger->addError($message);
            }
        }

        /** @var Uri $uri */
        $uri = $this->routeHelper->getUri($this->options->getLoginRoute());
        if($this->options->isAllowRedirectParam()) {
            $uri = $this->appendQueryParam($uri, $request->getUri(), $this->options->getRedirectParamName());
        }

        return new RedirectResponse($uri);
    }

}