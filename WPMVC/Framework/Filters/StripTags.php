<?php

namespace WPMVC\Framework\Filters;

class StripTags extends \WPMVC\Framework\Filter
{

    public function run($object, $attribute)
    {
        return strip_tags($object->$attribute);
    }
}
