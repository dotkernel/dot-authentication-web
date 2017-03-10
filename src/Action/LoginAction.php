<?php
/**
 * @see https://github.com/dotkernel/dot-authentication-web/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-authentication-web/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web\Action;

use Dot\Authentication\AuthenticationInterface;
use Dot\Authentication\AuthenticationResult;
use Dot\Authentication\Web\Event\AuthenticationEvent;
use Dot\Authentication\Web\Event\AuthenticationEventListenerInterface;
use Dot\Authentication\Web\Event\AuthenticationEventListenerTrait;
use Dot\Authentication\Web\Event\DispatchAuthenticationEventTrait;
use Dot\Authentication\Web\Exception\RuntimeException;
use Dot\Authentication\Web\Options\MessagesOptions;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Dot\Authentication\Web\Utils;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Helpers\Route\RouteOptionHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\Uri;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class LoginAction
 * @package Dot\Authentication\Web\Action
 */
class LoginAction implements AuthenticationEventListenerInterface
{
    use DispatchAuthenticationEventTrait;
    use AuthenticationEventListenerTrait;

    /** @var  AuthenticationInterface */
    protected $authentication;

    /** @var  RouteOptionHelper */
    protected $routeHelper;

    /** @var  WebAuthenticationOptions */
    protected $options;

    /** @var  FlashMessengerInterface */
    protected $flashMessenger;

    /** @var  TemplateRendererInterface */
    protected $template;

    /** @var  ServerRequestInterface */
    protected $request;

    /** @var bool */
    protected $debug = false;

    /**
     * LoginAction constructor.
     * @param AuthenticationInterface $authentication
     * @param TemplateRendererInterface $template
     * @param RouteOptionHelper $routeHelper
     * @param WebAuthenticationOptions $options
     * @param FlashMessengerInterface $flashMessenger
     */
    public function __construct(
        AuthenticationInterface $authentication,
        TemplateRendererInterface $template,
        RouteOptionHelper $routeHelper,
        WebAuthenticationOptions $options,
        FlashMessengerInterface $flashMessenger
    ) {
        $this->authentication = $authentication;
        $this->options = $options;
        $this->routeHelper = $routeHelper;
        $this->flashMessenger = $flashMessenger;
        $this->template = $template;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable|null $next
     * @return ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next = null
    ): ResponseInterface {
        if ($this->authentication->hasIdentity()) {
            return new RedirectResponse($this->routeHelper->getUri($this->options->getAfterLoginRoute()));
        }

        $this->request = $request;

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $event = $this->dispatchEvent(AuthenticationEvent::EVENT_BEFORE_AUTHENTICATION, [
                'request' => $request,
                'authenticationService' => $this->authentication,
                'data' => $data
            ]);

            if ($event instanceof ResponseInterface) {
                return $event;
            }

            $error = $event->getParam('error', null);
            // get the request in case someone changed it
            $request = $event->getParam('request');
            $this->request = $request;
            if (empty($error)) {
                $result = $this->authentication->authenticate($request);
                //we get this in case authentication skipped(due to missing credentials in request)
                //but for web application, we want to force implemetors to prepare their auth adapter first
                //so we throw an exception to be clear developers have missed something
                if ($result->getCode() === AuthenticationResult::FAILURE_MISSING_CREDENTIALS) {
                    throw new RuntimeException('Authentication service could not authenticate request. ' .
                        'Have you forgot to prepare the request first according to authentication adapter needs?');
                }

                $params = $event->getParams();
                $params += [
                    'authenticationResult' => $result
                ];

                if ($result->isValid()) {
                    $params += [
                        'identity' => $result->getIdentity()
                    ];
                    $event = $this->dispatchEvent(AuthenticationEvent::EVENT_AFTER_AUTHENTICATION, $params);
                    if ($event instanceof ResponseInterface) {
                        return $event;
                    }

                    $error = $event->getParam('error');
                    if (empty($error)) {
                        $this->dispatchEvent(AuthenticationEvent::EVENT_AUTHENTICATION_SUCCESS, $params);

                        $uri = $this->routeHelper->getUri($this->options->getAfterLoginRoute());
                        if ($this->options->isEnableWantedUrl()) {
                            $params = $request->getQueryParams();
                            $wantedUrlName = $this->options->getWantedUrlName();

                            if (isset($params[$wantedUrlName]) && !empty($params[$wantedUrlName])) {
                                $uri = new Uri(urldecode($params[$wantedUrlName]));
                            }
                        }
                        return new RedirectResponse($uri);
                    } else {
                        $this->dispatchEvent(AuthenticationEvent::EVENT_AUTHENTICATION_ERROR, $event->getParams());
                        return $this->prgRedirect($error);
                    }
                } else {
                    $message = $this->options->getMessagesOptions()
                        ->getMessage(Utils::$authResultCodeToMessageMap[$result->getCode()]);
                    $params += [
                        'error' => $message
                    ];
                    $this->dispatchEvent(AuthenticationEvent::EVENT_AUTHENTICATION_ERROR, $params);
                    return $this->prgRedirect($message);
                }
            } else {
                $this->dispatchEvent(AuthenticationEvent::EVENT_AUTHENTICATION_ERROR, $event->getParams());
                return $this->prgRedirect($error);
            }
        }

        $event = $this->dispatchEvent(AuthenticationEvent::EVENT_AUTHENTICATION_BEFORE_RENDER, [
            'request' => $request,
            'authenticationService' => $this->authentication,
            'template' => $this->options->getLoginTemplate()
        ]);
        if ($event instanceof ResponseInterface) {
            return $event;
        }

        $template = $event->getParam('template');
        $params = $event->getParams();
        unset($params['template']);

        return new HtmlResponse($this->template->render($template, $params));
    }

    /**
     * @param $error
     * @return ResponseInterface
     */
    protected function prgRedirect($error): ResponseInterface
    {
        if (is_array($error) || is_string($error)) {
            $this->flashMessenger->addError($error);
        } elseif ($error instanceof \Exception && $this->isDebug()) {
            $this->flashMessenger->addError($error->getMessage());
        } else {
            $this->flashMessenger->addError(
                $this->options->getMessagesOptions()
                    ->getMessage(MessagesOptions::AUTHENTICATION_FAIL_UNKNOWN)
            );
        }
        return new RedirectResponse($this->request->getUri(), 303);
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
}
