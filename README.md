# SocialProof - MusicData Fork

## What is SocialProof?

SocialProof is a [fluent interface](https://en.wikipedia.org/wiki/Fluent_interface) for fetching followers / fans from various social media platforms using their internal API. It handles all API requests asynchronous using [Guzzle](https://github.com/guzzle/guzzle) and catches API exceptions / errors with a user-definable default value.

## Features 

* Simple, fluent syntax for handling credentials / configuration.
* Completely asynchronous using Guzzle's `getAsync()`.
* User-definable default values when an API request fails.
* Simple debugging when configuring.
* Automatically handles OAuth when fetching from API's such as Twitter.
* Easily extendable and all PR's are very welcome. :heart:

## New Features Planned

* Be able to potentially allow an array of usernames to get passed and if an array of usernames is detected, return an array or object containing username => count – this would have to be done on each provider as well as making sure the API endpoints are going to a location that would be able to pull that data. Some API’s are more strict than others, so milage may vary no matter the approach.

## Current Platforms

* [Facebook](https://developers.facebook.com/)
* [Twitter](https://developer.twitter.com/)
* [Instagram](https://www.instagram.com/developer/)
* [Pinterest](https://developers.pinterest.com/)
* [LinkedIn](https://developer.linkedin.com/)

All pull requests for additional platforms are greatly appreciated. Please use the existing [Providers](https://github.com/Log1x/socialproof/tree/master/src/Providers) as an example.

## Requirements

* [PHP](https://secure.php.net/manual/en/install.php) >= 7.0
* [Composer](https://getcomposer.org/download/)

## Installation 

Install via Composer:

```bash
composer require log1x/socialproof
```

## Usage

SocialProof is incredibly easy to use, but caching values and storing them appropriately to not hit API limits / affect performance is up to the end-user. For WordPress, an example would be using the [Transients API](https://codex.wordpress.org/Transients_API) with an expiration of every 24 hours and the [Options API](https://codex.wordpress.org/Options_API) for a fallback value along with `->setDefault()` in the event an API request fails after your transient expires.

### Facebook

```php
use SocialProof\SocialProof;

return SocialProof::social()
  ->facebook()
      ->setUsername('example')
      ->setToken('XXXXXXXXXXXXXXXXXXXXXXXX')
  ->get();
```

See [here](http://tools.creoworx.com/facebook/) to generate a token for Facebook.

### Twitter 

```php
use SocialProof\SocialProof;

return SocialProof::social()
  ->twitter()
      ->setUsername('username')
      ->setConsumerKey('XXXXXXXXXXXXXXXXXXXXXXXX')
      ->setConsumerSecret('XXXXXXXXXXXXXXXXXXXXXXXX')
      ->setToken('XXXXXXXXXXXXXXXXXXXXXXXX')
      ->setTokenSecret('XXXXXXXXXXXXXXXXXXXXXXXX')
  ->get();
```

### Instagram

```php
use SocialProof\SocialProof;

return SocialProof::social()
  ->instagram()
      ->setToken('XXXXXXXXXXXXXXXXXXXXXXXX')
  ->get();
```

### Pinterest

```php
use SocialProof\SocialProof;

return SocialProof::social()
  ->pinterest()
      ->setUsername('username')
  ->get();
```

### LinkedIn

```php
use SocialProof\SocialProof;

return SocialProof::social()
  ->linkedin()
      ->setUsername('username')
      ->setToken('XXXXXXXXXXXXXXXXXXXXXXXX')
  ->get();
```

## Configuration 

`SocialProof::social()` accepts various configuration when passing through your social credentials. Here's an example using Facebook:

```php
use SocialProof\SocialProof;

return SocialProof::social()
  ->facebook()
      ->setUsername('example')
      ->setToken('XXXXXXXXXXXXXXXXXXXXXXXX')
      ->setDefault('No followers')
      ->setApi('https://graph.facebook.com')
      ->setEndpoint('/v2.7/')
      ->setTimeout(60)
      ->setDebug()
  ->get();
```

A long form syntax is also available for passing credentials and configuration through an array using `setCredentials($array)` and `setConfigs($array)` or a string using `setCredential($key, $value)` and `setConfig($key, value)`.

```php
use SocialProof\SocialProof;

return SocialProof::social()
  ->facebook()
      ->setCredentials([
        'username' => 'example',
        'token' => 'XXXXXXXXXXXXXXXXXXXXXXXX'
      ])
      ->setConfig('default', 'No Followers')
  ->get();
```

## Debugging

Since SocialProof catches API errors, timeouts, etc. and returns a default value instead, you can use `->setDebug()` to enable debugging during initial setup.
