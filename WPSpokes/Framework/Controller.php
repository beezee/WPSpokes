<?php

namespace WPSpokes\Framework;

class Controller extends Component
{

	public $layout = '\\layouts\main';
	private $_content_blocks=array();
	private $_current_content_block;

	public function __construct()
	{
		add_action('wp_head', array($this, 'content_for_header'));
		add_action('wp_footer', array($this, 'content_for_footer'));
	}

	public function content_for_header()
	{
		echo $this->content_for('header');
	}

	public function content_for_footer()
	{
		echo $this->content_for('footer');
	}

	public function content_for($key)
	{
		if (isset($this->_content_blocks[$key]))
			return $this->_content_blocks[$key];
		return '';
	}

	public function begin_content_for($key)
	{
		$this->_current_content_block = $key;
		ob_start();
	}

	public function end_content()
	{
		$this->_content_blocks[$this->_current_content_block] = ob_get_clean();
	}

	public function get_view_directory()
	{
		$reflector = new \ReflectionClass(get_called_class());
         	return dirname($reflector->getFileName());
	}

	private function resolve_view_path($path)
	{
		$file_path = str_replace('\\', DS, str_replace('\\', '\\\\', $path)).'.php';
		if (file_exists($local_view_path = $this->view_directory
							.DS.'..'.DS.'views'.$file_path))
			return $local_view_path;
		return \WPSpokes::instance()->base_dir.DS.'views'.$file_path;
	}

	public function render_partial($path, $arguments=array(), $return=false)
	{
		extract($arguments);
		if ($return) ob_start();
		include($this->resolve_view_path($path));
		if ($return) return ob_get_clean();
	}

	public function render($path, $arguments=array())
	{
		extract($arguments);
		$content = $this->render_partial($path, $arguments, true);
		include($this->resolve_view_path($this->layout));
	}

  public function set404()
  {
    global $wp_query;
    header("HTTP/1.0 404 Not Found - Archive Empty");
    header("Content-Type: text/html");
    $wp_query->set_404();
    require TEMPLATEPATH.'/404.php';
    exit;
  }

	public function before()
	{

	}

	public function after()
	{

	}
}
