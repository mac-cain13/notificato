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

	/**
	 * @dataProvider correctConstructorArguments
	 */
	public function testGetEndpoint($pemFile, $passphrase, $endpoint, $hasPassphrase, $fingerprint)
	{
		$certificate = new Certificate($pemFile, $passphrase, $endpoint);
		$this->assertEquals($endpoint, $certificate->getEndpoint(), 'Got incorrect endpoint.');
	}

	public function correctConstructorArguments()
	{
		return array(
			array(__DIR__ . '/.././resources/../resources/certificate_corrupt.pem', null, Certificate::ENDPOINT_PRODUCTION, false, '2d13b9d6fa8245594c521fd614e6ff53e3716038'),
			array(__DIR__ . '/.././resources/../resources/certificate_corrupt.pem', '', Certificate::ENDPOINT_PRODUCTION, false, '2d13b9d6fa8245594c521fd614e6ff53e3716038'),
			array(__DIR__ . '/../resources/certificate_corrupt.pem', 'thisIsThePassphrase', Certificate::ENDPOINT_PRODUCTION, true, '2d13b9d6fa8245594c521fd614e6ff53e3716038'),
			array(__DIR__ . '/../resources/certificate_corrupt.pem', 'thisIsThePassphrase', Certificate::ENDPOINT_SANDBOX, true, '73b085b7c0fd359fdb6c07f307b25ed92931d8f5')
			);
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
			array(null, null, Certificate::ENDPOINT_PRODUCTION),
			array(null, '', Certificate::ENDPOINT_PRODUCTION),
			array(null, 'thisIsThePassphrase', Certificate::ENDPOINT_PRODUCTION),
			array('', null, Certificate::ENDPOINT_PRODUCTION),
			array('', '', Certificate::ENDPOINT_PRODUCTION),
			array('', 'thisIsThePassphrase', Certificate::ENDPOINT_PRODUCTION),
			array(__DIR__ . '/../resources/certificate_doesnotexists.pem', null, Certificate::ENDPOINT_PRODUCTION),
			array(__DIR__ . '/../resources/certificate_doesnotexists.pem', '', Certificate::ENDPOINT_PRODUCTION),
			array(__DIR__ . '/../resources/certificate_doesnotexists.pem', 'thisIsThePassphrase', Certificate::ENDPOINT_PRODUCTION),
			array(__DIR__ . '/../resources/certificate_corrupt.pem', 'thisIsThePassphrase', null)
			);
	}
}