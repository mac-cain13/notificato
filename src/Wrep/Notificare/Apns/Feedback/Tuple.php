<?php

namespace Wrep\Notificare\Apns\Feedback;

use Wrep\Notificare\Apns\Certificate;

class Tuple
{
	private $invalidatedAt;
	private $deviceToken;
	private $certificate;

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
	 *  Check if the device didn't re-register after this moment before deleting it!
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