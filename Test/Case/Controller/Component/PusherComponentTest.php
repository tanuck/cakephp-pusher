<?php

/**
 * This file is part of the cakephp-pusher plugin.
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file included with this source code.
 *
 * @copyright	Copyright (c) James Tancock <tanuck@gmail.com>
 * @package		CakePusher.Test.Case.Controller.Component
 * @since		2.0
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('PusherComponent', 'CakePusher.Controller/Component');
App::uses('Controller', 'Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');

/**
 * A test variant of the PusherComponent class.
 *
 * @author James Tancock <tanuck@gmail.com>
 */
class TestPusherComponent extends PusherComponent {

/**
 * The Pusher api instance.
 *
 * @var Pusher
 * @codingStandardsIgnoreStart
 */
	public $_pusherApps = [];

	public $_current = null;
// @codingStandardsIgnoreEnd
}

class PusherComponentTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();

		$ComponentCollection = new ComponentCollection();
		$this->Pusher = new TestPusherComponent($ComponentCollection, array(
			'main' => array(
				'auth_key' => 'a',
				'secret' => 'b',
				'app_id' => '1234',
			),
			'backup' => array(
				'auth_key' => '1',
				'secret' => '2',
				'app_id' => '5678',
			),
		));
		$this->Pusher->initialize(new Controller(new CakeRequest(), new CakeResponse()));
	}

	public function testConstructor() {
		$collection = new ComponentCollection();
		$Pusher = new TestPusherComponent($collection, array(
			'auth_key' => 'b',
			'secret' => 'c',
			'app_id' => '4321',
			'bad_key' => 'bad_value',
		));

		$expected = array(
			'main' => array(
				'auth_key' => 'b',
				'secret' => 'c',
				'app_id' => '4321',
				'options' => array(),
				'host' => null,
				'port' => null,
				'timeout' => null,
				'bad_key' => 'bad_value',
			),
		);
		$this->assertEquals($expected, $Pusher->settings);
	}

	public function testInitialize() {
		$this->assertInstanceOf('Pusher', $this->Pusher->_pusherApps['main']);
	}

/**
 * @expectedException InternalErrorException
 * @expectedExceptionMessage Invalid pusher method.
 */
	public function testForErrorWhenCallingUnMappedMethod() {
		$this->Pusher->badMethodCall();
	}

	public function testTrigger() {
		$this->Pusher->_pusherApps['main'] = $this->getMockBuilder('Pusher')
									->disableOriginalConstructor()
									->getMock();
		$this->Pusher->_pusherApps['main']
					->expects($this->exactly(1))
					->method('trigger')
					->with($this->equalTo('my-channel'),
						$this->equalTo('new-message'),
						$this->equalTo('Hello Bill'),
						$this->equalTo(null))
					->will($this->returnValue(true));

		$this->assertTrue($this->Pusher->trigger('my-channel', 'new-message', 'Hello Bill'));
	}

	public function testSocketAuth() {
		$result = $this->Pusher->socketAuth('my-channel', '1234.1234');
		$this->assertInternalType('string', $result);
		$this->assertEquals(
			'{"auth":"a:0126071f211855bc9a126cc8a702a877b1b349e98189ddcb2a88e4be810297d1"}',
			$result
		);
	}

	public function testPresenceAuth() {
		$result = $this->Pusher->presenceAuth('my-channel', '1234.1234', 'user-12', true);
		$this->assertInternalType('string', $result);
		$this->assertEquals(
			'{"auth":"a:47279d0aa95b2c5cf73bbe8190bf3973db703f5742cf4b5e248c828138238dd0","channel_data":"{\"user_id\":\"user-12\",\"user_info\":true}"}',
			$result
		);

		$this->Pusher->_pusherApps['main'] = $this->getMockBuilder('Pusher')
									->disableOriginalConstructor()
									->getMock();
		$this->Pusher->_pusherApps['main']
					->expects($this->exactly(1))
					->method('presence_auth')
					->with($this->equalTo('my-channel'),
						$this->equalTo('1234.1234'),
						$this->equalTo('user-12'),
						$this->equalTo(true));
		$this->Pusher->presenceAuth('my-channel', '1234.1234', 'user-12', true);
	}

	public function testGet() {
		$this->Pusher->_pusherApps['main'] = $this->getMockBuilder('Pusher')
									->disableOriginalConstructor()
									->getMock();
		$this->Pusher->_pusherApps['main']
					->expects($this->exactly(1))
					->method('get')
					->with($this->equalTo('/channels'),
						$this->equalTo(array()))
					->will($this->returnValue(array('status' => 200)));

		$result = $this->Pusher->get('/channels');
		$this->assertInternalType('array', $result);
		$this->assertEquals(200, $result['status']);
	}

	public function testGetDefault() {
		$default = $this->Pusher->getDefault();
		$this->assertInternalType('string', $default);
		$this->assertEquals('main', $default);
	}

	public function testSetDefault() {
		$this->assertFalse($this->Pusher->setDefault(new stdClass()));

		$this->assertFalse($this->Pusher->setDefault('badappname'));

		$this->assertTrue($this->Pusher->setDefault('backup'));
		$this->assertEquals('backup', $this->Pusher->getDefault());
	}

	public function testAddAppConfig() {
		$result = $this->Pusher->addAppConfig('newapp', array(
			'auth_key' => 'key',
			'secret' => 'secret',
			'app_id' => '234',
		));
		$this->assertTrue($result);
	}

	public function testWith() {
		$result = $this->Pusher->with('backup');
		$this->assertInstanceOf('PusherComponent', $result);
		$this->assertEquals('backup', $this->Pusher->_current);

		$result = $this->Pusher->with();
		$this->assertEquals('main', $this->Pusher->_current);
	}

/**
 * @expectedException CakeException
 * @expectedExceptionMessage Missing Pusher app instance.
 */
	public function testWithErrorForMissingApp() {
		$this->Pusher->with('badappname');
	}

}
