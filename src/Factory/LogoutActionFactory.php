<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 4/30/2016
 * Time: 8:40 PM
 */

namespace Dot\Authentication\Web\Factory;

use Dot\Authentication\Web\Action\LogoutAction;
use Dot\Authentication\Web\Event\AuthenticationEvent;
use Dot\Authentication\Web\Listener\DefaultLogoutListener;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Interop\Container\ContainerInterface;
use N3vrax\DkAuthentication\AuthenticationInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\Expressive\Helper\UrlHelper;

class LogoutActionFactory
{
    /**
     * @param ContainerInterface $container
     * @return LogoutAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $eventManager = $container->has(EventManagerInterface::class)
            ? $container->get(EventManagerInterface::class)
            : new EventManager();

        $event = new AuthenticationEvent();

        $defaultListeners = $container->get(DefaultLogoutListener::class);
        $defaultListeners->attach($eventManager);

        $authentication = $container->get(AuthenticationInterface::class);
        $event->setAuthenticationService($authentication);

        $action = new LogoutAction(
            $container->get(AuthenticationInterface::class),
            $container->get(UrlHelper::class),
            $container->get(WebAuthenticationOptions::class)
        );
        $action->setEvent($event);
        $action->setEventManager($eventManager);

        return $action;
    }
}