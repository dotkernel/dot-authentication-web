<?php
/**
 * @see https://github.com/dotkernel/dot-authentication-web/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-authentication-web/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web\ErrorHandler;

use Dot\Authentication\AuthenticationInterface;
use Dot\Authentication\Exception\UnauthorizedException;
use Dot\Authentication\Web\Event\AuthenticationEvent;
use Dot\Authentication\Web\Event\AuthenticationEventListenerInterface;
use Dot\Authentication\Web\Event\AuthenticationEventListenerTrait;
use Dot\Authentication\Web\Event\DispatchAuthenticationEventTrait;
use Dot\Authentication\Web\Exception\RuntimeException;
use Dot\Authentication\Web\Options\MessagesOptions;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Helpers\Route\RouteHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\Uri;

/**
 * Class UnauthorizedHandler
 * @package Dot\Authentication\Web\ErrorHandler
 */
class UnauthorizedHandler implements MiddlewareInterface, AuthenticationEventListenerInterface
{
    use AuthenticationEventListenerTrait;
    use DispatchAuthenticationEventTrait;

    /** @var  AuthenticationInterface */
    protected $authenticationService;

    /** @var  WebAuthenticationOptions */
    protected $options;

    /** @var  RouteHelper */
    protected $routeHelper;

    /** @var  FlashMessengerInterface */
    protected $flashMessenger;

    /** @var array */
    protected $statusCodes = [401, 407];

    /** @var bool */
    protected $debug = false;

    /**
     * UnauthorizedHandler constructor.
     * @param AuthenticationInterface $authenticationService
     * @param RouteHelper $routeHelper
     * @param WebAuthenticationOptions $options
     * @param FlashMessengerInterface $flashMessenger
     */
    public function __construct(
        AuthenticationInterface $authenticationService,
        RouteHelper $routeHelper,
        WebAuthenticationOptions $options,
        FlashMessengerInterface $flashMessenger
    ) {
        $this->authenticationService = $authenticationService;
        $this->options = $options;
        $this->flashMessenger = $flashMessenger;
        $this->routeHelper = $routeHelper;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws \Exception
     * @throws \Throwable
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $response = $handler->handle($request);
            return $response;
        } catch (UnauthorizedException $e) {
            return $this->handleUnauthorizedError($e, $request);
        } catch (\Throwable $e) {
            if (in_array($e->getCode(), $this->statusCodes)) {
                return $this->handleUnauthorizedError($e, $request);
            }
            throw $e;
        } catch (\Exception $e) {
            if (in_array($e->getCode(), $this->statusCodes)) {
                return $this->handleUnauthorizedError($e, $request);
            }
            throw $e;
        }
    }

    /**
     * @param $error
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    protected function handleUnauthorizedError(
        $error,
        ServerRequestInterface $request
    ): ResponseInterface {
        $event = $this->dispatchEvent(AuthenticationEvent::EVENT_UNAUTHORIZED, [
            'request' => $request,
            'authenticationService' => $this->authenticationService,
            'error' => $error
        ]);
        if ($event instanceof ResponseInterface) {
            return $event;
        }

        $messages = $this->getErrorMessages($error);
        if (empty($messages)) {
            $messages = [$this->options->getMessagesOptions()->getMessage(MessagesOptions::UNAUTHORIZED)];
        }

        //add a flash message in case the login page displays errors
        if ($this->flashMessenger) {
            $this->flashMessenger->addError($messages);
        }

        /** @var Uri $uri */
        $uri = $this->routeHelper->generateUri($this->options->getLoginRoute());
        if ($this->routeHelper->uriEquals($uri, $request->getUri())) {
            throw new RuntimeException(
                'Default unauthorized listener has detected that the login route is not authorized either.' .
                ' This can result in an endless redirect loop. ' .
                'Please edit your  authorization schema to open login route to guests'
            );
        }
        if ($this->options->isEnableWantedUrl()) {
            $uri = $this->routeHelper->appendQueryParam(
                $uri,
                $this->options->getWantedUrlName(),
                $request->getUri()->__toString()
            );
        }

        return new RedirectResponse($uri);
    }

    /**
     * @param $error
     * @return array
     */
    protected function getErrorMessages($error): array
    {
        $messages = [];
        if (is_array($error) || is_string($error)) {
            $error = (array)$error;
            foreach ($error as $e) {
                if (is_string($e)) {
                    $messages[] = $e;
                }
            }
        } elseif ($error instanceof \Exception || $error instanceof \Throwable) {
            if ($this->isDebug() || $error instanceof UnauthorizedException) {
                $messages[] = $error->getMessage();
            }
        }
        return $messages;
    }

    /**
     * @return AuthenticationInterface
     */
    public function getAuthenticationService(): AuthenticationInterface
    {
        return $this->authenticationService;
    }

    /**
     * @param AuthenticationInterface $authenticationService
     */
    public function setAuthenticationService(AuthenticationInterface $authenticationService)
    {
        $this->authenticationService = $authenticationService;
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
