<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-authentication-web
 * @author: n3vrax
 * Date: 6/17/2016
 * Time: 4:24 PM
 */

namespace Dot\Authentication\Web\Listener;

use Dot\Authentication\AuthenticationInterface;
use Dot\Authentication\AuthenticationResult;
use Dot\Authentication\Web\Event\AuthenticationEvent;
use Dot\Authentication\Web\Exception\RuntimeException;
use Dot\Authentication\Web\Options\MessagesOptions;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Helpers\Route\RouteOptionHelper;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\Uri;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class DefaultAuthenticationListener
 * @package Dot\Authentication\Web\Listener
 */
class DefaultAuthenticationListener extends AbstractListenerAggregate
{
    /** @var  TemplateRendererInterface */
    protected $template;

    /** @var  RouteOptionHelper */
    protected $routeHelper;

    /** @var  AuthenticationInterface */
    protected $authentication;

    /** @var  WebAuthenticationOptions */
    protected $options;

    /** @var  FlashMessengerInterface */
    protected $flashMessenger;

    /**
     * DefaultAuthenticationListener constructor.
     * @param AuthenticationInterface $authentication
     * @param TemplateRendererInterface $template
     * @param RouteOptionHelper $routeHelper
     * @param FlashMessengerInterface $flashMessenger
     * @param WebAuthenticationOptions $options
     */
    public function __construct(
        AuthenticationInterface $authentication,
        TemplateRendererInterface $template,
        RouteOptionHelper $routeHelper,
        FlashMessengerInterface $flashMessenger,
        WebAuthenticationOptions $options
    ) {
        $this->authentication = $authentication;
        $this->routeHelper = $routeHelper;
        $this->template = $template;
        $this->options = $options;
        $this->flashMessenger = $flashMessenger;
    }

    /**
     * @param EventManagerInterface $events
     * @param int $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            AuthenticationEvent::EVENT_AUTHENTICATION_AUTHENTICATE,
            [$this, 'prepare'],
            1000
        );

        $this->listeners[] = $events->attach(
            AuthenticationEvent::EVENT_AUTHENTICATION_AUTHENTICATE,
            [$this, 'authenticate'],
            1
        );

        $this->listeners[] = $events->attach(
            AuthenticationEvent::EVENT_AUTHENTICATION_AUTHENTICATE,
            [$this, 'authenticationPost'],
            -1000
        );
    }

    /**
     * @param AuthenticationEvent $e
     */
    public function prepare(AuthenticationEvent $e)
    {
        //nothing to prepare for now, let it to implementors
    }

    /**
     * @param AuthenticationEvent $e
     */
    public function authenticate(AuthenticationEvent $e)
    {
        $request = $e->getRequest();
        $response = $e->getResponse();
        $error = $e->getError();
        if ($request->getMethod() === 'POST' && empty($error)) {

            $result = $this->authentication->authenticate($request, $response);
            //we get this in case authentication skipped(due to missing credentials in request)
            //but for web application, we want to force implemetors to prepare their auth adapter first
            //so we throw an exception to be clear developers have missed something
            if ($result === false) {
                throw new RuntimeException('Authentication service could not authenticate request. '.
                    'Have you forgot to prepare the request first according to authentication adapter needs?');
            }

            if ($result instanceof AuthenticationResult) {
                $e->setAuthenticationResult($result);

                if ($result->isValid()) {
                    $e->setIdentity($result->getIdentity());
                } else {
                    $e->setError($result->getMessage());
                }

                //set the possibly modified PSR7 messages to the event
                if ($result->getRequest()) {
                    $e->setRequest($result->getRequest());
                }

                if ($result->getResponse()) {
                    $e->setResponse($result->getResponse());
                }
            }
        }
    }

    /**
     * @param AuthenticationEvent $e
     * @return HtmlResponse|RedirectResponse
     * @throws \Exception
     */
    public function authenticationPost(AuthenticationEvent $e)
    {
        $request = $e->getRequest();
        if ($request->getMethod() === 'POST') {
            $error = $e->getError();
            if (!empty($error)) {
                return $this->prgRedirect($e);
            }

            $result = $e->getAuthenticationResult();
            if ($result && $result->isValid()) {
                $uri = $this->routeHelper->getUri($this->options->getAfterLoginRoute());

                if ($this->options->isAllowRedirectParam()) {
                    $params = $e->getRequest()->getQueryParams();
                    $redirectParam = $this->options->getRedirectParamName();

                    if (isset($params[$redirectParam]) && !empty($params[$redirectParam])) {
                        $uri = new Uri(urldecode($params[$redirectParam]));
                    }
                }

                return new RedirectResponse($uri);
            }
        }

        return $this->renderTemplate($e);
    }

    protected function prgRedirect(AuthenticationEvent $e)
    {
        $request = $e->getRequest();
        $error = $e->getError();
        if (is_array($error) || is_string($error)) {
            $this->flashMessenger->addError($error);
        } elseif ($error instanceof \Exception) {
            $this->flashMessenger->addError($error->getMessage());
        } else {
            $this->flashMessenger->addError(
                $this->options->getMessagesOptions()->getMessage(MessagesOptions::AUTHENTICATION_FAIL_MESSAGE));
        }

        return new RedirectResponse($request->getUri(), 303);
    }

    /**
     * @param AuthenticationEvent $e
     * @return HtmlResponse
     */
    protected function renderTemplate(AuthenticationEvent $e)
    {
        return new HtmlResponse($this->template->render($this->options->getLoginTemplate(), $e->getParams()));
    }
}