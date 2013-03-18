<?php

namespace Wrep\Notificato\Tests\Apns\Feedback;

use \Wrep\Notificato\Apns\Certificate;
use \Wrep\Notificato\Apns\Feedback\Tuple;

class TupleTests extends \PHPUnit_Framework_TestCase
{
	private $certificate;
	private $tuple;

	public function setUp()
	{
		$this->certificate = new Certificate(__DIR__ . '/../../resources/certificate_corrupt.pem', null, false, Certificate::ENDPOINT_ENV_PRODUCTION);
		$this->tuple = new Tuple(1362432924, 'ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $this->certificate);
	}

	public function testGetInvalidatedAt()
	{
		$this->assertEquals(new \DateTime('@1362432924'), $this->tuple->getInvalidatedAt(), 'Incorrect invalidation moment after constuction');
	}

	public function testGetDeviceToken()
	{
		$this->assertEquals('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $this->tuple->getDeviceToken(), 'Incorrect device token after constuction');
	}

	public function testGetCertificate()
	{
		$this->assertEquals($this->certificate, $this->tuple->getCertificate(), 'Incorrect certificate after constuction');
	}

	public function testNoInvalidatedAtTimestamp()
	{
		$this->setExpectedException('\InvalidArgumentException', 'Invalidated at timestamp must be > 0');
		new Tuple(null, 'ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $this->certificate);
	}

	public function testInvalidInvalidatedAtTimestamp()
	{
		$this->setExpectedException('\InvalidArgumentException', 'Invalidated at timestamp must be > 0');
		new Tuple(0, 'ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $this->certificate);
	}

	public function testNoDevicetokenTimestamp()
	{
		$this->setExpectedException('\InvalidArgumentException', 'No device token given.');
		new Tuple(1362432924, null, $this->certificate);
	}

	public function testNoHexDevicetokenTimestamp()
	{
		$this->setExpectedException('\InvalidArgumentException', 'Invalid device token given, no hexadecimal');
		new Tuple(1362432924, 'qfffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $this->certificate);
	}

	public function testToShortDevicetokenTimestamp()
	{
		$this->setExpectedException('\InvalidArgumentException', 'Invalid device token given, incorrect length');
		new Tuple(1362432924, 'fff', $this->certificate);
	}
}