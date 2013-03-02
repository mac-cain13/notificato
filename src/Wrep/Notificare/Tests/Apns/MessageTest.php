<?php

namespace Wrep\Notificare\Tests\Apns;

use \Wrep\Notificare\Apns\Message;
use \Wrep\Notificare\Apns\Certificate;

class MessageTest extends \PHPUnit_Framework_TestCase
{
	private $message;

	public function setUp()
	{
		$certificate = new Certificate(__DIR__ . '/../resources/certificate_corrupt.pem');
		$this->message = new Message('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $certificate);
	}

	public function testConstruction()
	{
		$this->assertInstanceOf('\Wrep\Notificare\Apns\Message', $this->message, 'Message of incorrect classtype.');
		$this->assertEquals('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', $this->message->getDeviceToken(), 'Incorrect token retrieved.');
	}

	/**
	 * @dataProvider incorrectConstructorArguments
	 */
	public function testInvalidConstruction($deviceToken)
	{
		$this->setExpectedException('InvalidArgumentException');
		$message = new Message($deviceToken, new Certificate(__DIR__ . '/../resources/certificate_corrupt.pem'));
	}

	public function incorrectConstructorArguments()
	{
		return array(
			array( null ),
			array( '' ),
			array( 'aef1234' ),
			array( 'thisisnotanhexstring!' )
			);
	}

	/**
	 * @dataProvider correctExpiryArguments
	 */
	public function testExpiry($expiryDate)
	{
		$this->assertEquals(0, $this->message->getExpiresAt());

		$this->message->setExpiresAt($expiryDate);
		if (null == $expiryDate)
		{
			$this->assertEquals(0, $this->message->getExpiresAt());
		}
		else
		{
			$this->assertEquals($expiryDate->format('U'), $this->message->getExpiresAt());
		}
	}

	public function correctExpiryArguments()
	{
		return array(
			array(new \DateTime('2020-12-12 12:12:12')),
			array(new \DateTime('tomorrow')),
			array(null)
			);
	}

	/**
	 * @dataProvider correctAlertArguments
	 */
	public function testAlert($body, $actionLocKey, $launchImage, $result)
	{
		$this->assertNull($this->message->getAlert(), 'Alert not null on creation of message.');

		$this->message->setAlert($body, $actionLocKey, $launchImage);
		$this->assertEquals($result, $this->message->getAlert());
	}

	public function correctAlertArguments()
	{
		return array(
			array( null, null, null, null ),
			array( 'alert-body', null, null, 'alert-body' ),
			array( 'alert-body', 'action', null, array('body' => 'alert-body', 'action-loc-key' => 'action') ),
			array( 'alert-body', 'action', 'image', array('body' => 'alert-body', 'action-loc-key' => 'action', 'launch-image' => 'image') ),
			array( 'alert-body', null, 'image', array('body' => 'alert-body', 'launch-image' => 'image') )
			);
	}

	/**
	 * @dataProvider incorrectAlertArguments
	 */
	public function testInvalidAlert($body, $actionLocKey, $launchImage)
	{
		$this->assertNull($this->message->getAlert(), 'Alert not null on creation of message.');

		$this->setExpectedException('InvalidArgumentException');
		$this->message->setAlert($body, $actionLocKey, $launchImage);
	}

	public function incorrectAlertArguments()
	{
		return array(
			array( null, 'action', null ),
			array( null, null, 'image' )
			);
	}

	/**
	 * @dataProvider correctAlertLocalizedArguments
	 */
	public function testAlertLocalized($locKey, $locArgs, $actionLocKey, $launchImage, $result)
	{
		$this->assertNull($this->message->getAlert(), 'Alert not null on creation of message.');

		$this->message->setAlertLocalized($locKey, $locArgs, $actionLocKey, $launchImage);
		$this->assertEquals($result, $this->message->getAlert());
	}

	public function correctAlertLocalizedArguments()
	{
		return array(
			array( 'alert-loc-key', array(), null, null, array('loc-key' => 'alert-loc-key', 'loc-args' => array()) ),

			array( 'alert-loc-key', array(), null, null, array('loc-key' => 'alert-loc-key', 'loc-args' => array()) ),
			array( 'alert-loc-key', array(), null, 'image', array('loc-key' => 'alert-loc-key', 'loc-args' => array(), 'launch-image' => 'image') ),
			array( 'alert-loc-key', array(), 'action', null, array('loc-key' => 'alert-loc-key', 'loc-args' => array(), 'action-loc-key' => 'action') ),
			array( 'alert-loc-key', array(), 'action', 'image', array('loc-key' => 'alert-loc-key', 'loc-args' => array(), 'action-loc-key' => 'action', 'launch-image' => 'image') ),

			array( 'alert-loc-key', array('1', '2'), null, null, array('loc-key' => 'alert-loc-key', 'loc-args' => array('1', '2')) ),
			array( 'alert-loc-key', array('1', '2'), null, 'image', array('loc-key' => 'alert-loc-key', 'loc-args' => array('1', '2'), 'launch-image' => 'image') ),
			array( 'alert-loc-key', array('1', '2'), 'action', null, array('loc-key' => 'alert-loc-key', 'loc-args' => array('1', '2'), 'action-loc-key' => 'action') ),
			array( 'alert-loc-key', array('1', '2'), 'action', 'image', array('loc-key' => 'alert-loc-key', 'loc-args' => array('1', '2'), 'action-loc-key' => 'action', 'launch-image' => 'image') )
			);
	}

	public function testBadge()
	{
		$this->assertNull($this->message->getBadge(), 'Badge not null on creation of message.');

		$this->message->setBadge(999);
		$this->assertEquals(999, $this->message->getBadge(), 'Setting badge to 999 did not persist.');

		$this->message->clearBadge();
		$this->assertEquals(0, $this->message->getBadge(), 'Clearing the badge did not persist.');

		$this->message->setBadge(null);
		$this->assertNull($this->message->getBadge(), 'Unsetting the badge did not persist.');
	}

	public function testSound()
	{
		$this->assertNull($this->message->getSound(), 'Sound not null on creation of message.');

		$this->message->setSound('funkybeat');
		$this->assertEquals('funkybeat', $this->message->getSound(), 'Setting sound to funkybeat did not persist.');

		$this->message->setSound();
		$this->assertEquals('default', $this->message->getSound(), 'Setting sound to default did not persist.');

		$this->message->setSound(null);
		$this->assertNull($this->message->getSound(), 'Unsetting the sound did not persist.');
	}

	/**
	 * @dataProvider correctPayloadArguments
	 */
	public function testPayload($payload, $referencePayload)
	{
		$this->assertNull($this->message->getPayload(), 'Payload not null on creation of message.');

		$this->message->setPayload($payload);
		$this->assertEquals($referencePayload, $this->message->getPayload(), 'Setting payload as array did not persist.');

		$this->message->setPayload(null);
		$this->assertNull($this->message->getPayload(), 'Unsetting the payload did not persist.');

		$this->message->setPayload(json_encode($payload));
		$this->assertEquals($referencePayload, $this->message->getPayload(), 'Setting payload as JSON string did not persist.');
	}

	public function correctPayloadArguments()
	{
		return array(
			array( array('payload' => 'hasAString'), array('payload' => 'hasAString') ),
			array( array('payload' => array('key' => 'value')), array('payload' => array('key' => 'value')) ),
			array( array('this', 'is', 'some', 'payload'), array('this', 'is', 'some', 'payload') ),
			);
	}

	/**
	 * @dataProvider incorrectPayloadArguments
	 */
	public function testInvalidPayload($payload)
	{
		$this->assertNull($this->message->getPayload(), 'Payload not null on creation of message.');

		$this->setExpectedException('InvalidArgumentException', 'Invalid payload for message.');
		$this->message->setPayload($payload);
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
		$this->assertEquals(json_encode(array(), JSON_FORCE_OBJECT), $this->message->getJson());

		$this->message->setPayload( array('payload' => array( 'some' => 'payloadhere' )) );
		$this->assertJsonStringEqualsJsonString(json_encode(array('payload' => array( 'some' => 'payloadhere' )), JSON_FORCE_OBJECT), $this->message->getJson());

		$this->message->setBadge(9);
		$this->assertJsonStringEqualsJsonString(json_encode(array('payload' => array( 'some' => 'payloadhere' ), 'badge' => 9), JSON_FORCE_OBJECT), $this->message->getJson());

		$this->message->setAlert('thisismyalert');
		$this->assertJsonStringEqualsJsonString(json_encode(array('payload' => array( 'some' => 'payloadhere' ), 'badge' => 9, 'alert' => 'thisismyalert'), JSON_FORCE_OBJECT), $this->message->getJson());

		$this->message->setSound('thisismysound');
		$this->assertJsonStringEqualsJsonString(json_encode(array('payload' => array( 'some' => 'payloadhere' ), 'badge' => 9, 'alert' => 'thisismyalert', 'sound' => 'thisismysound'), JSON_FORCE_OBJECT), $this->message->getJson());
	}
}