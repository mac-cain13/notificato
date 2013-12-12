# Reading the APNS feedback service
After you've started [pushing messages](push.md) you have to check what devices unregistered for your notifications. Note that Apple [monitors providers](https://developer.apple.com/library/ios/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/Chapters/CommunicatingWIthAPS.html#//apple_ref/doc/uid/TP40008194-CH101-SW3) for their diligence in checking the feedback service. So it's just as important to implement this feedback service as it is to get sending the pushmessages!

## Receiving feedback
This example will show you how to read the feedback service:
```php
// First we get the a Notificato instance and tell it what certificate to use as default certificate
$notificato = new Notificato('./certificate.pem', 'passphrase-to-use');

// Now read all "tuples" from the feedback service, be aware that this method is blocking
$tuples = $notificato->receiveFeedback();

// The tuples contain information about what device unregistered and when it did unregister.
//  Don't forget to check if the device reregistered after the "invalidated at" date!
foreach ($tuples as $tuple)
{
	echo 'Device ' . $tuple->getDeviceToken() . ' invalidated at ' . $tuple->getInvalidatedAt()->format(\DateTime::ISO8601) . PHP_EOL;
}
```

## How often should I check for feedback?
As always, it depends. The most important thing is that you do check it.

If you just send a few messages to a few devices a few times a day a cronjob running every night processing the feedback would be fine. You won't be sending to many messages to unregistered devices and this is an easy solution to implement.

A somewhat more high profile webservice that sends more often to more users also benefits from reading the service more often. Discarding unused tokens will improve sending performance. An easy solution would be to read the feedback service once an hour with an cronjob. A more advanced setup could read feedback after sending a batch of messages.

## How fast does a token show up?
We see that Apple does update the feedback service a few moments after you've send a message to a non-existing device token or after the device unregistered itself. Let's say they take one minute to update the feedback service. (This is just our experience no guarantees!)

## Do I need to implement this if I only push to Passbook Passes?
Yes, you do. Passbook Passes will unregister themself with the webservice you provide the passes with, but it is possible the pass can't unregister properly. For example because the device is reset without an internet connection available. The feedback service will help you to cleanup these kind of tokens.