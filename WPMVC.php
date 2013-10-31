<?php
/*
	Plugin Name: WPMVC
	Description: MVC Framework for working with WordPress
	Author: Brian Zeligson
	Version: 0.1-alpha
	Author URI: http://github.com/beezee
 */
define('DS', DIRECTORY_SEPARATOR); 

require_once(dirname(__FILE__).DS.'Autoloader.php'); 
require_once(dirname(__FILE__).DS.'WPMVC'.DS.'vendor'.DS.'autoload.php');

$framework_loader = new SplClassLoader('WPMVC', dirname(__FILE__)); 
$framework_loader->register(); 

use \WPMVC\Framework\Router; 
use \Illuminate\Database\Capsule\Manager as Capsule;
use \Illuminate\Events\Dispatcher;
use \Illuminate\Container\Container;

class WPMVC 
{ 
	private static $_instance; 
	private $_router; 
	private $_autoloaders = array(); 

	private function __construct() 
	{
		$this->_router = new Router();	
		$this->load_database();
		add_action('plugins_loaded', array($this, 'trigger_loaded'));
		add_action('template_include', array($this, 'dispatch_request'));
	}

	public function trigger_loaded()
	{
		do_action('wpmvc_loaded');
	}	

	public static function instance()
	{
		return self::$_instance
			?: self::$_instance = new WPMVC();
	}

	public function register_autoloader($namespace, $path)
	{
		$this->_autoloaders[$namespace] = new SplClassLoader($namespace, $path);
		$this->_autoloaders[$namespace]->register();
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
			'prefix' => $table_prefix));
		$capsule->setEventDispatcher(new Dispatcher(new Container()));
		$capsule->setAsGlobal();
		$capsule->bootEloquent();
	}

	public function get_autoloader($namespace)
	{
		return $this->_autoloaders[$namespace];
	}

	public function get_router()
	{
		return $this->_router;
	}

	private function parse_controller_action_pair_from_route($route)
	{
		$r = explode('#', $route->getTarget());
		$r[0] .= 'Controller';
		return $r;
	}

	public function dispatch_request($default_response)
	{
		if (!$route = $this->get_router()->matchCurrentRequest())
			return $default_response;
		call_user_func_array(
			$this->parse_controller_action_pair_from_route($route), $route->getParameters());
		return;
		var_dump($route->getTarget());
		var_dump($route->getParameters());
	}
}

WPMVC::instance();
