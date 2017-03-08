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
    'dot_authentication' => [
        //this package's specific configuration template
        'web' => [
            //change next two only if you changed the default login/logout routes
            'login_route' => ['route_name' => 'login', 'route_params' => [], 'query_params' => []],
            'logout_route' => ['route_name' => 'logout', 'route_params' => []],
            
            //template name to use for the login form
            'login_template' => 'app::login',
            
            //where to redirect after login success
            'after_login_route' => ['route_name' => 'my-account', 'route_params' => []],
            //where to redirect after logging out
            'after_logout_route' => ['route_name' => 'login', 'route_params' => []],
            
            //enable the wanted url feature, to login to the previously requested uri after login
            'enable_wanted_url' => true,
            'wanted_url_name' => 'redirect',
            
            'event_listeners' => [
                [
                    'type' => 'Some\Class\Or\Service',
                    'priority' => 1
                ],
            ],
            
            //for overwriting default module messages
            'messages_options' => [
                'messages' => [
                    //MessagesOptions::AUTHENTICATION_FAILURE =>
                    //    'Authentication failed. Check your credentials and try again',
                    
                    //MessagesOptions::AUTHENTICATION_INVALID_CREDENTIALS =>
                    //    'Authentication failed. Check your credentials and try again',
                    
                    //MessagesOptions::AUTHENTICATION_IDENTITY_AMBIGUOUS =>
                    //    'Authentication failed. Check your credentials and try again',
                    
                    //MessagesOptions::AUTHENTICATION_IDENTITY_NOT_FOUND =>
                    //   'Authentication failed. Check your credentials and try again',
                    
                    //MessagesOptions::AUTHENTICATION_UNCATEGORIZED =>
                    //    'Authentication failed. Check your credentials and try again',
                    
                    //MessagesOptions::AUTHENTICATION_MISSING_CREDENTIALS =>
                    //    'Authentication failed. Missing or invalid credentials',
                    
                    //MessagesOptions::AUTHENTICATION_SUCCESS =>
                    //    'Welcome! You have successfully signed in',
                    
                    //MessagesOptions::AUTHENTICATION_FAIL_UNKNOWN =>
                    //    'Authentication failed. Check your credentials and try again',
                    
                    //MessagesOptions::UNAUTHORIZED => 'You must sign in first to access the requested content',
                ],
            ],
        ]
    ]
];
```

## Login flow

@TODO: write full documentation for new version
