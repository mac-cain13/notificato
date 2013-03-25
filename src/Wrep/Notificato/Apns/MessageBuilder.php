<?php

namespace Wrep\Notificato\Apns;

class MessageBuilder
{
	// Attributes that go into the binary APNS comminucation
	private $deviceToken;
	private $certificate;
	private $expiresAt;

	// Attributes that go into the payload
	private $alert;
	private $badge;
	private $sound;
	private $payload;
	private $contentAvailable;

	/**
	 * Set the receiver of the message
	 *
	 * @param string Receiver of this message
	 */
	public function setDeviceToken($deviceToken)
	{
		$this->deviceToken = $deviceToken;
	}

	/**
	 * Set the certificate to use when
	 *
	 * @param Certificate The certificate that must be used for the APNS connection this message is send over
	 */
	public function setCertificate(Certificate $certificate)
	{
		$this->certificate = $certificate;
	}

	/**
	 * Set the moment this message should expire or null if APNS should not store the message at all.
	 *  The last message for a device is stored at APNS for delivery until this moment if the device is offline.
	 *
	 * @param \DateTime|null Date until the message should be stored for delivery
	 */
	public function setExpiresAt(\DateTime $expiresAt = null)
	{
		$this->expiresAt = $expiresAt;
	}

	/**
	 * Set the alert to display.
	 *  See also: http://developer.apple.com/library/ios/#documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/ApplePushService/ApplePushService.html#//apple_ref/doc/uid/TP40008194-CH100-SW1
	 *
	 * @param string|null The text of the alert to display or null to set no alert
	 * @param string|null The localization key to use for the action button
	 * @param string|null The name of the launch image to use
	 */
	public function setAlert($body, $actionLocKey = null, $launchImage = null)
	{
		// Check if we must use an JSON object
		if (null == $actionLocKey && null == $launchImage)
		{
			// No, just use a string
			$this->alert = $body;
		}
		else
		{
			// Yes, use an object
			$this->alert = array('body' => $body);

			if ($actionLocKey) {
				$this->alert['action-loc-key'] = $actionLocKey;
			}

			if ($launchImage) {
				$this->alert['launch-image'] = $launchImage;
			}
		}
	}

	/**
	 * Set the localized alert to display.
	 *  See also: http://developer.apple.com/library/ios/#documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/ApplePushService/ApplePushService.html#//apple_ref/doc/uid/TP40008194-CH100-SW1
	 *
	 * @param string The localization key to use for the text of the alert
	 * @param array The arguments that fill the gaps in the locKey text
	 * @param string|null The localization key to use for the action button
	 * @param string|null The name of the launch image to use
	 */
	public function setAlertLocalized($locKey, $locArgs = array(), $actionLocKey = null, $launchImage = null)
	{
		// Set the alert
		$this->alert = array('loc-key' => $locKey,  'loc-args' => $locArgs);

		if ($actionLocKey) {
			$this->alert['action-loc-key'] = $actionLocKey;
		}

		if ($launchImage) {
			$this->alert['launch-image'] = $launchImage;
		}
	}

	/**
	 * Set the badge to display on the App icon
	 *
	 * @param int|null The badge number to display
	 */
	public function setBadge($badge)
	{
		$this->badge = $badge;
	}

	/**
	 * Clear the badge from the App icon
	 */
	public function clearBadge()
	{
		$this->setBadge(0);
	}

	/**
	 * Set the sound that will be played when this message is received
	 *
	 * @param string Optional string of the sound to play, no string will play the default sound
	 */
	public function setSound($sound = 'default')
	{
		$this->sound = $sound;
	}

	/**
	 * Set newsstand content availability flag that will trigger the newsstand item to download new content
	 *
	 * @param boolean True when new newsstand content is available, false when not
	 */
	public function setContentAvailable($contentAvailable)
	{
		$this->contentAvailable = (bool)$contentAvailable;
	}

	/**
	 * Set custom payload to go with the message
	 *
	 * @param array|json|null The payload to send as array or JSON string
	 */
	public function setPayload($payload)
	{
		$this->payload = $payload;
	}

	/**
	 * Build the message
	 *
	 * @return Message
	 * @throws \InvalidArgumentException On invalid or missing arguments
	 * @throws \LengthException On too long message
	 */
	public function build()
	{
		if (null == $this->certificate) {
			throw new \InvalidArgumentException('The certificate cannot be null.');
		}

		return new Message(	$this->deviceToken,
							$this->certificate,
							$this->alert,
							$this->badge,
							$this->sound,
							$this->payload,
							$this->contentAvailable,
							$this->expiresAt);
	}
}