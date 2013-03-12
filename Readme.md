# Notificare [![Build Status of Master](https://travis-ci.org/wrep/notificare.png?branch=master)](https://travis-ci.org/wrep/notificare)
**Notificare takes care of push notifications in your PHP projects.**

> **notify** |ˈnəʊtɪfʌɪ| from Latin **notificare ‘make known’**

## Why use Notificare instead of X?
Notificare has some advantages not all other PHP push libraries have:

1. Supports multiple APNS certificates, so you can push to multiple Apps/Passbook Passes
2. Takes excellent care of PHPs buggy SSL-sockets, handles all quirks and error responses correctly
3. Well tested with unit tests and nice Object-Oriented structure

## Installation
Installation with [Composer](http://getcomposer.org) is recommended. Run the require command to add Notificare to your project:

`composer require wrep/notificare`

*Alternatives:*
There is also a [Notificare for Symfony2 bundle](https://github.com/wrep/notificare-symfony) available, highly recommended for Symfony2 users.

## State of the project
We're quite stable and using this library in some of our own projects, but we're still changing the API without considering backward compatibility. We hope to release 1.0.0 quite soon, but until then, consider this beta (or maybe even alpha) software. You've been warned! :)

## Getting started
Notificare examples can be found in the [Notificare examples repository](https://github.com/wrep/notificare-examples). Make sure you look though the PHP files in that repository! To get you started right away a push and read feedback snippet:

```php
<?php
require_once('vendor/autoload.php');

use \Wrep\Notificare\Apns\Certificate;
use \Wrep\Notificare\Apns\MessageFactory;
use \Wrep\Notificare\Apns\Sender;
use \Wrep\Notificare\Apns\Feedback\Feedback;

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

    /**
     * This example reads all unregistered devices from Apples feedback service
     */
    public function readFeedbackService()
    {
        // First we get the certificate that we want to use to connect to Apple
        $certificate = new Certificate('./apns-certificate.pem', 'passphrase-to-use');

        // Now get a connection to the feedback service
        $feedback = new Feedback($certificate);

        // Read all "tuples" from the feedback service
        $tuples = $feedback->receive();

        // The tuple contains information about what device unregistered and when it did unregister
        foreach ($tuples as $tuple)
        {
            echo 'Device ' . $tuple->getDeviceToken() . ' invalidated at ' . $tuple->getInvalidatedAt()->format(\DateTime::ISO8601) . PHP_EOL;
        }
    }
}

$gettingStarted = new GettingStarted();
$gettingStarted->sendMultiplePushNotification();
$gettingStarted->readFeedbackService();
```

## License

Notificare is released under the [MIT License](License) so you can use it in commercial and non-commercial projects.