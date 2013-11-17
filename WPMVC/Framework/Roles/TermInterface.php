<?php

namespace WPMVC\Framework\Roles;

class TermInterface extends \WPMVC\Framework\Role
{

	private $_name;

	public function set_name($name)
	{
		$this->_name = $name;		
	}

	public function get_name()
	{
		return $this->_name
			?: $this->owner->value('term.name');
	}

	public function get_slug()
	{
		return $this->term->slug;
	}

	public function created()
	{
		$t = new Term();
		$t->name = $this->_name;
		$t->save();
		$this->owner->term()->associate($t);
		$this->owner->save();
	}

	public function saved()
	{
		if (!$this->_name)
			return true;
		$this->owner->term->name = $this->_name;
		$this->owner->term->save();
	}

}
