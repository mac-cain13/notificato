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
1. [Create an APNS certificate](doc/certificate.md) for your App
2. Look at the notificare examples that can be found in the [Notificare examples repository](https://github.com/wrep/notificare-examples). Make sure you look though the PHP files in that repository!

To get you started right away a push and read feedback snippet:

```php
<?php
// This imports the Composer autoloader
require_once('vendor/autoload.php');

use \Wrep\Notificare\Notificare;

class GettingStarted
{
	/**
	 * This example sends one pushnotification with an alert to Apples production push servers
	 */
    public function sendOnePushNotification()
    {
        // First we get the a Notificare instance and tell it what certificate to use as default certificate
        $notificare = new Notificare('./apns-certificate.pem', 'passphrase-to-use');

        // Now we get a fresh message from Notificare
        //  This message will be send to device with pushtoken 'fffff...'
        //  it will automaticly be associated with the default certificate
        $message = $notificare->createMessage('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff');

        // Let's set the alert and badge of the message
        $message->setAlert('This will be the alert body text.');
        $message->setBadge(1);

        // Now that message is configured as we want to, let's send it!
        //  Be aware that this method is blocking and on failure Notificare will retry a few times
        $messageEnvelope = $notificare->send($message);

        // The returned envelope contains usefull information about how many retries were needed and if sending succeeded
        echo $messageEnvelope->getFinalStatusDescription();
    }

    /**
     * This example reads all unregistered devices from Apples feedback service
     */
    public function readFeedbackService()
    {
        // First we get the a Notificare instance and tell it what certificate to use as default certificate
        $notificare = new Notificare('./apns-certificate.pem', 'passphrase-to-use');

        // Now read all "tuples" from the feedback service
        //  Be aware that this method is blocking
        $tuples = $notificare->receiveFeedback();

        // The tuples contain information about what device unregistered and when it did unregister
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

## License
Notificare is released under the [MIT License](License) so you can use it in commercial and non-commercial projects.