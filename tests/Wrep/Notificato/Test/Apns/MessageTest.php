<?php

namespace Wrep\Notificato\Tests\Apns;

use \Wrep\Notificato\Apns\Message;
use \Wrep\Notificato\Apns\Certificate;

class MessageTest extends \PHPUnit_Framework_TestCase
{
	public function testConstruction()
	{
		$certificate = new Certificate(__DIR__ . '/../resources/certificate_corrupt.pem', null, false, Certificate::ENDPOINT_ENV_PRODUCTION);
		$message = Message::builder()->setDeviceToken('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff')->setCertificate($certificate)->build();

		$this->assertInstanceOf('\Wrep\Notificato\Apns\Message', $message, 'Message of incorrect classtype.');
		$this->assertEquals('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $message->getDeviceToken(), 'Incorrect token retrieved.');
	}

	/**
	 * @dataProvider incorrectConstructorArguments
	 */
	public function testInvalidConstruction($deviceToken, $certificate, $alert, $badge, $sound, $payload, $category, $contentAvailable, $expiresAt)
	{
		$this->setExpectedException('InvalidArgumentException');
		$message = new Message($deviceToken, $certificate, $alert, $badge, $sound, $payload, $category, $contentAvailable, $expiresAt);
	}

	public function incorrectConstructorArguments()
	{
		$certificate = new Certificate(__DIR__ . '/../resources/certificate_corrupt.pem', null, false, Certificate::ENDPOINT_ENV_PRODUCTION);

		return array(
			array( 'thisisnotanhexstring!', $certificate, null, 5, 'default', null, null, null, null ),
			array( '', $certificate, null, 5, 'default', null, null, null, null ),
			array( 'ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificate, null, -1, 'default', null, null, null, null ),
			array( 'ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificate, null, null, 'default', 'invalidjsonstring {}', null, null, null )
			);
	}

	/**
	 * @dataProvider correctExpiryArguments
	 */
	public function testExpiry($expiryDate)
	{
		$certificate = new Certificate(__DIR__ . '/../resources/certificate_corrupt.pem', null, false, Certificate::ENDPOINT_ENV_PRODUCTION);
		$message = new Message('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificate, null, 0, 'default', null, null, null, $expiryDate);

		if (null == $expiryDate)
		{
			$this->assertEquals(0, $message->getExpiresAt());
		}
		else
		{
			$this->assertEquals($expiryDate->format('U'), $message->getExpiresAt());
		}
	}

	public function correctExpiryArguments()
	{
		return array(
			array( new \DateTime('2020-12-12 12:12:12') ),
			array( new \DateTime('tomorrow') ),
			array(null)
			);
	}

	/**
	 * @dataProvider correctAlertArguments
	 */
	public function testAlert($alert)
	{
		$certificate = new Certificate(__DIR__ . '/../resources/certificate_corrupt.pem', null, false, Certificate::ENDPOINT_ENV_PRODUCTION);
		$message = new Message('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificate, $alert, 0, 'default', null, null, null, null);

		$this->assertEquals($alert, $message->getAlert());
	}

	public function correctAlertArguments()
	{
		return array(
			array( null ),
			array( 'alert-body' ),
			array( array('body' => 'alert-body', 'action-loc-key' => 'action') ),
			array( array('body' => 'alert-body', 'action-loc-key' => 'action', 'launch-image' => 'image') ),
			array( array('body' => 'alert-body', 'launch-image' => 'image') ),

			array('loc-key' => 'alert-loc-key', 'loc-args' => array()),
			array('loc-key' => 'alert-loc-key', 'loc-args' => array()),
			array('loc-key' => 'alert-loc-key', 'loc-args' => array(), 'launch-image' => 'image'),
			array('loc-key' => 'alert-loc-key', 'loc-args' => array(), 'action-loc-key' => 'action'),
			array('loc-key' => 'alert-loc-key', 'loc-args' => array(), 'action-loc-key' => 'action', 'launch-image' => 'image'),
			array('loc-key' => 'alert-loc-key', 'loc-args' => array('1', '2')),
			array('loc-key' => 'alert-loc-key', 'loc-args' => array('1', '2'), 'launch-image' => 'image'),
			array('loc-key' => 'alert-loc-key', 'loc-args' => array('1', '2'), 'action-loc-key' => 'action'),
			array('loc-key' => 'alert-loc-key', 'loc-args' => array('1', '2'), 'action-loc-key' => 'action', 'launch-image' => 'image')
			);
	}

	/**
	 * @dataProvider incorrectAlertArguments
	 */
	public function testInvalidAlert($alert)
	{
		$certificate = new Certificate(__DIR__ . '/../resources/certificate_corrupt.pem', null, false, Certificate::ENDPOINT_ENV_PRODUCTION);

		$this->setExpectedException('InvalidArgumentException');
		$message = new Message('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificate, $alert, 0, 'default', null, null, null, null);

	}

	public function incorrectAlertArguments()
	{
		return array(
			array( array('action-loc-key' => 'action') ),
			array( array('loc-key' => 'alert-loc-key') ),
			array( array('loc-args' => array()) )
			);
	}

	public function testBadge()
	{
		$certificate = new Certificate(__DIR__ . '/../resources/certificate_corrupt.pem', null, false, Certificate::ENDPOINT_ENV_PRODUCTION);

		$message = new Message('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificate, null, 999, 'default', null, null, null, null);
		$this->assertEquals(999, $message->getBadge(), 'Setting badge to 999 did not persist.');

		$message = new Message('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificate, null, 0, 'default', null, null, null, null);
		$this->assertEquals(0, $message->getBadge(), 'Clearing the badge did not persist.');

		$message = new Message('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificate, null, null, 'default', null, null, null, null);
		$this->assertNull($message->getBadge(), 'Unsetting the badge did not persist.');
	}

	public function testSound()
	{
		$certificate = new Certificate(__DIR__ . '/../resources/certificate_corrupt.pem', null, false, Certificate::ENDPOINT_ENV_PRODUCTION);

		$message = new Message('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificate, null, null, 'funkybeat', null, null, null, null, null);
		$this->assertEquals('funkybeat', $message->getSound(), 'Setting sound to funkybeat did not persist.');

		$message = new Message('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificate, null, null, 'default', null, null, null, null, null);
		$this->assertEquals('default', $message->getSound(), 'Setting sound to default did not persist.');

		$message = new Message('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificate, null, null, null, null, null, null, null, null);
		$this->assertNull($message->getSound(), 'Unsetting the sound did not persist.');
	}

	public function testCategory()
	{
		$certificate = new Certificate(__DIR__ . '/../resources/certificate_corrupt.pem', null, false, Certificate::ENDPOINT_ENV_PRODUCTION);

		$message = new Message('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificate, null, null, null, null, 'testcat', null, null);
		$this->assertEquals('testcat', $message->getCategory(), 'Setting category to testcat did not persist.');

		$message = new Message('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificate, null, null, null, null, null, null, null);
		$this->assertNull(null, $message->getCategory(), 'Nulling the category did not persist.');
	}

	public function testContentAvailable()
	{
		$certificate = new Certificate(__DIR__ . '/../resources/certificate_corrupt.pem', null, false, Certificate::ENDPOINT_ENV_PRODUCTION);

		$message = new Message('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificate, null, null, null, null, null, true, null);
		$this->assertEquals(true, $message->getContentAvailable(), 'Setting ContentAvailable to true did not persist.');

		$message = new Message('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificate, null, null, null, null, null, false, null);
		$this->assertEquals(false, $message->getContentAvailable(), 'Disabling the ContentAvailable did not persist.');

		$message = new Message('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificate, null, null, null, null, null, null, null);
		$this->assertEquals(false, $message->getContentAvailable(), 'Nulling the ContentAvailable did not persist.');
	}

	/**
	 * @dataProvider correctPayloadArguments
	 */
	public function testPayload($payload)
	{
		$certificate = new Certificate(__DIR__ . '/../resources/certificate_corrupt.pem', null, false, Certificate::ENDPOINT_ENV_PRODUCTION);

		$message = new Message('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificate, null, null, null, $payload, null, null, null);
		$this->assertEquals($payload, $message->getPayload(), 'Setting payload did not persist.');
	}

	public function correctPayloadArguments()
	{
		return array(
			array( array('payload' => 'hasAString') ),
			array( array('payload' => array('key' => 'value')) ),
			array( array('this', 'is', 'some', 'payload') ),
			array( array('p' => str_pad('a', 248)) )
			);
	}

	/**
	 * @dataProvider longPayloadArguments
	 */
	public function testTooLongPayload($payload)
	{
		$certificate = new Certificate(__DIR__ . '/../resources/certificate_corrupt.pem', null, false, Certificate::ENDPOINT_ENV_PRODUCTION);

		$this->setExpectedException('\LengthException');
		$message = new Message('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificate, null, null, null, $payload, null, null, null);
		$this->assertEquals($payload, $message->getPayload(), 'Setting payload did not persist.');
	}

	public function longPayloadArguments()
	{
		return array(
			array( array('p' => str_pad('a', 2041))),
			array( array('p' => str_pad('a', 2048))),
			array( array('p' => str_pad('a', 4000)))
			);
	}

	/**
	 * @dataProvider shortPayloadArguments
	 */
	public function testCompatibleWithSmallPayload($payload)
	{
		$certificate = new Certificate(__DIR__ . '/../resources/certificate_corrupt.pem', null, false, Certificate::ENDPOINT_ENV_PRODUCTION);

		$message = new Message('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificate, null, null, null, $payload, null, null, null);
		$this->assertEquals($payload, $message->getPayload(), 'Setting payload did not persist.');
		$this->assertEquals(true, $message->isCompatibleWithSmallPayloadSize());
	}

	public function shortPayloadArguments()
	{
		return array(
			array( array('p' => str_pad('a', 248))),
			array( array('p' => str_pad('a', 100))),
			array( array('p' => str_pad('a', 5)))
			);
	}

	/**
	 * @dataProvider tooBigForShortPayloadArguments
	 */
	public function testIncompatibleWithSmallPayload($payload)
	{
		$certificate = new Certificate(__DIR__ . '/../resources/certificate_corrupt.pem', null, false, Certificate::ENDPOINT_ENV_PRODUCTION);

		$message = new Message('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificate, null, null, null, $payload, null, null, null);
		$this->assertEquals($payload, $message->getPayload(), 'Setting payload did not persist.');
		$this->assertEquals(false, $message->isCompatibleWithSmallPayloadSize());
	}

	public function tooBigForShortPayloadArguments()
	{
		return array(
			array( array('p' => str_pad('a', 249))),
			array( array('p' => str_pad('a', 300))),
			array( array('p' => str_pad('a', 400)))
			);
	}

	/**
	 * @dataProvider incorrectPayloadArguments
	 */
	public function testInvalidPayload($payload)
	{
		$certificate = new Certificate(__DIR__ . '/../resources/certificate_corrupt.pem', null, false, Certificate::ENDPOINT_ENV_PRODUCTION);

		$this->setExpectedException('InvalidArgumentException', 'Invalid payload for message.');
		$message = new Message('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificate, null, null, null, $payload, null, null, null);
		$this->assertEquals($payload, $message->getPayload(), 'Setting payload did not persist.');
	}

	public function incorrectPayloadArguments()
	{
		return array(
			array( '' ),
			array( array() ),
			array( 'CompletelyNotJSON' ),
			array( 'Z{ "some": "invalid JSON" }' )
			);
	}

	public function testGetJson()
	{
		$certificate = new Certificate(__DIR__ . '/../resources/certificate_corrupt.pem', null, false, Certificate::ENDPOINT_ENV_PRODUCTION);
		$message = new Message('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificate, 'alert', 3, 'sound', array('payload' => array( 'some' => 'payloadhere' )), 'mycategory', true, new \DateTime('1970-01-01T00:01:00Z'));
		$this->assertJsonStringEqualsJsonString(json_encode(array('payload' => array( 'some' => 'payloadhere' ), 'aps' => array('badge' => 3, 'alert' => 'alert', 'sound' => 'sound', 'category' => 'mycategory', 'content-available' => 1)), JSON_FORCE_OBJECT), $message->getJson());
	}
}