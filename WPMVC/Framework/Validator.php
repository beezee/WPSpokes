<?php

namespace WPMVC\Framework;

class Validator extends \WPMVC\Framework\Component
{
	public $field;
	public $value;
	public $params;

	public function get_has_model_with_attribute()
	{
		return (isset($this->params[0]) 
			and is_a($this->params[0], '\WPMVC\Framework\Model'));
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
