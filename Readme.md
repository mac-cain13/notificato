# Notificare [![Build Status of Master](https://travis-ci.org/wrep/notificare.png?branch=master)](https://travis-ci.org/wrep/notificare)
**Notificare takes care of push notifications in your PHP projects.**

> **notify** |ˈnəʊtɪfʌɪ| from Latin **notificare ‘make known’**

## Why use Notificare instead of X?
Notificare has some advantages not all other PHP push libraries have:

- Supports multiple certificates, so you can push to multiple Apps/Passbook Passes
- Takes excellent care of PHPs buggy SSL-sockets, handles all quirks and error responses correctly
- Well tested with unit tests

## Installation
Installation with [Composer](http://getcomposer.org) is recommended:

1. Add `wrep/notificare` to the requirements in your `composer.json` file
2. Run `composer install` and start using Notificate!

*Alternatives:*
If you don't want to use composer, but use a [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md) compatible autoloader you should be able to drop the Notificare classes into your project quite easily. Of course you can also just throw the classes into you project and incluse them yourself, but you should really look into composer if you're considering this.

## State of the project
We're still in a alpha state, coding hard and changing the API without any backward compatibility. The library isn't production ready yet, but contributions are welcome and feel free to give Notificare a try.

## Getting started
Note that if you use [Symfony2](http://symfony.com) you should consider using the [Notificare Symfony bundle](https://github.com/wrep/notificare-symfony).

```php
<?php
require_once('vendor/autoload.php');

use \Wrep\Notificare\Apns\Certificate;
use \Wrep\Notificare\Apns\MessageFactory;
use \Wrep\Notificare\Apns\Sender;

class GettingStarted
{
	/**
	 * This example sends one pushnotification with an alert to Apples production push servers
	 */
    public function sendOnePushNotification()
    {
        // First we get the certificate that we want to use to connect to Apple
        $certificate = new Certificate('./apns-certificate.pem', 'passphrase-to-use');

        // Then we get the message factory that will help us to create the pushmessages
        $messageFactory = new MessageFactory();

        // Get a message object from the factory
        //  This message will be send to device with pushtoken 'fffff...'
        //  and we pass the certificate so Notificare knows what connection to send it over
        $message = $messageFactory->createMessage('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificate);

        // Let's set the alert text of the message
        $message->setAlert('This will be the alert body text.');

        // Now get a Sender object that will do sending for us
        $sender = new Sender();

        // Send the pushmessage, we'll get an envelope back from the Sender
        $messageEnvelope = $sender->send($message);

        // The envelope contains usefull information about how many retries were needed and if sending succeeded
        echo $messageEnvelope->getStatusDescription();
    }
}

$gettingStarted = new GettingStarted();
$gettingStarted->sendOnePushNotification();
```

More examples can be found in the [Notificare examples repository](https://github.com/wrep/notificare-examples).

## License

Notificare is released under the [MIT License](License) so you can use it in commercial and non-commercial projects.