<?php

namespace Wrep\Notificare\Apns\Feedback;

use Wrep\Notificare\Apns\Certificate;

class Tuple
{
	const BINARY_LENGTH = 38; // 4 timestamp + 2 token;ength + 32 token

	private $invalidatedAt;
	private $deviceToken;
	private $certificate;

	/**
	 * Tuple constructor
	 *
	 * @param $invalidatedAtTimestamp int Unix timestamp of the moment the device token was marked as unregistered
	 * @param $deviceToken string Hexadecimal string of the device token that unregistered
	 * @param $certificate Certificate The Certificate the device token is associated with and was used for the APNS connection
	 */
	public function __construct($invalidatedAtTimestamp, $deviceToken, Certificate $certificate)
	{
		// Check if invalidatedAtTimestamp is an int above 0
		if ((int)$invalidatedAtTimestamp <= 0) {
			throw new \InvalidArgumentException('Invalidated at timestamp must be > 0, ' . $invalidatedAtTimestamp . ' given.');
		}

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

		// Save the data
		$this->invalidatedAt = new \DateTime('@' . (int)$invalidatedAtTimestamp);
		$this->deviceToken = $deviceToken;
		$this->certificate = $certificate;
	}

	/**
	 * Moment the device unregistered
	 *  Note: Check if the device didn't re-register after this moment before deleting it!
	 *  Note: This DateTime object is in the UTC timezone, you should be aware of that.
	 *
	 * @return \DateTime
	 */
	public function getInvalidatedAt()
	{
		return $this->invalidatedAt;
	}

	/**
	 * Get the device token of the device that unregistered
	 *
	 * @return string
	 */
	public function getDeviceToken()
	{
		return $this->deviceToken;
	}

	/**
	 * Get the certificate used while receiving this tuple
	 *
	 * @return Certificate
	 */
	public function getCertificate()
	{
		return $this->certificate;
	}
}