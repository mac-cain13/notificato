<?php

namespace Wrep\Notificato\Apns;

/**
 * An APNS Message representation.
 * Note: All strings given to this class must be in UTF-8 to create a valid message
 */
class Message implements \Serializable
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
	private $category;
	private $contentAvailable;

	/**
	 * Get a builder to constuct a message
	 *
	 * @return MessageBuilder
	 */
	public static function builder()
	{
		return new MessageBuilder();
	}

	/**
	 * Construct Message
	 *
	 * @param string Receiver of this message
	 * @param Certificate The certificate that must be used for the APNS connection this message is send over
	 * @param array|null The alert to display or null to set no alert
	 * @param int|null The badge number to display
	 * @param string|null String of the sound to play, null for no sound sound
	 * @param array|json|null The payload to send as array or JSON string
	 * @param string Category identifier for the app to display the correct custom actions
	 * @param boolean True when new newsstand content is available, false when not
	 * @param \DateTime|null Date until the message should be stored for delivery
	 * @throws \InvalidArgumentException On invalid or missing arguments
	 * @throws \LengthException On too long message
	 */
	public function __construct($deviceToken, Certificate $certificate, $alert, $badge, $sound, $payload, $category, $contentAvailable, \DateTime $expiresAt = null)
	{
		// Set the devicetoken
		$this->setDeviceToken($deviceToken);
		$this->certificate = $certificate;
		$this->expiresAt = (null == $expiresAt) ? 0 : $expiresAt->format('U');

		// Set the defaults
		$this->setAlert($alert);
		$this->setBadge($badge);
		$this->sound = (null == $sound) ? null : (string)$sound;
		$this->setPayload($payload);
		$this->category = $category;
		$this->contentAvailable = (bool)$contentAvailable;

		// Validate the length of the message
		if (strlen($this->getJson()) > 2048) {
			throw new \LengthException('Length of the message exceeds the maximum of 2048 characters.');
		}
	}

	/**
	 * Check if this message is short enough to be send to iOS 7 or OS X
	 * Note: iOS 8 support messages up to 2048 bytes, OS X and iOS 7 and below support messages up to 256 bytes
	 *
	 * @return boolean Wheter you can send this message savely to older OSses
	 */
	public function isCompatibleWithSmallPayloadSize() 
	{
		return (strlen($this->getJson()) <= 256);
	}

	/**
	 * Get the device token of the receiving device
	 *
	 * @return string
	 */
	public function getDeviceToken()
	{
		return $this->deviceToken;
	}

	/**
	 * Get the certificate that should be used for this message
	 *
	 * @return Certificate
	 */
	public function getCertificate()
	{
		return $this->certificate;
	}

	/**
	 * Get the moment this message expires
	 *
	 * @return int Unix timestamp of expiry moment or zero if no specific expiry moment is set
	 */
	public function getExpiresAt()
	{
		return $this->expiresAt;
	}

	/**
	 * Get the current alert
	 *
	 * @return string|array
	 */
	public function getAlert()
	{
		return $this->alert;
	}

	/**
	 * Get the value of the badge as set in this message
	 *
	 * @return int|null
	 */
	public function getBadge()
	{
		return $this->badge;
	}

	/**
	 * Get the sound that will be played when this message is received
	 *
	 * @return string|null
	 */
	public function getSound()
	{
		return $this->sound;
	}

	/**
	 * Get the category identifier that will be used to determine custom actions
	 *
	 * @return string|null
	 */
	public function getCategory()
	{
		return $this->category;
	}

	/**
	 * Get newsstand content availability flag that will trigger the newsstand item to download new content
	 *
	 * @return boolean True when new content is available, false when not
	 */
	public function getContentAvailable()
	{
		return $this->contentAvailable;
	}

	/**
	 * Get the current payload
	 *
	 * @return array|null
	 */
	public function getPayload()
	{
		return $this->payload;
	}

	/**
	 * Get the JSON payload that should be send to the APNS
	 *
	 * @return string
	 * @throws \RuntimeException When unable to create JSON, for example because of non-UTF-8 characters
	 */
	public function getJson()
	{
		// Get message and aps array to create JSON from
		$message = array();
		$aps = array();

		// If we have a payload replace the message object by the payload
		if (null !== $this->payload) {
			$message = $this->payload;
		}

		// Add the alert if any
		if (null !== $this->alert) {
			$aps['alert'] = $this->alert;
		}

		// Add the badge if any
		if (null !== $this->badge) {
			$aps['badge'] = $this->badge;
		}

		// Add the sound if any
		if (null !== $this->sound) {
			$aps['sound'] = $this->sound;
		}

		// Add category identifier if any
		if (null !== $this->category) {
			$aps['category'] = $this->category;
		}

		// Add the content-available flag if set
		if (true == $this->contentAvailable) {
			$aps['content-available'] = 1;
		}

		// Check if APS data is set
		if (count($aps) > 0) {
			$message['aps'] = $aps;
		}

		// Encode as JSON object
		$json = json_encode($message);
		if (false == $json) {
			throw new \RuntimeException('Failed to convert APNS\Message to JSON, are all strings UTF-8?', json_last_error());
		}

		return $json;
	}

	/**
	 * String representation of object
	 *
	 * @return string
	 */
	public function serialize()
	{
		return serialize(array(	$this->deviceToken,
								$this->certificate,
								$this->expiresAt,
								$this->alert,
								$this->badge,
								$this->sound,
								$this->payload,
								$this->contentAvailable));
	}

	/**
	 * Constructs the object from serialized data
	 *
	 * @param string Serialized data
	 */
	public function unserialize($serialized)
	{
		list(	$this->deviceToken,
				$this->certificate,
				$this->expiresAt,
				$this->alert,
				$this->badge,
				$this->sound,
				$this->payload,
				$this->contentAvailable) = unserialize($serialized);
	}

	/**
	 * Get a string representation of this object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return	get_class($this) . ':' . PHP_EOL .
				' Device token: ' . $this->getDeviceToken() . PHP_EOL .
				' Certificate: ' . $this->getCertificate()->getPemFile() . PHP_EOL .
				' Expires timestamp: ' . $this->getExpiresAt() . PHP_EOL .
				' Badge: ' . $this->getBadge() . PHP_EOL .
				' Sound: ' . $this->getSound() . PHP_EOL .
				' Content avail.: ' . $this->getContentAvailable() . PHP_EOL .
				' Alert: ' . json_encode($this->getAlert(), JSON_PRETTY_PRINT) . PHP_EOL .
				' Payload: ' . json_encode($this->getPayload(), JSON_PRETTY_PRINT) . PHP_EOL;
	}

	/**
	 * Set the receiver of the message
	 *
	 * @param string Receiver of this message
	 * @throws \InvalidArgumentException On invalid or missing arguments
	 */
	private function setDeviceToken($deviceToken)
	{
		// Check if a devicetoken is given
		if (null == $deviceToken) {
			throw new \InvalidArgumentException('No device token given.');
		}

		// Check if the devicetoken is a valid hexadecimal string
		if (!ctype_xdigit($deviceToken)) {
			throw new \InvalidArgumentException('Invalid device token given, no hexadecimal: ' . $deviceToken);
		}

		// Check if the length of the devicetoken is correct
		if (64 != strlen($deviceToken)) {
			throw new \InvalidArgumentException('Invalid device token given, incorrect length: ' . $deviceToken . ' (' . strlen($deviceToken) . ')');
		}

		// Set the devicetoken
		$this->deviceToken = $deviceToken;
	}

	/**
	 * Set the alert to display.
	 *  See also: http://developer.apple.com/library/ios/#documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/ApplePushService/ApplePushService.html#//apple_ref/doc/uid/TP40008194-CH100-SW1
	 *
	 * @param array|null The alert to display or null to set no alert
	 * @throws \InvalidArgumentException On invalid or missing arguments
	 */
	private function setAlert($alert)
	{
		if (null == $alert)
		{
			// No alert is okay
			$this->alert = null;
		}
		else if ( is_string($alert) )
		{
			// String only alert is okay
			$this->alert = $alert;
		}
		else if ( is_array($alert) )
		{
			if ( isset($alert['body']) || (isset($alert['loc-key']) && isset($alert['loc-args'])) )
			{
				// Valid alert, just fine!
				$this->alert = $alert;
			}
			else
			{
				// Alert must contain correct keys
				throw new \InvalidArgumentException('Invalid alert for message. Alert does not contain a body key or the loc-key and loc-args keys.');
			}
		}
		else
		{
			// Alert must be null or an array
			throw new \InvalidArgumentException('Invalid alert for message. Alert was not an array or null value.');
		}
	}

	/**
	 * Set the badge to display on the App icon
	 *
	 * @param int|null The badge number to display, zero to remove badge
	 * @throws \InvalidArgumentException On invalid or missing arguments
	 */
	private function setBadge($badge)
	{
		// Validate the badge int
		if ((int)$badge < 0) {
			throw new \InvalidArgumentException('Badge must be 0 or higher.');
		}

		// Cast to int or set to null
		$this->badge = (null === $badge) ? null : (int)$badge;
	}

	/**
	 * Set custom payload to go with the message
	 *
	 * @param array|json|null The payload to send as array or JSON string
	 * @throws \InvalidArgumentException On invalid or missing arguments
	 */
	private function setPayload($payload)
	{
		if ( (is_string($payload) && empty($payload)) || (is_array($payload) && count($payload) == 0) )
		{
			// Empty strings or arrays are not allowed
			throw new \InvalidArgumentException('Invalid payload for message. Payload was empty, but not null)');
		}
		else if (is_array($payload) || null === $payload)
		{
			if ( isset($payload['aps']) )
			{
				// Reserved key is used
				throw new \InvalidArgumentException('Invalid payload for message. Custom payload may not contain the reserved "aps" key.');
			}
			else
			{
				// This is okay, set as payload
				$this->payload = $payload;
			}
		}
		else
		{
			// Try to decode JSON string payload
			$payload = json_decode($payload, true);

			// Check if decoding the payload worked
			if (null === $payload) {
				throw new \InvalidArgumentException('Invalid payload for message. Payload was invalid JSON.');
			}

			// Set as payload
			$this->payload = $payload;
		}
	}
}