# Notificare [![Build Status of Master](https://travis-ci.org/wrep/notificare.png?branch=master)](https://travis-ci.org/wrep/notificare)
**Notificare takes care of push notifications in your PHP projects.**

> **notify** |ˈnəʊtɪfʌɪ|
>
> verb ( **notifies, notifying, notified** ) [ with obj. ]
>
> from Latin **notificare ‘make known’**

## Why use Notificare instead of X?
Notificare has some advantages not all other PHP push libraries have:

- Supports multiple certificates, so you can push to multiple Apps/Passes/Newsstand Apps
- Takes care of PHPs horrible SSL-sockets and handles all quirks and error responses for you
- Well tested with unit tests

## Installation
Installation with [composer](http://getcomposer.org) is recommended:

1. Add `wrep/notificare` to the requirements in your `composer.json` file
2. Run `composer install` and start using Notificate!

Alternatives:
If you don't want to use composer, but use a [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md) compatible autoloader you should be able to drop the Notificare classes into your project quite easily. Of course you can also just throw the classes into you project and incluse them yourself, but you should really look into composer if you're considering this.

## State of the project
We're still in a alpha state, coding hard and changing the API without any backward compatibility. The library isn't production ready yet, but contributions are welcome and feel free to give Notificare a try.

## Getting started
Note that if you use [Symfony2](http://symfony.com) you should consider using the [Notificare Symfony bundle](https://github.com/wrep/notificare-symfony).

```php
<?php
namespace \Wrep\Example;

use \Wrep\Notificare\Apns\Service;
use \Wrep\Notificare\Apns\MessageFactory;
use \Wrep\Notificare\Apns\Certificate;

class NotificareExample
{
	public function sendPushNotification()
	{
		// Get the certificate that we want to use
		$certificate = new Certificate('/path/to/your/certificate.pem', 'passphrase-of-the-certificate', Certificate::ENDPOINT_SANDBOX);

		// Create the pushmessage object
		$messageFactory = new MessageFactory();
		$message = $messageFactory->createMessage('the-receiving-device-pushtoken-goes-here', $certificate);

		// Configure the pushmessage
		$message->setAlert('This will be the alert body text.');

		// Queue and send the pushmessage
		$sender = new Sender();
		$messageEnvelope = $sender->queue($message);
		$sender->flush();

		// Print the resulting state of our pushmessage
		echo $messageEnvelope->getStatusDescription();
	}
}
```

## License

Notificare is released under the [MIT License](License) so you can use it in commercial and non-commercial projects.