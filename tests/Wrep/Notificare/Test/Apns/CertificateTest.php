<?php

namespace Wrep\Notificare\Tests\Apns;

use \Wrep\Notificare\Apns\Certificate;

class CertificateTests extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider correctConstructorArguments
	 */
	public function testCorrectConstruction($pemFile, $passphrase, $endpoint)
	{
		$certificate = new Certificate($pemFile, $passphrase, $endpoint);
		$this->assertInstanceOf('\Wrep\Notificare\Apns\Certificate', $certificate, 'Certificate of incorrect classtype.');
	}

	/**
	 * @dataProvider correctConstructorArguments
	 */
	public function testGetPemFile($pemFile, $passphrase, $endpoint)
	{
		$certificate = new Certificate($pemFile, $passphrase, $endpoint);
		$this->assertEquals(realpath($pemFile), $certificate->getPemFile(), 'Got incorrect PEM file path from getter.');
	}

	/**
	 * @dataProvider correctConstructorArguments
	 */
	public function testHasPassphrase($pemFile, $passphrase, $endpoint, $hasPassphrase)
	{
		$certificate = new Certificate($pemFile, $passphrase, $endpoint);
		$this->assertEquals($hasPassphrase, $certificate->hasPassphrase(), 'Has passphrase returned incorrect result.');
	}

	/**
	 * @dataProvider correctConstructorArguments
	 */
	public function testGetPassphrase($pemFile, $passphrase, $endpoint)
	{
		$certificate = new Certificate($pemFile, $passphrase, $endpoint);
		$this->assertEquals($passphrase, $certificate->getPassphrase(), 'Get passphrase returned incorrect passphrase.');
	}

	/**
	 * @dataProvider correctConstructorArguments
	 */
	public function testGetFingerprint($pemFile, $passphrase, $endpoint, $hasPassphrase, $fingerprint)
	{
		$certificate = new Certificate($pemFile, $passphrase, $endpoint);
		$this->assertEquals($fingerprint, $certificate->getFingerprint(), 'Got incorrect fingerprint of PEM file.');
	}

	public function correctConstructorArguments()
	{
		return array(
			array(__DIR__ . '/.././resources/../resources/certificate_corrupt.pem', null, Certificate::ENDPOINT_ENV_PRODUCTION, false, '9f1db6cc07170c41001b0e92e943747a3bee3aa2'),
			array(__DIR__ . '/.././resources/../resources/certificate_corrupt.pem', '', Certificate::ENDPOINT_ENV_PRODUCTION, false, '9f1db6cc07170c41001b0e92e943747a3bee3aa2'),
			array(__DIR__ . '/../resources/certificate_corrupt.pem', 'thisIsThePassphrase', Certificate::ENDPOINT_ENV_PRODUCTION, true, '9f1db6cc07170c41001b0e92e943747a3bee3aa2'),
			array(__DIR__ . '/../resources/certificate_corrupt.pem', 'thisIsThePassphrase', Certificate::ENDPOINT_ENV_SANDBOX, true, '8f34cc9e3de410bd045f777b1f36e004a2449aa7')
			);
	}

	public function testGetEndpoint()
	{
		$certificate = new Certificate(__DIR__ . '/.././resources/../resources/certificate_corrupt.pem', null, Certificate::ENDPOINT_ENV_PRODUCTION);
		$this->assertEquals('ssl://gateway.push.apple.com:2195', $certificate->getEndpoint(), 'Got incorrect production default endpoint.');
		$this->assertEquals('ssl://gateway.push.apple.com:2195', $certificate->getEndpoint(Certificate::ENDPOINT_TYPE_GATEWAY), 'Got incorrect production gateway endpoint.');
		$this->assertEquals('ssl://feedback.push.apple.com:2196', $certificate->getEndpoint(Certificate::ENDPOINT_TYPE_FEEDBACK), 'Got incorrect production feedback endpoint.');

		$certificate = new Certificate(__DIR__ . '/.././resources/../resources/certificate_corrupt.pem', null, Certificate::ENDPOINT_ENV_SANDBOX);
		$this->assertEquals('ssl://gateway.sandbox.push.apple.com:2195', $certificate->getEndpoint(), 'Got incorrect sandbox default endpoint.');
		$this->assertEquals('ssl://gateway.sandbox.push.apple.com:2195', $certificate->getEndpoint(Certificate::ENDPOINT_TYPE_GATEWAY), 'Got incorrect sandbox gateway endpoint.');
		$this->assertEquals('ssl://feedback.sandbox.push.apple.com:2196', $certificate->getEndpoint(Certificate::ENDPOINT_TYPE_FEEDBACK), 'Got incorrect sandbox feedback endpoint.');
	}

	public function testInvalidGetEndpointProduction()
	{
		$certificate = new Certificate(__DIR__ . '/.././resources/../resources/certificate_corrupt.pem', null, Certificate::ENDPOINT_ENV_PRODUCTION);
		$this->setExpectedException('InvalidArgumentException', 'is not a valid endpoint type.');
		$certificate->getEndpoint('invalid');
	}

	public function testInvalidGetEndpointSandbox()
	{
		$certificate = new Certificate(__DIR__ . '/.././resources/../resources/certificate_corrupt.pem', null, Certificate::ENDPOINT_ENV_SANDBOX);
		$this->setExpectedException('InvalidArgumentException', 'is not a valid endpoint type.');
		$certificate->getEndpoint('invalid');
	}

	/**
	 * @dataProvider incorrectConstructorArguments
	 */
	public function testIncorrectConstruction($pemFile, $passphrase, $endpoint)
	{
		$this->setExpectedException('InvalidArgumentException');
		new Certificate($pemFile, $passphrase, $endpoint);
	}

	public function incorrectConstructorArguments()
	{
		return array(
			array(null, null, Certificate::ENDPOINT_ENV_PRODUCTION),
			array(null, '', Certificate::ENDPOINT_ENV_PRODUCTION),
			array(null, 'thisIsThePassphrase', Certificate::ENDPOINT_ENV_PRODUCTION),
			array('', null, Certificate::ENDPOINT_ENV_PRODUCTION),
			array('', '', Certificate::ENDPOINT_ENV_PRODUCTION),
			array('', 'thisIsThePassphrase', Certificate::ENDPOINT_ENV_PRODUCTION),
			array(__DIR__ . '/../resources/certificate_doesnotexists.pem', null, Certificate::ENDPOINT_ENV_PRODUCTION),
			array(__DIR__ . '/../resources/certificate_doesnotexists.pem', '', Certificate::ENDPOINT_ENV_PRODUCTION),
			array(__DIR__ . '/../resources/certificate_doesnotexists.pem', 'thisIsThePassphrase', Certificate::ENDPOINT_ENV_PRODUCTION),
			array(__DIR__ . '/../resources/certificate_corrupt.pem', 'thisIsThePassphrase', null)
			);
	}
}