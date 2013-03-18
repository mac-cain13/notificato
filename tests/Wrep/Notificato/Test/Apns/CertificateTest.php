<?php

namespace Wrep\Notificato\Tests\Apns;

use \Wrep\Notificato\Apns\Certificate;

class CertificateTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider correctConstructorArguments
	 */
	public function testCorrectConstruction($pemFile, $passphrase, $validate, $endpoint)
	{
		$certificate = new Certificate($pemFile, $passphrase, $validate, $endpoint);
		$this->assertInstanceOf('\Wrep\Notificato\Apns\Certificate', $certificate, 'Certificate of incorrect classtype.');
	}

	/**
	 * @dataProvider correctConstructorArguments
	 */
	public function testGetPemFile($pemFile, $passphrase, $validate, $endpoint)
	{
		$certificate = new Certificate($pemFile, $passphrase, $validate, $endpoint);
		$this->assertEquals(realpath($pemFile), $certificate->getPemFile(), 'Got incorrect PEM file path from getter.');
	}

	/**
	 * @dataProvider correctConstructorArguments
	 */
	public function testHasPassphrase($pemFile, $passphrase, $validate, $endpoint, $hasPassphrase)
	{
		$certificate = new Certificate($pemFile, $passphrase, $validate, $endpoint);
		$this->assertEquals($hasPassphrase, $certificate->hasPassphrase(), 'Has passphrase returned incorrect result.');
	}

	/**
	 * @dataProvider correctConstructorArguments
	 */
	public function testGetPassphrase($pemFile, $passphrase, $validate, $endpoint)
	{
		$certificate = new Certificate($pemFile, $passphrase, $validate, $endpoint);
		$this->assertEquals($passphrase, $certificate->getPassphrase(), 'Get passphrase returned incorrect passphrase.');
	}

	/**
	 * @dataProvider correctConstructorArguments
	 */
	public function testGetFingerprint($pemFile, $passphrase, $validate, $endpoint, $hasPassphrase, $fingerprint)
	{
		$certificate = new Certificate($pemFile, $passphrase, $validate, $endpoint);
		$this->assertEquals($fingerprint, $certificate->getFingerprint(), 'Got incorrect fingerprint of PEM file.');
	}

	public function correctConstructorArguments()
	{
		return array(
			array(__DIR__ . '/.././resources/../resources/certificate_corrupt.pem', null, false, Certificate::ENDPOINT_ENV_PRODUCTION, false, '9f1db6cc07170c41001b0e92e943747a3bee3aa2'),
			array(__DIR__ . '/.././resources/../resources/certificate_corrupt.pem', '', false, Certificate::ENDPOINT_ENV_PRODUCTION, false, '9f1db6cc07170c41001b0e92e943747a3bee3aa2'),
			array(__DIR__ . '/../resources/certificate_corrupt.pem', 'thisIsThePassphrase', false, Certificate::ENDPOINT_ENV_PRODUCTION, true, '9f1db6cc07170c41001b0e92e943747a3bee3aa2'),
			array(__DIR__ . '/../resources/certificate_corrupt.pem', 'thisIsThePassphrase', false, Certificate::ENDPOINT_ENV_SANDBOX, true, '8f34cc9e3de410bd045f777b1f36e004a2449aa7')
			);
	}

	public function testGetEndpoint()
	{
		$certificate = new Certificate(__DIR__ . '/.././resources/../resources/certificate_corrupt.pem', null, false, Certificate::ENDPOINT_ENV_PRODUCTION);
		$this->assertEquals('ssl://gateway.push.apple.com:2195', $certificate->getEndpoint(Certificate::ENDPOINT_TYPE_GATEWAY), 'Got incorrect production gateway endpoint.');
		$this->assertEquals('ssl://feedback.push.apple.com:2196', $certificate->getEndpoint(Certificate::ENDPOINT_TYPE_FEEDBACK), 'Got incorrect production feedback endpoint.');

		$certificate = new Certificate(__DIR__ . '/.././resources/../resources/certificate_corrupt.pem', null, false, Certificate::ENDPOINT_ENV_SANDBOX);
		$this->assertEquals('ssl://gateway.sandbox.push.apple.com:2195', $certificate->getEndpoint(Certificate::ENDPOINT_TYPE_GATEWAY), 'Got incorrect sandbox gateway endpoint.');
		$this->assertEquals('ssl://feedback.sandbox.push.apple.com:2196', $certificate->getEndpoint(Certificate::ENDPOINT_TYPE_FEEDBACK), 'Got incorrect sandbox feedback endpoint.');
	}

	public function testInvalidGetEndpointProduction()
	{
		$certificate = new Certificate(__DIR__ . '/.././resources/../resources/certificate_corrupt.pem', null, false, Certificate::ENDPOINT_ENV_PRODUCTION);
		$this->setExpectedException('InvalidArgumentException', 'is not a valid endpoint type.');
		$certificate->getEndpoint('invalid');
	}

	public function testInvalidGetEndpointSandbox()
	{
		$certificate = new Certificate(__DIR__ . '/.././resources/../resources/certificate_corrupt.pem', null, false, Certificate::ENDPOINT_ENV_SANDBOX);
		$this->setExpectedException('InvalidArgumentException', 'is not a valid endpoint type.');
		$certificate->getEndpoint('invalid');
	}

	/**
	 * @dataProvider incorrectConstructorArguments
	 */
	public function testIncorrectConstruction($pemFile, $passphrase, $validate, $endpoint)
	{
		$this->setExpectedException('InvalidArgumentException');
		new Certificate($pemFile, $passphrase, $validate, $endpoint);
	}

	public function incorrectConstructorArguments()
	{
		return array(
			array(null, null, false, Certificate::ENDPOINT_ENV_PRODUCTION),
			array(null, '', false, Certificate::ENDPOINT_ENV_PRODUCTION),
			array(null, 'thisIsThePassphrase', false, Certificate::ENDPOINT_ENV_PRODUCTION),
			array('', null, false, Certificate::ENDPOINT_ENV_PRODUCTION),
			array('', '', false, Certificate::ENDPOINT_ENV_PRODUCTION),
			array('', 'thisIsThePassphrase', false, Certificate::ENDPOINT_ENV_PRODUCTION),
			array(__DIR__ . '/../resources/certificate_doesnotexists.pem', null, false, Certificate::ENDPOINT_ENV_PRODUCTION),
			array(__DIR__ . '/../resources/certificate_doesnotexists.pem', '', false, Certificate::ENDPOINT_ENV_PRODUCTION),
			array(__DIR__ . '/../resources/certificate_doesnotexists.pem', 'thisIsThePassphrase', false, Certificate::ENDPOINT_ENV_PRODUCTION),
			array(__DIR__ . '/../resources/certificate_corrupt.pem', 'thisIsThePassphrase', false, null)
			);
	}

	/**
	 * @group realpush
	 */
	public function testValidationWithValidCert()
	{
		$cert = new Certificate(__DIR__ . '/../resources/paspas.pem', null);
		$this->assertNotNull($cert->getValidTo());
	}

	public function testValidationWithCorruptCert()
	{
		$this->setExpectedException('InvalidArgumentException', 'Unable to parse certificate');
		new Certificate(__DIR__ . '/../resources/certificate_corrupt.pem', null);
	}
}