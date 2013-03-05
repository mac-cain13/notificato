<?php

namespace Wrep\Notificare\Apns\Feedback;

use \Wrep\Notificare\Apns\SslSocket;
use \Wrep\Notificare\Apns\Certificate;

class Feedback extends SslSocket
{
	public function __construct(Certificate $certificate)
	{
		parent::__construct($certificate);
	}

	/**
	 * Receive the feedback tuples from APNS
	 *
	 * @return array Array containing FeedbackTuples received from Apple
	 */
	public function receive()
	{
		// Connect to the feedback service
		$this->connect(Certificate::ENDPOINT_TYPE_FEEDBACK);

		// Read all feedback messages

		// And we're done, disconnect from the service
		$this->disconnect();
	}
}