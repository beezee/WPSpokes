<?php

namespace WPMVC\Framework\Validators;

class PurifyValidator extends \WPMVC\Framework\Validator
{

	public function validate()
	{
		if (!$this->hasModelWithAttribute)
			return true;
		$this->params['model']->{$this->field} 
			= \WPMVC::instance()->purifier->purify($this->params['model']->{$this->field});
		return true;
	}
}
