<?php

namespace WPMVC\Framework\Validators;

class PurifyValidator extends \WPMVC\Framework\Validator
{

	public function validate()
	{
		if (!$this->has_model_with_attribute)
			return true;
		$this->params[0]->{$this->field} 
			= \WPMVC::instance()->purifier->purify($this->value);
		return true;
	}
}
