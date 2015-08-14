<?php

/**
 * This file is part of the cakephp-pusher plugin.
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file included with this source code.
 *
 * @copyright	Copyright (c) James Tancock <tanuck@gmail.com>
 * @package		CakePusher.Controller.Component
 * @since		2.0
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Component', 'Controller');

/**
 * The pusher component class.
 *
 * @author James Tancock <tanuck@gmail.com>
 */
class PusherComponent extends Component {

/**
 * The default configuration.
 *
 * @var array
 */
	protected $_defaultConfig = array(
		'auth_key' => null,
		'secret' => null,
		'app_id' => null,
		'options' => array(),
		'host' => null,
		'port' => null,
		'timeout' => null,
	);

/**
 * The Pusher api instance.
 *
 * @var Pusher
 */
	protected $_pusher;

/**
 * Constructor
 *
 * @param ComponentCollection $collection A ComponentCollection this component can use to lazy load its components
 * @param array $settings Array of configuration settings.
 * @return void
 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		parent::__construct($collection, $settings);
		$this->settings = array_merge($this->_defaultConfig, $this->settings);
	}

/**
 * Component initialize method.
 *
 * @param Controller $controller The associated controller object.
 * @return void
 */
	public function initialize(Controller $controller) {
		$this->_pusher = new Pusher(
			$this->settings['auth_key'],
			$this->settings['secret'],
			$this->settings['app_id'],
			$this->settings['options'],
			$this->settings['host'],
			$this->settings['port'],
			$this->settings['timeout']
		);
	}

/**
 * Trigger a pusher app event.
 *
 * @param string|array $channels The channel or channels to send the event to.
 * @param string $event Event name.
 * @param mixed $data Event data.
 * @param string $socketId The client socket ID.
 * @return bool
 */
	public function trigger($channels, $event, $data, $socketId = null) {
		return $this->_pusher->trigger($channels, $event, $data, $socketId);
	}

/**
 * Create a socket auth signature.
 *
 * @param string $channel The channel name.
 * @param string $socketId The socket ID.
 * @return string
 */
	public function socketAuth($channel, $socketId) {
		return $this->_pusher->socket_auth($channel, $socketId);
	}

/**
 * Create a presence signature.
 *
 * @param string $channel The channel name.
 * @param string $socketId The socket ID.
 * @param string $userId The user ID.
 * @param bool $userInfo The user information.
 * @return string
 */
	public function presenceAuth($channel, $socketId, $userId, $userInfo = false) {
		return $this->_pusher->presence_auth($channel, $socketId, $userId, $userInfo);
	}

/**
 * Get a REST resource at a given path.
 *
 * @param string $path The url path.
 * @param array $params API parameters.
 * @return array
 */
	public function get($path, $params = array()) {
		return $this->_pusher->get($path, $params);
	}

/**
 * Accessor to the original object so that functionality not yet implemented
 * by the Component can be accessed.
 *
 * @return Pusher
 */
	public function instance() {
		return $this->_pusher;
	}
}
