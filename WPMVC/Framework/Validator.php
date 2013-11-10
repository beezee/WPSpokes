<?php

namespace WPMVC\Framework;

class Validator extends \WPMVC\Framework\Component
{
	public $field;
	public $value;
	public $params;

	public function getHasModelWithAttribute()
	{
		return (isset($this->params['model']) 
			and is_a($this->params['model'], \WPMVC\Framework\Model)
				and $this->params['model']->can_get_property($this->field));
	}
	
	public function run($field, $value, $params=array())
	{
		$this->field = $field;
		$this->value = $value;
		$this->params = $params;
		return $this->validate();
	}

	public function validate()
	{
		return true;
	}
}
