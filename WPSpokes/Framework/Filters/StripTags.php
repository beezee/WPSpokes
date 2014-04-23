<?php

namespace WPSpokes\Framework\Filters;

class StripTags extends \WPSpokes\Framework\Filter
{

    public function run($object, $attribute)
    {
        return strip_tags($object->$attribute);
    }
}
