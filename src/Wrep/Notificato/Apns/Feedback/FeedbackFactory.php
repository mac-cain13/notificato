<?php

namespace Wrep\Notificato\Apns\Feedback;

use Wrep\Notificato\Apns\CertificateFactory;
use Wrep\Notificato\Apns\Certificate;

class FeedbackFactory
{
	private $certificateFactory;

	/**
	 * Create the FeedbackFactory
	 *
	 * @param CertificateFactory|null The certificate factory to use when no specific certificate is given on feedback creation
	 */
	public function __construct(CertificateFactory $certificateFactory = null)
	{
		$this->setCertificateFactory($certificateFactory);
	}

	/**
	 * Set a certificate factory to fetch the default certificate from
	 *
	 * @param CertificateFactory|null The certificate factory to use when no specific certificate is given on feedback creation
	 */
	public function setCertificateFactory(CertificateFactory $certificateFactory = null)
	{
		$this->certificateFactory = $certificateFactory;
	}

	/**
	 * Get the current certificate factory
	 *
	 * @return CertificateFactory|null
	 */
	public function getCertificateFactory()
	{
		return $this->certificateFactory;
	}

	/**
	 * Create a Feedback object
	 *
	 * @param Certificate|null The certificate to use or null to use the default certificate from the given certificate factory
	 * @return Feedback
	 */
	public function createFeedback(Certificate $certificate = null)
	{
		// Check if a certificate is given, if not use the default certificate
		if (null == $certificate && $this->getCertificateFactory() != null) {
			$certificate = $this->getCertificateFactory()->getDefaultCertificate();
		}

		// Check if there is a certificate to use after falling back on the default certificate
		if (null == $certificate) {
			throw new \RuntimeException('No certificate given for the creation of the feedback service and no default certificate available.');
		}

		return new Feedback($certificate);
	}
}
