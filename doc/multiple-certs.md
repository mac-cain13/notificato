# Using multiple certificates
Here we describe a somewhat more complex use case where we use multiple certificates to push messages to multiple Apps/passes at once. This is very usefull in cases where you, for example, use one CMS to push to different Apps or want to use one CMS to push to your production and development App at once.

*Make sure you've read about [basic pushing](push.md) and [reading the feedback service](feedback.md) before you read this.*

## Pushing multiple messages to multiple Apps
This example pushes 2 messages to two different Apps. The big difference is that you pass the certificate you want to use to the message, this way Notificato knows what connection to use for that particular message. There is no default certificate used.
```php
// First we get the a Notificato instance, note that we don't pass it a default certificate!
$notificato = new Notificato();

// Now we create the certificate objects for both Apps
$certificateAppFoo = $notificato->createCertificate('./certificate-app-foo.pem', 'passphrase-here');
$certificateAppBar = $notificato->createCertificate('./certificate-app-bar.pem', 'the-passphrase');

// Create an array to save the message envelopes in
$messageEnvelopes = array();

/** Now send a message to App Foo **/
// First we get a fresh message from Notificato and set the device token, certificate, alert and sound
//  Note that we pass the certificate to the message, as we're not using a default certificate anymore
$builder = $notificato->messageBuilder()
			->setDeviceToken($deviceToken)
			->setCertificate($certificateAppFoo)
			->setAlert('Pilot: They\'re looking for us in the wrong place.')
			->setSound('lost-sound');

// Queue the message for sending
$messageEnvelopes[] = $notificato->queue( $builder->build() );

/** Now send a message to App Bar **/
// We reuse the builder and update it with the new device token, certificate and alert
//  Note that we pass the certificate to the message, as we're not using a default certificate anymore
$builder = $notificato->messageBuilder()
			->setDeviceToken($deviceToken)
			->setCertificate($certificateAppBar)
			->setAlert('Charlie: It was imaginary peanut butter, actually.');

// Queue the message for sending
$messageEnvelopes[] = $notificato->queue( $builder->build() );

// Now all messages are queued, lets send them at once
//  Be aware that this method is blocking and on failure Notificato will retry if necessary
$notificato->flush();

// The returned envelopes contains usefull information about how many retries where needed and if sending succeeded
foreach ($messageEnvelopes as $messageEnvelope)
{
	echo $messageEnvelope->getIdentifier() . ' ' . $messageEnvelope->getFinalStatusDescription() . PHP_EOL;
}
```

*Note: You can still pass the Notificato constructor a default certificate, this certificate will be set on the `MessageBuilder` by default. Use the setCertificate method to use alternative certificates.*

## Receiving feedback for all your certificates
Now we've send the messages we must read the feedback service for all the certificates that are in use. Again the biggest difference is that we don't use a default certificate that we pass to Notificato, but pass a specific certificate to the `receiveFeedback`-method.
```php
// First we get the a Notificato instance, note that we don't pass it a default certificate!
$notificato = new Notificato();

// Now we create the certificate objects for both Apps
$certificateAppFoo = $notificato->createCertificate('./certificate-app-foo.pem', 'passphrase-here');
$certificateAppBar = $notificato->createCertificate('./certificate-app-bar.pem', 'the-passphrase');

/** Get feedback for App Foo **/
// Now read all "tuples" from the feedback service, be aware that this method is blocking
$tuples = $notificato->receiveFeedback($certificateAppFoo);

// The tuples contain information about what device unregistered and when it did unregister.
//  Don't forget to check if the device reregistered after the "invalidated at" date!
foreach ($tuples as $tuple)
{
	echo '[App Foo] Device ' . $tuple->getDeviceToken() . ' invalidated at ' . $tuple->getInvalidatedAt()->format(\DateTime::ISO8601) . PHP_EOL;
}

/** Get feedback for App Bar **/
// Now read all "tuples" from the feedback service, be aware that this method is blocking
$tuples = $notificato->receiveFeedback($certificateAppBar);

// The tuples contain information about what device unregistered and when it did unregister.
//  Don't forget to check if the device reregistered after the "invalidated at" date!
foreach ($tuples as $tuple)
{
	echo '[App Bar] Device ' . $tuple->getDeviceToken() . ' invalidated at ' . $tuple->getInvalidatedAt()->format(\DateTime::ISO8601) . PHP_EOL;
}
```
