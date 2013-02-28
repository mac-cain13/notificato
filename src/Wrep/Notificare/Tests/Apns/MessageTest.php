<?php

namespace Wrep\Notificare\Tests\Apns;

use \Wrep\Notificare\Apns\Message;

class MessageTest extends \PHPUnit_Framework_TestCase
{
	public function testConstruction()
	{
		$message = new Message('2635d2cb3e51b705bcdf277498ffffce4c64e48ec313d2ccb9f603e2ffff98ef');
		$this->assertInstanceOf('\Wrep\Notificare\Apns\Message', $message, 'Message of incorrect classtype.');
		$this->assertEquals('2635d2cb3e51b705bcdf277498ffffce4c64e48ec313d2ccb9f603e2ffff98ef', $message->getDeviceToken(), 'Incorrect token retrieved.');
	}

	/**
	 * @dataProvider incorrectConstructorArguments
	 */
	public function testInvalidConstruction($deviceToken)
	{
		$this->setExpectedException('InvalidArgumentException');
		$message = new Message($deviceToken);
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
	 * @dataProvider correctAlertArguments
	 */
	public function testAlert($body, $actionLocKey, $launchImage, $result)
	{
		$message = new Message('2635d2cb3e51b705bcdf277498ffffce4c64e48ec313d2ccb9f603e2ffff98ef');
		$this->assertNull($message->getAlert(), 'Alert not null on creation of message.');

		$message->setAlert($body, $actionLocKey, $launchImage);
		$this->assertEquals($result, $message->getAlert());
	}

	public function correctAlertArguments()
	{
		return array(
			array( 'alert-body', null, null, 'alert-body' ),
			array( 'alert-body', 'action', null, array('body' => 'alert-body', 'action-loc-key' => 'action') ),
			array( 'alert-body', 'action', 'image', array('body' => 'alert-body', 'action-loc-key' => 'action', 'launch-image' => 'image') ),
			array( 'alert-body', null, 'image', array('body' => 'alert-body', 'launch-image' => 'image') )
			);
	}

	/**
	 * @dataProvider correctAlertLocalizedArguments
	 */
	public function testAlertLocalized($locKey, $locArgs, $actionLocKey, $launchImage, $result)
	{
		$message = new Message('2635d2cb3e51b705bcdf277498ffffce4c64e48ec313d2ccb9f603e2ffff98ef');
		$this->assertNull($message->getAlert(), 'Alert not null on creation of message.');

		$message->setAlertLocalized($locKey, $locArgs, $actionLocKey, $launchImage);
		$this->assertEquals($result, $message->getAlert());
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
		$message = new Message('2635d2cb3e51b705bcdf277498ffffce4c64e48ec313d2ccb9f603e2ffff98ef');
		$this->assertNull($message->getBadge(), 'Badge not null on creation of message.');

		$message->setBadge(999);
		$this->assertEquals(999, $message->getBadge(), 'Setting badge to 999 did not persist.');

		$message->clearBadge();
		$this->assertEquals(0, $message->getBadge(), 'Clearing the badge did not persist.');

		$message->setBadge(null);
		$this->assertNull($message->getBadge(), 'Unsetting the badge did not persist.');
	}

	public function testSound()
	{
		$message = new Message('2635d2cb3e51b705bcdf277498ffffce4c64e48ec313d2ccb9f603e2ffff98ef');
		$this->assertNull($message->getSound(), 'Sound not null on creation of message.');

		$message->setSound('funkybeat');
		$this->assertEquals('funkybeat', $message->getSound(), 'Setting sound to funkybeat did not persist.');

		$message->setSound();
		$this->assertEquals('default', $message->getSound(), 'Setting sound to default did not persist.');

		$message->setSound(null);
		$this->assertNull($message->getSound(), 'Unsetting the sound did not persist.');
	}

	/**
	 * @dataProvider correctPayloadArguments
	 */
	public function testPayload($payload, $referencePayload)
	{
		$message = new Message('2635d2cb3e51b705bcdf277498ffffce4c64e48ec313d2ccb9f603e2ffff98ef');
		$this->assertNull($message->getPayload(), 'Payload not null on creation of message.');

		$message->setPayload($payload);
		$this->assertEquals($referencePayload, $message->getPayload(), 'Setting payload as array did not persist.');

		$message->setPayload(null);
		$this->assertNull($message->getPayload(), 'Unsetting the payload did not persist.');

		$message->setPayload(json_encode($payload));
		$this->assertEquals($referencePayload, $message->getPayload(), 'Setting payload as JSON string did not persist.');
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
		$message = new Message('2635d2cb3e51b705bcdf277498ffffce4c64e48ec313d2ccb9f603e2ffff98ef');
		$this->assertNull($message->getPayload(), 'Payload not null on creation of message.');

		$this->setExpectedException('InvalidArgumentException', 'Invalid payload for message.');
		$message->setPayload($payload);
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
		$message = new Message('2635d2cb3e51b705bcdf277498ffffce4c64e48ec313d2ccb9f603e2ffff98ef');
		$this->assertEquals(json_encode(array(), JSON_FORCE_OBJECT), $message->getJson());

		$message->setPayload( array('payload' => array( 'some' => 'payloadhere' )) );
		$this->assertJsonStringEqualsJsonString(json_encode(array('payload' => array( 'some' => 'payloadhere' )), JSON_FORCE_OBJECT), $message->getJson());

		$message->setBadge(9);
		$this->assertJsonStringEqualsJsonString(json_encode(array('payload' => array( 'some' => 'payloadhere' ), 'badge' => 9), JSON_FORCE_OBJECT), $message->getJson());

		$message->setAlert('thisismyalert');
		$this->assertJsonStringEqualsJsonString(json_encode(array('payload' => array( 'some' => 'payloadhere' ), 'badge' => 9, 'alert' => 'thisismyalert'), JSON_FORCE_OBJECT), $message->getJson());

		$message->setSound('thisismysound');
		$this->assertJsonStringEqualsJsonString(json_encode(array('payload' => array( 'some' => 'payloadhere' ), 'badge' => 9, 'alert' => 'thisismyalert', 'sound' => 'thisismysound'), JSON_FORCE_OBJECT), $message->getJson());
	}
}