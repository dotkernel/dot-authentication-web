# dot-authentication-web
This package provides a simple login/logout flow for web applications built on dot-authentication. It relies on events to do the authentication process offering in the same time flexibility for customization and further extension.

## Installation

Install the module by running the following command in your project root dir
```bash
$ composer install dotkernel/dot-authentication-web
```

Enable the module by merging its `ConfigProvider` output to your application's autoloaded configuration.
This registers all required dependencies, so you don't have to do it manually.

In addition to registering the dependencies, the ConfigProvider does the following:
* defines a login route and associates the LoginAction middleware to it
* defines a logout route and associates the LogoutAction middleware to it
* defines a piped error handling middleware `UnauthorizedHandler` to watch for Unauthorized exceptions or 401 responses
* configures the package with some sane defaults which can be overwritten

## Configuration

##### authentication-web.global.php
```php
return [
    //the same key as dot-authentication-service
    'dot_authentication' => [
        //dot-authentication-web config
        'web' => [
            //specifies to the module, the login and logout routes
            //these need to be changed only if routes where modified
            'login_route' => ['name' => 'login', 'params' => [], 'query_params' => []],
            'logout_route' => ['name' => 'logout', 'params' => [], 'query_params' => []],
            
            //template name to use for the login page
            'login_template' => 'app::login',
            
            //where to redirect after successfull login
            //can be specified as a string(route name) or an array
            'after_login_route' => 'home',
            
            //where to redirect after uses logs out
            'after_logout_route' => ['name' => 'login'],
            
            //enabled by default, check if wanted url is in query params 
            //and redirect to that instead if login successful
            'allow_redirect_param' => true,
            'redirect_param_name' => 'redirect',
            
            //for overwritting default module messages
            'messages_options' => [
                'messages' => [
                    //MessagesOptions::AUTHENTICATION_FAIL_MESSAGE => 'Authentication failed. Check your credentials and try again',
                    //MessagesOptions::UNAUTHORIZED_MESSAGE => 'You must be authenticated to access the requested content',
                ],
            ],
        ],
    
    ],
];
```

## Login flow

The authentication flow uses [zend-eventmanager](https://github.com/zendframework/zend-eventmanager). We advise you to check the official documentation before.

Calling the login route and subsequently the LoginAction middleware, an authentication event is triggered.
The actual authentication process happens on a listener registered at priority 1 defined in the listener aggregate `DefaultAuthenticationListener`
Please note the authentication event is triggered on both GET and POST requests. You should check in your listeners the request method before taking the appropriate action.

There is also a post authentication listener at priority -1000 that checks if there are errors and redirects back to the login page.
If authentication succeeded, it redirects to the after login route or the wanted url.

You can come with your own listeners to further extend the functionality or even completely rewrite the authentication process.


## Logout flow

Calling LogoutAction middleware, similar to login, it triggers a logout event. We provide a default logout listeners that uses an AuthenticationInterface service to clear the identity from storage.
It also redirects to the after logout route as configured. Again, you can register your own listeners for this event to do additional actions when users log out.

## Unauthorized exception handling

A piped error handler middleware is provided to catch UnauthorizedException or any Exception or response that has a 401 code.
In the same vein as login/logout, the unauthorized handler does not process the exception, it delegates instead responsibility to listeners by triggering an unauthorized event.

The default unauthorized listener process the authentication error messages, setting them as session messages(flash messages) and redirects back to the login route, optionally appending the wanted url to the query.


## AuthenticationEvent

Triggered on login, logout and unauthorized actions, it holds identity information, authentication result and also the authentication service, and current errors, depending on the authentication stage.
Defines 3 types of authentication events
* `AuthenticationEvent::EVENT_AUTHENTICATION_AUTHENTICATE` - triggered when authentication is needed
* `AuthenticationEvent::EVENT_AUTHENTICATION_LOGOUT` - triggered when logout is needed
* `AuthenticationEvent::EVENT_AUTHENTICATION_UNAUTHORIZED` - triggered when an UnauthorizedException or 401 codes are detected

The package provides default listeners for all these events, in order to provide just the basic functionality of a web authentication flow.


## Useful observations

* The default authentication listener skips if the AuthenticationEvent errors property is not empty. This allows you to have pre authentication listeners to make additional validations for example.
* The AuthenticationEvent->getParams() are sent to the login template, so you can inject your own variables into the template(like the login form, for example)
* If you have listeners that return a ResponseInterface, you basically interrupt the listener chain. You could use this to completely rewrite the authentication flow if needed, by registering listeners before all the default ones that are provided.