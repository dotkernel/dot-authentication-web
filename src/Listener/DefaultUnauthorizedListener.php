<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-authentication-web
 * @author: n3vrax
 * Date: 6/17/2016
 * Time: 7:50 PM
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web\Listener;

use Dot\Authentication\Exception\UnauthorizedException;
use Dot\Authentication\Web\Event\AuthenticationEvent;
use Dot\Authentication\Web\Exception\RuntimeException;
use Dot\Authentication\Web\Options\MessagesOptions;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Helpers\Route\RouteOptionHelper;
use Dot\Helpers\Route\UriHelperTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
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

    /** @var bool */
    protected $debug = false;

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
    ) {
        $this->routeHelper = $routeHelper;
        $this->options = $options;
        $this->flashMessenger = $flashMessenger;
    }

    /**
     * @param AuthenticationEvent $e
     * @return ResponseInterface
     * @throws \Exception
     */
    public function __invoke(AuthenticationEvent $e): ResponseInterface
    {
        $request = $e->getRequest();

        $messages = $this->getErrorMessages($e);
        if (empty($messages)) {
            $messages = [$this->options->getMessagesOptions()->getMessage(MessagesOptions::UNAUTHORIZED_MESSAGE)];
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
            $uri = $this->appendQueryParam($uri, $this->options->getWantedUrlName(), $request->getUri()->__toString());
        }

        return new RedirectResponse($uri);
    }

    /**
     * @param AuthenticationEvent $e
     * @return array
     */
    protected function getErrorMessages(AuthenticationEvent $e): array
    {
        $messages = [];
        $error = $e->getError();
        if (is_array($error)) {
            foreach ($error as $e) {
                if (is_string($e)) {
                    $messages[] = $e;
                }
            }
        } elseif (is_string($error)) {
            $messages[] = $error;
        } elseif ($error instanceof \Exception) {
            if ($this->isDebug() || $error instanceof UnauthorizedException) {
                $messages[] = $error->getMessage();
            }
        }
        return $messages;
    }

    /**
     * @return boolean
     */
    public function isDebug(): bool
    {
        return $this->debug;
    }

    /**
     * @param boolean $debug
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
