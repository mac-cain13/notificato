# Notificato [![Build Status of Master](https://travis-ci.org/wrep/notificato.png?branch=master)](https://travis-ci.org/wrep/notificato)
**Notificato takes care of push notifications in your PHP projects.**

> *Italian:* **notificato** è: participio passato *English:* **notified**

## Why use Notificato instead of X?
Notificato has some advantages not all other PHP push libraries have:

1. Supports multiple APNS certificates, so you can push to multiple Apps/Passbook Passes
2. Takes excellent care of PHPs buggy SSL-sockets, handles quirks and error responses correctly
3. Well tested with unit tests and nice Object-Oriented structure

## Installation
Installation with [Composer](http://getcomposer.org) is recommended. Run the require command to add Notificato to your project:

`composer require wrep/notificato`

*Alternatives:*
There is also a [Notificato for Symfony2 bundle](https://github.com/wrep/notificato-symfony) available, highly recommended for Symfony2 users.

## Getting started
1. Take a look at the snippet below for a impression how Notificato works
2. [Read the documentation](/doc/Readme.md) it will help you with common use cases
3. Check out the [API docs](http://wrep.github.com/notificato/master/) for a deeper understanding what Notificato is capable of

```php
<?php
// This imports the Composer autoloader
require_once('vendor/autoload.php');

use Wrep\Notificato\Notificato;

class GettingStarted
{
	/**
	 * This example sends one pushnotification with an alert to Apples production push servers
	 */
	public function sendOnePushNotification()
	{
		// First we get a Notificato instance and tell it what certificate to use as default certificate
		$notificato = new Notificato('./certificate.pem', 'passphrase-to-use');

		// Now we get a fresh message from Notificato
		//  This message will be send to device with pushtoken 'fffff...'
		//  it will automaticly be associated with the default certificate
		$message = $notificato->createMessage('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff');

		// Let's set App icon badge with this push to 1
		$message->setBadge(1);

		// The message is ready, let's send it!
		//  Be aware that this method is blocking and on failure Notificato will retry a few times
		$messageEnvelope = $notificato->send($message);

		// The returned envelope contains usefull information about how many retries where needed and if sending succeeded
		echo $messageEnvelope->getFinalStatusDescription();
	}

	/**
	 * This example reads all unregistered devices from Apples feedback service
	 */
	public function readFeedbackService()
	{
		// First we get the a Notificato instance and tell it what certificate to use as default certificate
		$notificato = new Notificato('./certificate.pem', 'passphrase-to-use');

		// Now read all "tuples" from the feedback service, be aware that this method is blocking
		$tuples = $notificato->receiveFeedback();

		// The tuples contain information about what device unregistered and when it did unregister.
		//  Don't forget to check if the device reregistered after the "invaidated at" date!
		foreach ($tuples as $tuple)
		{
			echo 'Device ' . $tuple->getDeviceToken() . ' invalidated at ' . $tuple->getInvalidatedAt()->format(\DateTime::ISO8601) . PHP_EOL;
		}
	}
}

$gettingStarted = new GettingStarted();
$gettingStarted->sendOnePushNotification();
$gettingStarted->readFeedbackService();
```

## Contribute
We'll love contributions, read [Contribute.md](Contribute.md) for some more info on what you can do and stuff that you should know if you want to help!

## License & Credits
Notificato is released under the [MIT License](License) by [Wrep](http://www.wrep.nl/), so feel free to use it in commercial and non-commercial projects.
