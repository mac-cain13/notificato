<?php

namespace Wrep\Notificato\Apns\Feedback;

use \Wrep\Notificato\Apns\SslSocket;
use \Wrep\Notificato\Apns\Certificate;

class Feedback extends SslSocket
{
	/**
	 * Construct Feedback
	 *
	 * @param Certificate The certificate to use to connect to APNS
	 */
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
		// Initialize variables
		$feedbackData = '';
		$tuples = array();

		// Connect to the feedback service
		$this->connect(Certificate::ENDPOINT_TYPE_FEEDBACK);

		// Read all data from the feedback service
		while ( !feof($this->getConnection()) )
		{
			// Make sure signals to this process are respected and handled
			if (function_exists('pcntl_signal_dispatch')) {
				pcntl_signal_dispatch();
			}

			// Fetch the available feedback data from the socket
			$feedbackData .= fread($this->getConnection(), 16384);

			// Loop over all tuples in the current feedbackdata
			while (strlen($feedbackData) >= Tuple::BINARY_LENGTH)
			{
				// Get the first tuple out of the data and unpack the data
				$binaryTupleData = substr($feedbackData, 0, Tuple::BINARY_LENGTH);
				$tupleData = unpack('Ntimestamp/ntokenLength/H*deviceToken', $binaryTupleData);

				// Create a tuple object from it
				$tuples[] = new Tuple($tupleData['timestamp'], $tupleData['deviceToken'], $this->getCertificate());

				// Remove the tuple from the feedbackdata
				$feedbackData = substr($feedbackData, Tuple::BINARY_LENGTH);
			}

			// All messages send, wait some time for an APNS response
			$read = array($this->getConnection());
			$write = $except = null;
			$changedStreams = stream_select($read, $write, $except, 0, self::READ_TIMEOUT);

			// Did waiting for the response succeed?
			if (false === $changedStreams)
			{
				// We'll just stop reading and do not throw an error, because we don't want to loose any tuples
				break;
			}
		}

		// And we're done, disconnect from the service
		$this->disconnect();

		$this->logger->info('Apns\Feedback recieved ' . count($tuples) . ' tuples from APNS feedback service using certificate "' . $this->getCertificate()->getFingerprint() . '"');

		return $tuples;
	}
}