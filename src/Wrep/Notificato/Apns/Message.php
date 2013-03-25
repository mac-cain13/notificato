<?php

namespace Wrep\Notificato\Apns;

/**
 * An APNS Message representation.
 * Note: All strings given to this class must be in UTF-8 to create a valid message
 */
class Message
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
	 * Construct Message
	 *
	 * @param string Receiver of this message
	 * @param Certificate The certificate that must be used for the APNS connection this message is send over
	 * @param array|null The alert to display or null to set no alert
	 * @param int|null The badge number to display
	 * @param string|null String of the sound to play, null for no sound sound
	 * @param array|json|null The payload to send as array or JSON string
	 * @param boolean True when new newsstand content is available, false when not
	 * @param \DateTime|null Date until the message should be stored for delivery
	 * @throws \InvalidArgumentException On invalid or missing arguments
	 */
	public function __construct($deviceToken, Certificate $certificate, $alert, $badge, $sound, $payload, $contentAvailable, \DateTime $expiresAt = null)
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
		$this->contentAvailable = (bool)$contentAvailable;
	}

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

	private function setAlert($alert)
	{
		if (null == $alert)
		{
			// No alert is okay
			$this->alert = null;
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

	private function setBadge($badge)
	{
		// Validate the badge int
		if ((int)$badge < 0) {
			throw new \InvalidArgumentException('Badge must be 0 or higher.');
		}

		// Cast to int or set to null
		$this->badge = (null === $badge) ? null : (int)$badge;
	}

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
	 * Checks if the length of the message is acceptable for the APNS
	 *
	 * @return boolean True when the length is okay, false when you should shorten the payload
	 */
	public function validateLength()
	{
		return (strlen($this->getJson()) <= 256);
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

		// Add the content-available flag if set
		if (true == $this->contentAvailable) {
			$aps['content-available'] = 1;
		}

		// Check if APS data is set
		if (count($aps) > 0) {
			$message['aps'] = $aps;
		}

		// Encode as JSON object
		$json = json_encode($message, JSON_FORCE_OBJECT);
		if (false == $json) {
			throw new \RuntimeException('Failed to convert APNS\Message to JSON, are all strings UTF-8?', json_last_error());
		}

		return $json;
	}
}