<?php

namespace Wrep\Notificare\Apns;

class MessageEnvelope
{
	const BINARY_COMMAND = 1;
	const BINARY_DEVICETOKEN_SIZE = 32;

	private $identifier;
	private $message;
	private $errors;

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
	}

	public function getIdentifier()
	{
		return $this->identifier;
	}

	public function getMessage()
	{
		return $this->message;
	}

	public function getBinaryMessage()
	{
		$jsonMessage = $this->getMessage()->getJson();
		$jsonMessageLength = strlen($jsonMessage);

		$binaryMessage = pack('CNNnH*', self::BINARY_COMMAND, $this->getIdentifier(), 0, self::BINARY_DEVICETOKEN_SIZE, $this->getMessage()->getDeviceToken()) . pack('n', $jsonMessageLength);
		return $binaryMessage . $jsonMessage;
	}
}