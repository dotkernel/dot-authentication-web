![OSS Lifecycle](https://img.shields.io/osslifecycle/dotkernel/dot-authentication-web)
[![GitHub license](https://img.shields.io/github/license/dotkernel/dot-authentication-web)](https://github.com/dotkernel/dot-authentication-web/blob/2.0/LICENSE.md)
![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-authentication-web/2.9.0)

## Note

> **dot-authentication-web** is in **maintenance** mode.

> This package is considered feature-complete, and is now in **security-only** maintenance mode.

# dot-authentication-web
This package provides a simple login/logout flow for web applications built on top of dot-authentication. It relies on events to do the authentication process offering in the same time flexibility for customization and further extension.

## Installation

Install the package by running the following command in your project root directory
```bash
$ composer install dotkernel/dot-authentication-web
```

Enable the module by merging its `ConfigProvider` output to your application's autoloaded configuration.
This registers all required dependencies, so you don't have to do it manually.

## Configuration and usage

First of all, `LoginAction`, `LogoutAction`, and `UnauthorizedHandler` must be registered in the middleware stack. The first two are routed middleware, and the last one is an error handler(still a middleware) that handles the `\Dot\Authentication\Exception\UnauthorizedException`.

##### routes.php
* register the routed middleware login and logout actions.
```php
$app->route('/user/login', LoginAction::class, ['GET', 'POST'], 'login');
$app->route('/user/logout', LogoutAction::class, ['GET'], 'logout');
```

* register the error handler as early as possible to catch all possible UnauthorizedException comming from routed middleware. Usually, you can register this between the routing and dispatching middleware, if you need the access to the RouteResult, just make sure it will catch all UnauthorizedException. DotKernel applications by default, registers this middleware right after the routing middleware.
##### pipeline.php
```php
$app->pipeRoutingMiddleware();
//...
$app->pipe(UnauthorizedHandler::class);
```

Here is an example configuration for this module, you can put this in `config/autoload` or in a `ConfigProvider` in your project. It is based on the above configured middleware
##### authentication-web.global.php
```php
return [
    'dot_authentication' => [
        'web' => [
            // login/logout route definitions, as configured in the expressive router
            'login_route' => ['route_name' => 'login', 'route_params' => []],
            'logout_route' => ['route_name' => 'logout', 'route_params' => []],
            
            //template name to use for the login form
            'login_template' => 'app::login',
            
            //where to redirect after login success
            'after_login_route' => ['route_name' => 'account', 'route_params' => []],
            
            //where to redirect after logging out
            'after_logout_route' => ['route_name' => 'login', 'route_params' => []],
            
            //enable the wanted url feature, to go to the previously requested uri after login
            'enable_wanted_url' => true,
            'wanted_url_name' => 'redirect',
            
            // event listeners for authentication, logout and unauthorized events
            'event_listeners' => [
                [
                    'type' => 'Some\Class\Or\Service',
                    'priority' => 1
                ],
            ],
            
            //for overwriting default module messages
            'messages_options' => [
                'messages' => [
                    // see \Dot\Authentication\Web\Options\MessageOptions class
                ],
            ],
        ]
    ]
];
```

## Login flow

Happens in the `LoginAction` middleware. On a GET request, it renders the HTML template configured as above, at `login_template` configuration key. The login process happens on POST requests.
The login page should display a login form, with its action going back to the login route via method POST. Note that the LoginAction middleware on its own, does not know about any login form, nor does validate the POST data. It alows customization though, through before and after authentication events, which will see later.

It uses the authentication service to authenticate the request. Depending on the authentication service implementation, additional actions might be needed before, which can be done in pre-authentication event. In case you use [dot-authentication-service](https://github.com/dotkernel/dot-authentication-service), along with the CallbackCheck adapter, the request should be injected beforehand with a `DbCredential` object attribute for example.

If any error occur, the middleware will do a PRG redirect to the login route, using the flash messenger(see [dot-flashmessenger](https://github.com/dotkernel/dot-flashmessenger)) to set a session error message which you can display in the login template.

In case authentication is successful, it will trigger the after authentication event, and will redirect to the `after_login_route` as configured. If it detects a `wanted_url` in the query parameters, it will redirect there instead. This is useful if the application redirected to the login page due to an unauthorized exception, setting the wanted url. After successful login, user will be redirected to the desired original page.

### Authentication events

An authentication event, be it login, logout or unauthorized event, is represented by the `AuthenticationEvent` class. The events regarding strictly the authentication process are

```php
class AuthenticationEvent extends Event
{
    const EVENT_BEFORE_AUTHENTICATION = 'event.beforeAuthentication';
    const EVENT_AFTER_AUTHENTICATION = 'event.afterAuthentication';
    const EVENT_AUTHENTICATION_SUCCESS = 'event.authenticationSuccess';
    const EVENT_AUTHENTICATION_ERROR = 'event.authenticationError';
    const EVENT_AUTHENTICATION_BEFORE_RENDER = 'event.authenticationBeforeRender';
    
    //...
```

##### AuthenticationEvent::EVENT_BEFORE_AUTHENTICATION
* `AuthenticationEvent::EVENT_BEFORE_AUTHENTICATION` - is triggered right before sending a POST to the LoginAction. It allows you to do any pre authentication actions, like preparing the credentials, additional validation of the POST data. You can even return a ResponseInterface, to stop the default authentication process and return your response instead.
* these event object holds the following parameters
    * `request` - the current `ServerRequestInterface` object
    * `authenticationService` - the configured `AuthenticationInterface` service implementation
    * `data` - the original parsed POST data, as retrieved by `$request->getParsedBody();`

* things you can do by listening to this event
    * extract information from the request, and also add information to it via attributes. The `LoginAction` will use the request parameter from the event object, so you can change it in your listeners.
    * validate data, for example if you use input filters. In order to trigger an error from within the listener, add an `error` parameter to the event object. The `LoginAction` will check if the error is present, before going forward. Otherwise it will redirect back to the login route, with the error message set in the flash messenger.
    * this might rarely be used, but you can also return a ResponseInterface, in which case the event listener chain will be stopped. The `LoginAction` will return any ResponseInterface received as is to the client.

##### AuthenticationEvent::EVENT_AFTER_AUTHENTICATION
* `AuthenticationEvent::EVENT_AFTER_AUTHENTICATION` is triggered right after the authentication service returns the `AuthenticationResult` object with a valid flag. This event marks then, that the authentication was successfull with the authentication service, and allows you to do any post authentication actions. At this point, authentication should not be considered completed. The reason is that any listener of this event can further validate the identity. To give you an example, it could check if the identity's status is allowed, and can further proceed to automatically logout the user if not so. That's why there is another event that triggers after this one, which you'll see next.
* the event object holds the following paramters
    * the ones used in the before authentication event + any parameter set by its listeners
    * `authenticationResult` - the `AuthenticationResult` returned by the authentication service
    * `identity` - the authenticated identity object
* things you can do by listening to this event
    * further validate the user identity, by checking account status etc.
    * logging authentication
    * return a ResponseInterface that will stop the event listeners chain and will be returned as-is
    * inject an `error` parameter into the event object, that will be checked by the `LoginAction` similar as to what was described for before authentication event

##### AuthenticationEvent::EVENT_AUTHENTICATION_SUCCESS
* by the time this event is triggered you can be sure the authentiation is completed and successfull. Listening to this event might be helpful for logging reasons. After this, the page will be redirected to the after login route, or the wanted url if it is the case.

##### AuthenticationEvent::EVENT_AUTHENTICATION_ERROR
* triggered on any authentication error status, like failed validation, or errors set by other event listeners. After event triggers, it will imediately do a PRG redirect back to the login page, to display the error messages. In this case, you can only listen for the event, but returning a ResponseInterface will not affect the authentication flow(could change in the future if it will be considered useful). You can mainly listen for this event for logging purposes.

##### AuthenticationEvent::EVENT_AUTHENTICATION_BEFORE_RENDER
* this is an event that could not be considered part of the authentication process. It is triggered right before displaying the login page(template rendering), to let you change the response or inject variables into the template.
* the parameters that you get in the event object are
    * `request` - the server request object
    * `authenticationService` - authentication service implementation
    * `template` - the login template name, as defined in the configuration

* the reasons you might want to listen to this event are
    * change the template name. Just listen for the event, replace the `template` event parameter with your own value, and it will be considered when rendering the page.
    * any additional named parameter set in the event object will be eventually injected as template data. So this is a good place for example to inject the login form object into the template to be displayed. This is how frontend and admin application are done for example.

As you can see, listening to authentication events allows you to inject additional logic into the login process. It also allows you to do it in a more decoupled way. For a full understanding of the entire process, make sure to check the `LoginAction` class. You can also find the frontend and admin applications useful, as they already provide some customization. Check the corresponding authentication event listeners defined there, for a sample of what you can achieve through listeners.

## Logout flow

The logout process is much simpler. It triggers 2 events: after and before logout. In between, the authenticated identity is cleared using the `clearIdentity()` method of the authentication service. After that, the client is redirected the the configured `after_logout_route`.

### Logout events

##### AuthenticationEvent::EVENT_BEFORE_LOGOUT
* triggered before clearing the identity, it can be used for logging reasons, customize the logout process. Returning a ResponseInterface from any of the event listeners, will stop the chain, and that response will be returned directly to the client. Please note, that doing that will stop the predefined logout process to happen, meaning that the identity will not be cleared and the after logout event will not be triggered. You'll need to make sure you do this in your event listeners if you choose to return your own response.

##### AuthenticationEvent::EVENT_AFTER_LOGOUT
* happens right after the identity was cleared. At this point, you can assume the user was logged out. Could be used for logging purposes, or any other post-logout actions. Returning a response object will not be considered here, instead the client will be redirected to the after logout route.

## UnauthorizedException handling

Mezzio error handlers are middleware that wraps the response in a try-catch block. They are registered early in the pipeline, in order to get all possible exceptions. This package's UnauthorizedHandler handles the following exceptions
* UnauthorizedException - the native authentication defined exception
* Throwable or Exception types that have an exception code 401(http unauthorized)

For any other kind of exceptions, it re-throws them in order to be handles by other error handlers.

### Events

When an unauthorized exception is catched, the following steps are followed by the error handler
* trigger an unauthorized event
* creates an error message from the error object/exception recevied. Note that will take into consideration the debug flag
* it redirect to the configured login route, setting the error message in the flash messenger. It will also append a wanted url as a GET parameter, if this is enabled, so that after successful authentication, the user will be redirected back to the requested page.

##### AuthenticationEvent::EVENT_UNAUTHORIZED
* is triggered right after catching an unauthorized exception. The event object will carry the following parameters
    * `request` - the server request object
    * `authenticationService` - the authentication service implementation
    * `error` - the error object/exception as catched by the handler

You can listen to this event mainly for logging purposes or additional actions after this kind of exception. You can also return a ResponseInterface from one of the event listeners(the event chain will stop), in which case, that response will be returned to the client as-is, basically overwriting the entire error handling process.

## Writing an authentication listener

Authentication listeners must implement `AuthenticationEventListenerInterface`, an interface that defines all possible event method handlers. You should also extend the `AbstractAuthenticationEventListener` or use the `AuthenticationEventListenerTrait` which are already supporting the event attach methods. They also implement the event listener interface, by providing empty interface methods. This helps when writing your event listener, as you may want to listen to only some of the events. This will let you implement just the event handler methods that you are interested in.

##### AuthenticationEventListenerInterface.php
```php
// the authentication event listener interface defined in this package
interface AuthenticationEventListenerInterface extends ListenerAggregateInterface
{
    public function onBeforeAuthentication(AuthenticationEvent $e);

    public function onAfterAuthentication(AuthenticationEvent $e);

    public function onAuthenticationSuccess(AuthenticationEvent $e);

    public function onAuthenticationError(AuthenticationEvent $e);

    public function onAuthenticationBeforeRender(AuthenticationEvent $e);

    public function onBeforeLogout(AuthenticationEvent $e);

    public function onAfterLogout(AuthenticationEvent $e);

    public function onUnauthorized(AuthenticationEvent $e);
}
```

##### MyAuthenticationEventListener.php
```php
//...
class MyAuthenticationEventListener extends AbstractAuthenticationEventListener
{
    public function onBeforeAuthentication(AuthenticationEvent $e)
    {
        // do something...
    }
    
    // other event handlers methods
}
```

* register the event listener. You can do this multiple ways: use a delegator factory on the LoginAction, LogoutAction or UnauthorizedHandler. We also provide a more convenient way of attaching your event listeners - through configuration. You can provide a list of authentication event listeners, as class names or service names, which will be attached to all types of authentication events

```php
return [
    'dot_authentication' => [
        'web' => [
            //...
            
            // event listeners for authentication, logout and unauthorized events
            'event_listeners' => [
                [
                    'type' => MyAuthenticationEventListener::class,
                    'priority' => 1
                ],
            ],
            
           //....
        ]
    ]
];
```

