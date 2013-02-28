<?php

namespace Wrep\Notificare\Apns;

class MessageEnvelope
{
	// Statuscode constants
	const STATUS_NOERRORS = 0;
	const STATUS_SENDFAILED = 256;
	const STATUS_EARLIERERROR = 257;

	// Binary message constants
	const BINARY_COMMAND = 1;
	const BINARY_DEVICETOKEN_SIZE = 32;

	// Attributes
	private $identifier;
	private $message;
	private $status;

	// Get human readable strings for the statuscodes
	private static $statusDescriptionMapping = array(
			 -1 => 'Not send to APNS',

			  0 => '[APNS] No errors encountered',
			  1 => '[APNS] Processing error',
			  2 => '[APNS] Missing device token',
			  3 => '[APNS] Missing topic',
			  4 => '[APNS] Missing payload',
			  5 => '[APNS] Invalid token size',
			  6 => '[APNS] Invalid topic size',
			  7 => '[APNS] Invalid payload size',
			  8 => '[APNS] Invalid token',
			255 => '[APNS] Unknown error',

			256 => 'Sending failed, will retry with other envelope',
			257 => 'Failed due earlier error, will retry with other envelope',
		);

	/**
	 * Construct MessageEnvelope
	 *
	 * @param $identifier int Unique number to the relevant APNS connection to identify this message
	 * @param $message Message The message that's is contained by this envelope
	 */
	public function __construct($identifier, Message $message)
	{
		// A message id greater then 0 is required
		if (!($identifier > 0)) {
			throw new \InvalidArgumentException('Message ID #' . $identifier . ' is invalid, must be an integer above zero.');
		}

		// A message is required
		if (null == $message) {
			throw new \InvalidArgumentException('No message given.');
		}

		// Save the given parameters
		$this->identifier = $identifier;
		$this->message = $message;
		$this->status = -1;
	}

	/**
	 * Unique number to the relevant APNS connection to identify this message
	 *
	 * @return int
	 */
	public function getIdentifier()
	{
		return $this->identifier;
	}

	/**
	 * The message that's is contained by this envelope
	 *
	 * @return Message
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * Set the status of this message envelope
	 *  only possible if there is no final state set yet.
	 *
	 * @param $status int One of the keys in self::$statusDescriptionMapping
	 */
	public function setStatus($status)
	{
		// Ignore if status stays the same
		if ($this->getStatus() == $status) {
			return;
		}

		// Check if we're not in a final state yet
		if ($this->status > 0) {
			throw new \RuntimeException('Cannot change status from final state ' . $this->status . ' to state ' . $status . '.');
		}

		// Check if this is a valid state
		if ( !in_array($status, array_keys(self::$statusDescriptionMapping)) ) {
			throw new \InvalidArgumentException('Status ' . $status . ' is not a valid status.');
		}

		// Save it!
		$this->status = $status;
	}

	/**
	 * Get the current status of this message envelope
	 *
	 * @return int
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * Get a description of the current status of this message envelope
	 *
	 * @return string
	 */
	public function getStatusDescription()
	{
		return self::$statusDescriptionMapping[$this->getStatus()];
	}

	/**
	 * Get the message that this envelope contains in binary APNS compatible format
	 *
	 * @return string
	 */
	public function getBinaryMessage()
	{
		$jsonMessage = $this->getMessage()->getJson();
		$jsonMessageLength = strlen($jsonMessage);

		$binaryMessage = pack('CNNnH*', self::BINARY_COMMAND, $this->getIdentifier(), 0, self::BINARY_DEVICETOKEN_SIZE, $this->getMessage()->getDeviceToken()) . pack('n', $jsonMessageLength);
		return $binaryMessage . $jsonMessage;
	}
}