<?php

namespace WPMVC\Framework;

class Validator extends \WPMVC\Framework\Component
{
	public $field;
	public $value;
	public $params;

	public function run($field, $value, $params=array())
	{
		$this->field = $field;
		$this->value = $value;
		$this->params = $params[0];
		return $this->validate();
	}

	public function validate()
	{
		return true;
	}
}
