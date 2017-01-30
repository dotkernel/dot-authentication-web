<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-authentication-web
 * @author: n3vrax
 * Date: 6/17/2016
 * Time: 4:24 PM
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web\Listener;

use Dot\Authentication\AuthenticationInterface;
use Dot\Authentication\AuthenticationResult;
use Dot\Authentication\Web\Event\AuthenticationEvent;
use Dot\Authentication\Web\Exception\RuntimeException;
use Dot\Authentication\Web\Options\MessagesOptions;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Dot\Authentication\Web\Utils;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Helpers\Route\RouteOptionHelper;
use Psr\Http\Message\ResponseInterface;
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
    protected $webAuthOptions;

    /** @var  FlashMessengerInterface */
    protected $flashMessenger;

    /** @var  bool */
    protected $debug = false;

    /**
     * DefaultAuthenticationListener constructor.
     * @param AuthenticationInterface $authentication
     * @param TemplateRendererInterface $template
     * @param RouteOptionHelper $routeHelper
     * @param FlashMessengerInterface $flashMessenger
     * @param WebAuthenticationOptions $webAuthOptions
     */
    public function __construct(
        AuthenticationInterface $authentication,
        TemplateRendererInterface $template,
        RouteOptionHelper $routeHelper,
        FlashMessengerInterface $flashMessenger,
        WebAuthenticationOptions $webAuthOptions
    ) {
        $this->authentication = $authentication;
        $this->routeHelper = $routeHelper;
        $this->template = $template;
        $this->webAuthOptions = $webAuthOptions;
        $this->flashMessenger = $flashMessenger;
    }

    /**
     * @param EventManagerInterface $events
     * @param int $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1): void
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
    public function prepare(AuthenticationEvent $e): void
    {
        //nothing to prepare for now, let it to implementors
    }

    /**
     * @param AuthenticationEvent $e
     */
    public function authenticate(AuthenticationEvent $e): void
    {
        $request = $e->getRequest();
        $error = $e->getError();

        if ($request->getMethod() === 'POST' && empty($error)) {
            $result = $this->authentication->authenticate($request);
            //we get this in case authentication skipped(due to missing credentials in request)
            //but for web application, we want to force implemetors to prepare their auth adapter first
            //so we throw an exception to be clear developers have missed something
            if ($result->getCode() === AuthenticationResult::FAILURE_MISSING_CREDENTIALS) {
                throw new RuntimeException('Authentication service could not authenticate request. ' .
                    'Have you forgot to prepare the request first according to authentication adapter needs?');
            }

            $e->setAuthenticationResult($result);

            if ($result->isValid()) {
                $e->setIdentity($result->getIdentity());
            } else {
                $e->setError(
                    $this->webAuthOptions->getMessagesOptions()
                        ->getMessage(Utils::$authResultCodeToMessageMap[$result->getCode()])
                );
            }
        }
    }

    /**
     * @param AuthenticationEvent $e
     * @return ResponseInterface
     * @throws \Exception
     */
    public function authenticationPost(AuthenticationEvent $e): ResponseInterface
    {
        $request = $e->getRequest();

        if ($request->getMethod() === 'POST') {
            $result = $e->getAuthenticationResult();
            if ($result->isValid()) {
                $uri = $this->routeHelper->getUri($this->webAuthOptions->getAfterLoginRoute());

                if ($this->webAuthOptions->isEnableWantedUrl()) {
                    $params = $request->getQueryParams();
                    $wantedUrlName = $this->webAuthOptions->getWantedUrlName();

                    if (isset($params[$wantedUrlName]) && !empty($params[$wantedUrlName])) {
                        $uri = new Uri(urldecode($params[$wantedUrlName]));
                    }
                }

                return new RedirectResponse($uri);
            }

            //in this point, authentication result is not valid, redirect back to login with error
            return $this->prgRedirect($e);
        }

        return $this->renderTemplate($e);
    }

    /**
     * @param AuthenticationEvent $e
     * @return ResponseInterface
     */
    protected function prgRedirect(AuthenticationEvent $e): ResponseInterface
    {
        $request = $e->getRequest();
        $error = $e->getError();

        if (is_array($error) || is_string($error)) {
            $this->flashMessenger->addError($error);
        } elseif ($error instanceof \Exception && $this->isDebug()) {
            $this->flashMessenger->addError($error->getMessage());
        } else {
            $this->flashMessenger->addError(
                $this->webAuthOptions->getMessagesOptions()
                    ->getMessage(MessagesOptions::AUTHENTICATION_FAIL_UNKNOWN)
            );
        }

        return new RedirectResponse($request->getUri(), 303);
    }

    /**
     * @return bool
     */
    public function isDebug(): bool
    {
        return $this->debug;
    }

    /**
     * @param bool $debug
     */
    public function setDebug(bool $debug)
    {
        $this->debug = $debug;
    }

    /**
     * @param AuthenticationEvent $e
     * @return ResponseInterface
     */
    protected function renderTemplate(AuthenticationEvent $e): ResponseInterface
    {
        return new HtmlResponse(
            $this->template->render(
                $this->webAuthOptions->getLoginTemplate(),
                $e->getParams()
            )
        );
    }
}
