<?php

namespace WPMVC\Framework\Filters;

class Purify extends \WPMVC\Framework\Filter
{

    public function run($object, $attribute)
    {
		return \WPMVC::instance()->purifier->purify($object->$attribute);
    }
}

