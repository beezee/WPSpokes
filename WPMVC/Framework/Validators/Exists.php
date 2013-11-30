<?php

namespace WPMVC\Framework\Validators;

class Exists extends \WPMVC\Framework\Validator
{

    public function validate()
    {
       if (!isset($this->params['class_name']))
        throw new \Exception('class_name must be specified on Exists validator');
       $class_name = $this->params['class_name'];
       return $this->value and is_a($class_name::find($this->value), $class_name);
    }
}
