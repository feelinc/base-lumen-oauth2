## Implementation of OAuth 2 Server in Lumen PHP Framework using [league/oauth2-server](https://github.com/thephpleague/oauth2-server)

[![Build Status](https://travis-ci.org/laravel/lumen-framework.svg)](https://travis-ci.org/laravel/lumen-framework)
[![Total Downloads](https://poser.pugx.org/laravel/lumen-framework/d/total.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/lumen-framework/v/stable.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Unstable Version](https://poser.pugx.org/laravel/lumen-framework/v/unstable.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![License](https://poser.pugx.org/laravel/lumen-framework/license.svg)](https://packagist.org/packages/laravel/lumen-framework)

Laravel Lumen is a stunningly fast PHP micro-framework for building web applications with expressive, elegant syntax. Documentation for the framework can be found on the [Lumen website](http://lumen.laravel.com/docs).

Lumen PHP Framework version 5.1.*

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


## Issue
Submit your issue in [here](https://github.com/feelinc/base-lumen-oauth2/issues).

### License

Whole additional source codes included is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
