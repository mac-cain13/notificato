<?php

namespace Wrep\Notificare\Tests\Apns\Feedback;

use \Wrep\Notificare\Apns\Feedback\Feedback;
use \Wrep\Notificare\Apns\Certificate;

class FeedbackTests extends \PHPUnit_Framework_TestCase
{
	private $feedback;
	private $certificate;

	public function setUp()
	{
		$this->certificate = new Certificate(__DIR__ . '/../../resources/certificate_corrupt.pem');
		$this->feedback = new Feedback($this->certificate);
	}

	public function testGetCertificate()
	{
		$this->assertEquals($this->certificate, $this->feedback->getCertificate(), 'Incorrect certificate after constuction');
	}
}