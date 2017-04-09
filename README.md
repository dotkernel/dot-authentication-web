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


