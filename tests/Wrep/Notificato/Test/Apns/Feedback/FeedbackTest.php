<?php

namespace Wrep\Notificato\Tests\Apns\Feedback;

use \Wrep\Notificato\Apns\Feedback\Feedback;
use \Wrep\Notificato\Apns\Certificate;

class FeedbackTests extends \PHPUnit_Framework_TestCase
{
	private $feedback;
	private $certificate;

	public function setUp()
	{
		$this->certificate = new Certificate(__DIR__ . '/../../resources/certificate_corrupt.pem', null, false, Certificate::ENDPOINT_ENV_PRODUCTION);
		$this->feedback = new Feedback($this->certificate);
	}

	public function testGetCertificate()
	{
		$this->assertEquals($this->certificate, $this->feedback->getCertificate(), 'Incorrect certificate after constuction');
	}

	/**
	 * @group realpush
	 */
	public function testFeedback()
	{
		$this->certificate = new Certificate(__DIR__ . '/../../resources/paspas.pem');
		$this->feedback = new Feedback($this->certificate);

		$tuples = $this->feedback->receive();
		$this->assertTrue(is_array($tuples), 'Tuples should be in an array');
		//print_r($tuples); // This is quite usefull to see if there is something comming back from Apple
	}
}