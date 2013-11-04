<?php

namespace WPMVC\Framework;
use \WPMVC\Framework\Component;

class Role extends Component
{

	private $_enabled = true;
	protected $owner;

	public function assign_to($owner)
	{
		$this->owner = $owner;
	}

	public function remove_from()
	{
		$this->owner = null;
	}

	public function set_enabled($boolean)
	{
		$this->_enabled = $boolean;
	}

	public function get_enabled()
	{
		return $this->_enabled;
	}
}
