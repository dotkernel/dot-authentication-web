<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 4/30/2016
 * Time: 8:40 PM
 */

namespace Dot\Authentication\Web\Factory;

use Dot\Authentication\Web\Action\LoginAction;
use Dot\Authentication\Web\Event\AuthenticationEvent;
use Dot\Authentication\Web\Listener\DefaultAuthenticationListener;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Interop\Container\ContainerInterface;
use N3vrax\DkAuthentication\AuthenticationInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\Expressive\Helper\UrlHelper;

class LoginActionFactory
{
    /**
     * @param ContainerInterface $container
     * @return LoginAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $eventManager = $container->has(EventManagerInterface::class)
            ? $container->get(EventManagerInterface::class)
            : new EventManager();

        /** @var DefaultAuthenticationListener $defaultListeners */
        $defaultListeners = $container->get(DefaultAuthenticationListener::class);
        $defaultListeners->attach($eventManager);

        $authentication = $container->get(AuthenticationInterface::class);
        $event = new AuthenticationEvent();
        $event->setAuthenticationService($authentication);

        $action = new LoginAction(
            $authentication,
            $container->get(WebAuthenticationOptions::class),
            $container->get(UrlHelper::class)
        );
        
        $action->setEvent($event);
        $action->setEventManager($eventManager);

        return $action;
    }
}