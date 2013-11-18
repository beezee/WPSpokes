<?php

namespace WPMVC\Framework\Roles;

use \_;

class SlugGenerator extends \WPMVC\Framework\Role
{
	public $slug_source_attribute;
	public $slug_target_attribute;
	private $_matches = array();

	private function increment_slug_until_unique($slug)
	{
		$count = 1;
		$slug .= '-'.$count;
		$slug_attribute = $this->slug_source_attribute;
		while(_::find($this->_matches, function($m) use ($slug, $slug_attribute){
				return $slug == $m->$slug_attribute; }))
			$slug = preg_replace('/[\d]+$/', $count++, $slug);
		return $slug;
	}

	public function get_slug_uniqueness_scope()
	{
		$class = get_class($this->owner);
		return $class::query();
	}

	public function get_unique_slug()
	{
		$class = get_class($this->owner);
		$slug = \Stringy\StaticStringy::slugify($this->owner->value($this->slug_source_attribute));
		$this->_matches 
			= $this->owner->slug_uniqueness_scope
				->where($this->slug_target_attribute, 'LIKE', $slug.'%')
				->where($this->owner->getKeyName(), '<>', $this->owner->getKey() ?: 0)
				->get();
		return (count($this->_matches) > 0)
			? $this->increment_slug_until_unique($slug)
			: $slug;
	}

	public function get_needs_slug_update()
	{
		return trim($this->owner->value($this->slug_source_attribute)) != ''
			and trim($this->owner->value($this->slug_target_attribute)) == '';
	}

	public function validating()
	{
		if (!$this->slug_source_attribute
			or !$this->slug_target_attribute)
				throw new Exception('SlugGenerator role requires '
					.'both slug_source_attribute and slug_target_attribute '
					.'to be set');
		if (!$this->owner->needs_slug_update)
			return true;
		$this->owner->{$this->slug_target_attribute} = $this->unique_slug;
		return true;
	}
}
