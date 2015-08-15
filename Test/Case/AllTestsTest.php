<?php

/**
 * This file is part of the cakephp-pusher plugin.
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file included with this source code.
 *
 * @copyright	Copyright (c) James Tancock <tanuck@gmail.com>
 * @package		CakePusher.Test.Case
 * @since		2.0
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * The CakePusher test suite class.
 *
 * @author James Tancock <tanuck@gmail.com>
 */
class AllTestsTest extends PHPUnit_Framework_TestSuite {

/**
 * The cake test suite method, defines all files for the tests.
 *
 * @return CakeTestSuite
 */
	public static function suite() {
		$suite = new CakeTestSuite('All CakePusher plugin tests');

		$base = dirname(__FILE__);
		$suite->addTestFile($base . DS . 'Controller' . DS . 'Component' . DS . 'PusherComponentTest.php');

		return $suite;
	}
}
