<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-authentication-web
 * @author: n3vrax
 * Date: 4/30/2016
 * Time: 10:45 PM
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
use Dot\Helpers\Route\RouteOptionHelper;
use Dot\Helpers\Route\UriHelperTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\Uri;

/**
 * Class UnauthorizedHandler
 * @package Dot\Authentication\Web\ErrorHandler
 */
class UnauthorizedHandler implements AuthenticationEventListenerInterface
{
    use AuthenticationEventListenerTrait;
    use DispatchAuthenticationEventTrait;
    use UriHelperTrait;

    /** @var  AuthenticationInterface */
    protected $authenticationService;

    /** @var  WebAuthenticationOptions */
    protected $options;

    /** @var  RouteOptionHelper */
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
     * @param RouteOptionHelper $routeHelper
     * @param WebAuthenticationOptions $options
     * @param FlashMessengerInterface $flashMessenger
     */
    public function __construct(
        AuthenticationInterface $authenticationService,
        RouteOptionHelper $routeHelper,
        WebAuthenticationOptions $options,
        FlashMessengerInterface $flashMessenger
    ) {
        $this->authenticationService = $authenticationService;
        $this->options = $options;
        $this->flashMessenger = $flashMessenger;
        $this->routeHelper = $routeHelper;
    }

    /**
     * @param $error
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable|null $next
     * @return ResponseInterface
     */
    public function __invoke(
        $error,
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next = null
    ): ResponseInterface {
        if ($error instanceof \Exception && in_array($error->getCode(), $this->statusCodes)
            || in_array($response->getStatusCode(), $this->statusCodes)
        ) {
            $event = $this->dispatchEvent(AuthenticationEvent::EVENT_UNAUTHORIZED, [
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
            $uri = $this->routeHelper->getUri($this->options->getLoginRoute());
            if ($this->areUriEqual($uri, $request->getUri())) {
                throw new RuntimeException(
                    'Default unauthorized listener has detected that the login route is not authorized either.' .
                    ' This can result in an endless redirect loop. ' .
                    'Please edit your  authorization schema to open login route to guests'
                );
            }
            if ($this->options->isEnableWantedUrl()) {
                $uri = $this->appendQueryParam(
                    $uri,
                    $this->options->getWantedUrlName(),
                    $request->getUri()->__toString()
                );
            }

            return new RedirectResponse($uri);
        }

        return $next($request, $response, $error);
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
        } elseif ($error instanceof \Exception) {
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

    /**
     * @param UriInterface $uri1
     * @param UriInterface $uri2
     * @return bool
     */
    protected function areUriEqual(UriInterface $uri1, UriInterface $uri2): bool
    {
        return $uri1->getScheme() === $uri2->getScheme()
            && $uri1->getHost() === $uri2->getHost()
            && $uri1->getPath() === $uri2->getPath()
            && $uri1->getPort() === $uri2->getPort();
    }
}
