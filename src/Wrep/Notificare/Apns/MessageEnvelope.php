<?php

class MessageEnvelope
{
	private $messageId;
	private $message;
	private $errors;

	public function __construct($messageId, ApnsMessage $message)
	{
		// A message id greater then 0 is required
		if (!($messageId > 0)) {
			throw new \InvalidArgumentException('Message ID #' . $messageId . ' is invalid, must be an integer above zero.');
		}

		// A message is required
		if (null == $message) {
			throw new \InvalidArgumentException('No message given.');
		}

		// Save the given parameters
		$this->messageId = $messageId;
		$this->message = $message;
	}

	public function getBinaryMessage()
	{
		;
	}
}