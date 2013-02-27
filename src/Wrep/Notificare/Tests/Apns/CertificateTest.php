<?php

namespace Wrep\Notificare\Tests\Apns;

use \Wrep\Notificare\Apns\Certificate;

class CertificateTests extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider correctConstructorArguments
	 */
	public function testCorrectConstruction($pemFile, $passphrase)
	{
		$certificate = new Certificate($pemFile, $passphrase);
		$this->assertInstanceOf('\Wrep\Notificare\Apns\Certificate', $certificate, 'Certificate of incorrect classtype.');
	}

	/**
	 * @dataProvider correctConstructorArguments
	 */
	public function testGetPemFile($pemFile, $passphrase)
	{
		$certificate = new Certificate($pemFile, $passphrase);
		$this->assertEquals(realpath($pemFile), $certificate->getPemFile(), 'Got incorrect PEM file path from getter.');
	}

	/**
	 * @dataProvider correctConstructorArguments
	 */
	public function testHasPassphrase($pemFile, $passphrase, $hasPassphrase)
	{
		$certificate = new Certificate($pemFile, $passphrase);
		$this->assertEquals($hasPassphrase, $certificate->hasPassphrase(), 'Has passphrase returned incorrect result.');
	}

	/**
	 * @dataProvider correctConstructorArguments
	 */
	public function testGetPassphrase($pemFile, $passphrase)
	{
		$certificate = new Certificate($pemFile, $passphrase);
		$this->assertEquals($passphrase, $certificate->getPassphrase(), 'Get passphrase returned incorrect passphrase.');
	}

	/**
	 * @dataProvider correctConstructorArguments
	 */
	public function testGetFingerprint($pemFile, $passphrase, $hasPassphrase, $fingerprint)
	{
		$certificate = new Certificate($pemFile, $passphrase);
		$this->assertEquals($fingerprint, $certificate->getFingerprint(), 'Got incorrect fingerprint of PEM file.');
	}

	public function correctConstructorArguments()
	{
		return array(
			array(__DIR__ . '/.././resources/../resources/certificate_corrupt.pem', null, false, 'f6727a7e827844141147a6f12250bf2b50e559a4'),
			array(__DIR__ . '/.././resources/../resources/certificate_corrupt.pem', '', false, 'f6727a7e827844141147a6f12250bf2b50e559a4'),
			array(__DIR__ . '/../resources/certificate_corrupt.pem', 'thisIsThePassphrase', true, 'f6727a7e827844141147a6f12250bf2b50e559a4')
			);
	}

	/**
	 * @dataProvider incorrectConstructorArguments
	 */
	public function testIncorrectConstruction($pemFile, $passphrase)
	{
		$this->setExpectedException('InvalidArgumentException', 'Could not find the given PEM file');
		new Certificate($pemFile, $passphrase);
	}

	public function incorrectConstructorArguments()
	{
		return array(
			array(null, null),
			array(null, ''),
			array(null, 'thisIsThePassphrase'),
			array('', null),
			array('', ''),
			array('', 'thisIsThePassphrase'),
			array(__DIR__ . '/../resources/certificate_doesnotexists.pem', null),
			array(__DIR__ . '/../resources/certificate_doesnotexists.pem', ''),
			array(__DIR__ . '/../resources/certificate_doesnotexists.pem', 'thisIsThePassphrase')
			);
	}
}