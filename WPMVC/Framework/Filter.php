<?php

namespace WPMVC\Framework;

class Filter extends \WPMVC\Framework\Component
{

    public function run($object, $attribute)
    {
        return $object->$attribute;
    }

	public function apply($object, $attributes)
	{
		if (!is_array($attributes))
			$attributes = array($attributes);
		foreach($attributes as $attribute)
			$object->$attribute = $this->run($object, $attribute);
	}
}
