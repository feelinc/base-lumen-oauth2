## Implementation of OAuth 2 Server in Lumen PHP Framework using league/oauth2-server

[![Build Status](https://travis-ci.org/laravel/lumen-framework.svg)](https://travis-ci.org/laravel/lumen-framework)
[![Total Downloads](https://poser.pugx.org/laravel/lumen-framework/d/total.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/lumen-framework/v/stable.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Unstable Version](https://poser.pugx.org/laravel/lumen-framework/v/unstable.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![License](https://poser.pugx.org/laravel/lumen-framework/license.svg)](https://packagist.org/packages/laravel/lumen-framework)

Laravel Lumen is a stunningly fast PHP micro-framework for building web applications with expressive, elegant syntax. Documentation for the framework can be found on the [Lumen website](http://lumen.laravel.com/docs).

Lumen PHP Framework version 5.1.*

## About

This is simple example of how to use [league/oauth2-serve](https://github.com/thephpleague/oauth2-server) inside Lumen PHP Framework.

#### Grants Implemented:
1. Client Credentials
2. Authorization Code
3. Refresh Token

Authorization Code grant is automatically executed after logging in a user in this implementation, so you will not have any authorization approval user interface.

You can modify the Authorization Code grant implementation or remove it by modifying the [UserController](https://github.com/feelinc/base-lumen-oauth2/blob/master/app/Http/Controllers/v1/UserController.php) file. You can find [OAuth2 trait](https://github.com/feelinc/base-lumen-oauth2/blob/master/app/Http/Controllers/OAuth2.php) usage there. But remember, you need to figure out by your self on how to identify authenticated User by removing the implementation.

## Installation

Run composer install from the command line
```
composer install
```

## Tables Schema

Import [SQL table schema](https://github.com/feelinc/base-lumen-oauth2/blob/master/Schema.sql) into your database.


## Testing

Import [Postman JSON file](https://github.com/feelinc/base-lumen-oauth2/blob/master/Lumen_OAuth2.json.postman_collection) into your Postman application.

#### Steps
1. Change all URL endpoints based on your installation.
2. Run the "Authorization".
3. Copy access_token value into the "User Login" Authorization header, then run it.
4. Copy refresh_token value into the "Refresh Token" body, then run it.
5. Copy access_token value into the "Get a User" Authorization header, then run it.

## Middlewares
#### 1. OAuthMiddleware
This middleware will identify Client based on access_token passed to the request, then it will save the identified OAuth info (client, user, and scopes) into request object.

#### 2. AuthMiddleware
This middleware will identify authenticated User based on identified OAuth info, so you need to put OAuthMiddleware in the first place of you route middleware definition before this middleware.

## Issue
Submit your issue in [here](https://github.com/feelinc/base-lumen-oauth2/issues).

### License

Whole additional source codes included is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
