<?php

namespace WPSpokes\Framework\Filters;

class Purify extends \WPSpokes\Framework\Filter
{

    public function run($object, $attribute)
    {
		return \WPSpokes::instance()->purifier->purify($object->$attribute);
    }
}

