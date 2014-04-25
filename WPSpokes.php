<?php
/*
	Plugin Name: WPSpokes
	Description: MVC Framework for WordPress
	Author: Brian Zeligson
	Version: 0.1-alpha
	Author URI: http://github.com/beezee
 */
define('DS', DIRECTORY_SEPARATOR); 

require_once(dirname(__FILE__).DS.'Autoloader.php'); 
require_once(dirname(__FILE__).DS.'WPSpokes'.DS.'vendor'.DS.'autoload.php');
require_once(dirname(__FILE__).DS.'WPSpokes'.DS.'vendor'.DS.'HtmlPurifier'.DS.'HTMLPurifier.standalone.php');
require_once(dirname(__FILE__).DS.'WPSpokes'.DS.'vendor'.DS.'_.php');
if (defined('ENVIRONMENT') and ENVIRONMENT === 'development')
{
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	require_once(dirname(__FILE__).DS.'WPSpokes'.DS.'vendor'.DS.'php_error.php');
	\php_error\reportErrors(array('wordpress' => true));
}

$framework_loader = new SplClassLoader('WPSpokes', dirname(__FILE__)); 
$framework_loader->register(); 

use \WPSpokes\Framework\Router; 
use \Illuminate\Database\Capsule\Manager as Capsule;
use \Illuminate\Events\Dispatcher;
use \Illuminate\Container\Container;

class WPSpokes extends \WPSpokes\Framework\Component
{ 
	private static $_instance; 
	private static $_is_being_accessed_through_factory = false;
	private $_router; 
	private $_namespaces = array(); 
	private $_purifier;

	public function __construct()
	{
		if (!WPSpokes::is_being_accessed_through_factory())
			throw new Exception('WPSpokes::__construct cannot be called directly. Use WPSpokes::instance');
		$this->initialize();
		return parent::__construct();
	}	

	private function initialize() 
	{
		$this->_router = new Router();	
		$this->load_database();
		$this->register_vendor_namespace('Axelarge');
		$this->register_vendor_namespace('Stringy');
		$this->set_up_validator();
		add_action('plugins_loaded', array($this, 'trigger_loaded'));
		add_action('template_include', array($this, 'dispatch_request'));
	}

	public function trigger_loaded()
	{
		do_action('wpspokes_loaded');
	}	

	public static function is_being_accessed_through_factory()
	{
		return self::$_is_being_accessed_through_factory;
	}

	public static function instance()
	{
		self::$_is_being_accessed_through_factory = true;
		$instance = self::$_instance
			?: self::$_instance = new WPSpokes();
		self::$_is_being_accessed_through_factory = false;
		return $instance;
	}

	private function register_vendor_namespace($namespace)
	{
		$this->register_namespace($namespace, dirname(__FILE__).DS.'WPSpokes'.DS.'vendor');
	}

	public function register_namespace($namespace, $path)
	{
		$this->_namespaces[$namespace] = new SplClassLoader($namespace, $path);
		$this->_namespaces[$namespace]->register();
	}

	private function set_up_validator()
	{
		$this->register_vendor_namespace('Valitron');
		\Valitron\Validator::langDir(dirname(__FILE__).DS.'WPSpokes'.DS.'vendor'.DS.'Valitron'.DS.'lang');
        \Valitron\Validator::addRule('exists', array(new \WPSpokes\Framework\Validators\Exists(), 'run'), 
                    'Must exist');
	}

	public function load_database()
	{
		$capsule = new Capsule();
		$capsule->addConnection(array(
			'driver' => 'mysql',
			'host' => DB_HOST,
			'database' => DB_NAME,
			'username' => DB_USER,
			'password' => DB_PASSWORD,
			'charset' => DB_CHARSET,
			'collation' => 'utf8_unicode_ci',
			'prefix' => ''));
		$capsule->setEventDispatcher(new Dispatcher(new Container()));
		$capsule->setAsGlobal();
		$capsule->bootEloquent();
	}

	public function get_namespace($namespace)
	{
		return $this->_namespaces[$namespace];
	}

	public function get_router()
	{
		return $this->_router;
	}

	public function get_purifier()
	{
		if ($this->_purifier)
			return $this->_purifier;
		$config = HTMLPurifier_Config::createDefault();
		return $this->_purifier = new HTMLPurifier($config);
	}

	private function parse_controller_action_pair_from_route($route)
	{
		$r = explode('#', $route->getTarget());
		$controller_class = $r[0].'Controller';
		$controller = new $controller_class();
		$r[0] = $controller;
		return $r;
	}

	public function dispatch_request($default_response)
	{
		if (!$route = $this->get_router()->matchCurrentRequest())
			return $default_response;
		list($controller, $action) = $this->parse_controller_action_pair_from_route($route);
		$controller->before();
		call_user_func_array(
			array($controller, $action), $route->getParameters());
		$controller->after();
		return;
	}

	public function get_base_dir()
	{
		return dirname(__FILE__).DS.'WPSpokes';
	}
}

WPSpokes::instance();
