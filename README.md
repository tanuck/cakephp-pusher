# CakePHP Pusher

[![Build Status](https://secure.travis-ci.org/tanuck/cakephp-pusher.svg?branch=master)](http://travis-ci.org/tanuck/cakephp-pusher)
[![License](https://poser.pugx.org/tanuck/pusher/license.svg)](https://packagist.org/packages/tanuck/pusher)
[![Total Downloads](https://poser.pugx.org/tanuck/pusher/downloads.svg)](https://packagist.org/packages/tanuck/pusher)

A CakePHP plugin for interaction with the Pusher API.

## Installation

Include the following in your composer.json file:

```
{
	"require": {
		"tanuck/pusher": "dev-master"
	}
}
```

Note: This plugin should install in your `Plugin` directory rather than the composer vendors directory.

## Configuration

Firstly, you must load the plugin:

```php
CakePlugin::load('CakePusher');
```

Include the component in your controller:

```php
class MyController extends AppController {

	public $components = array(
		'CakePusher.Pusher' => array(
			'auth_key' => 'PUSHER_APP_AUTH_KEY',
			'secret' => 'PUSHER_APP_SECRET',
			'app_id' => 'PUSHER_APP_ID',
		),
	);
}
```

This is the most basic form taken by the Component constructor, for further configuration options see the Usage section below.

Then access the Component like so:
```php
$this->Pusher->trigger('my-channel', 'my-event', 'My Message');
```

## Usage

If you wish to use just the one Pusher app in your CakePHP application, then the `$components` definition above will be enough. The options available mirror those passed to the Pusher API constructor (see [here](https://github.com/pusher/pusher-http-php) form more information).

The plugin allows you to configure and use multiple Pusher apps with the Component. This can be done by nesting the configuration arrays in the component settings, the array index of each will become the internal alias to each Pusher app. For example:

```php
class MyController extends AppController {

	public $components = array(
		'CakePusher.Pusher' => array(
			'main' => array(
				'auth_key' => 'PUSHER_APP_AUTH_KEY',
				'secret' => 'PUSHER_APP_SECRET',
				'app_id' => 'PUSHER_APP_ID',
			),
			'otherApp' => array(
				'auth_key' => 'PUSHER_APP_AUTH_KEY',
				'secret' => 'PUSHER_APP_SECRET',
				'app_id' => 'PUSHER_APP_ID',
			),
		),
	);
}
```
**NOTE: When using more than one Pusher instance you must specifiy a `main` app.**

You can interact with the default `main` app using the methods directly on the component or you can specify an app to use with the `with` method:

```php
$this->Pusher->trigger('my-channel', 'my-event', 'My Message');
$this->Pusher->socketAuth('my-channel', '1234.1234');
$this->Pusher->presenceAuth('my-channel', '1234.1234', 'user-12', true);
$this->Pusher->get('/channels');

$this->Pusher->with('otherApp')->trigger('my-channel', 'my-event', 'My Message');
```

You can get and set the name of the default Pusher app like so:

```php
$this->Pusher->getDefault();

$this->Pusher->setDefault('otherApp');
```

And you can add additional app configurations on the fly:

```php
$this->Pusher->addAppConfig('myAppName', array(
	'auth_key' => 'PUSHER_APP_AUTH_KEY',
	'secret' => 'PUSHER_APP_SECRET',
	'app_id' => 'PUSHER_APP_ID',
));
```

## License

cakephp-pusher is offered under an [MIT license](http://www.opensource.org/licenses/mit-license.php).
