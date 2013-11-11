<?php

namespace WPMVC\Framework\Validators;

class StripTagsValidator extends \WPMVC\Framework\Validator
{

	public function validate()
	{
		if (!$this->has_model_with_attribute)
			return true;
		$this->params[0]->{$this->field}
			= strip_tags($this->value);
		return true;
	}
}
