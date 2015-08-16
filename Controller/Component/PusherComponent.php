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
 * Maps Cake CS style methods to the original object.
 *
 * @var array
 */
	protected $_methodMaps = array(
		'trigger' => 'trigger',
		'socketAuth' => 'socket_auth',
		'presenceAuth' => 'presence_auth',
		'get' => 'get',
	);

/**
 * The Pusher api instance.
 *
 * @var Pusher
 */
	protected $_pusherApps = [];

/**
 * The default app name.
 *
 * @var string
 */
	protected $_default = 'main';

/**
 * The pusher app currently in use.
 *
 * @var string
 */
	protected $_current = null;

/**
 * Constructor
 *
 * @param ComponentCollection $collection A ComponentCollection this component can use to lazy load its components
 * @param array $settings Array of configuration settings.
 * @return void
 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		parent::__construct($collection, $settings);

		if (!array_key_exists($this->_default, $settings)) {
			$settings[$this->_default] = $settings;
		}

		$this->settings = array();
		foreach ($settings as $app => &$config) {
			if (!is_array($config)) {
				unset($settings[$app]);
			} else {
				$this->settings[$app] = array_merge($this->_defaultConfig, $config);
			}
		}
	}

/**
 * Component initialize method.
 *
 * @param Controller $controller The associated controller object.
 * @return void
 */
	public function initialize(Controller $controller) {
		foreach ($this->settings as $app => $config) {
			$this->_pusherApps[$app] = $this->_getInstance($config);
		}
	}

/**
 *
 *
 * @param string $name
 * @param array $arguments
 * @throws InternalErrorException
 * @return mixed
 */
	public function __call($name, $arguments) {
		if (!array_key_exists($name, $this->_methodMaps)) {
			throw new InternalErrorException('Invalid pusher method.');
		}

		$app = ($this->_current === null) ? $this->_default : $this->_current;
		$this->_current = null;

		return call_user_func_array(array($this->_pusherApps[$app], $this->_methodMaps[$name]), $arguments);
	}

/**
 * Get the default app name.
 *
 * @return string
 */
	public function getDefault() {
		return $this->_default;
	}

/**
 * Set the default app name.
 *
 * @param string $newDefault The new default app name.
 */
	public function setDefault($newDefault) {
		if (!is_string($newDefault) || !array_key_exists($newDefault, $this->_pusherApps)) {
			return false;
		}

		return (bool)$this->_default = $newDefault;
	}

/**
 * Retrieve
 *
 * @param string $appName The name of the pusher app to use.
 * @throws CakeException
 * @return Pusher
 */
	public function with($appName = null) {
		if (!is_string($appName)) {
			$appName = &$this->_default;
		}

		if (!array_key_exists($appName, $this->_pusherApps)) {
			throw new CakeException('Missing Pusher app instance.');
		}

		$this->_current = $appName;

		return $this;
	}

/**
 *
 *
 * @param string $name
 * @param array $config
 * @return bool
 */
	public function addAppConfig($name, $config) {
		$this->settings[$name] = array_merge($this->_defaultConfig, $config);

		return (bool)$this->_pusherApps[$name] = $this->_getInstance($this->settings[$name]);
	}

/**
 *
 *
 * @param array $config
 * @return Pusher
 */
	protected function _getInstance(array $config) {
		return new Pusher(
			$config['auth_key'],
			$config['secret'],
			$config['app_id'],
			$config['options'],
			$config['host'],
			$config['port'],
			$config['timeout']
		);
	}
}
