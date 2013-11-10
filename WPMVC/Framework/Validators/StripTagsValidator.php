<?php

namespace WPMVC\Framework\Validators;

class StripTagsValidator extends \WPMVC\Framework\Validator
{

	public function validate()
	{
		if (!$this->hasModelWithAttribute)
			return true;
		$this->params['model']->{$this->attribute}
			= strip_tags($this->params['model']->{$this->attribute});
		return true;
	}
}
