<?php

/**
 * This file is part of the cakephp-pusher plugin.
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file included with this source code.
 *
 * @copyright		Copyright (c) James Tancock <tanuck@gmail.com>
 * @package			CakePusher.Test.Case.Controller.Component
 * @since			2.0
 * @license 		http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('PusherComponent', 'CakePusher.Controller/Component');
App::uses('Controller', 'Controller');

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
 */
	public $_pusher;
}

class PusherComponentTest extends CakeTestCase {

	public function testTrigger() {

	}
}
