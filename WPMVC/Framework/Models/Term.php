<?php

namespace WPMVC\Framework\Models;

class Term extends \WPMVC\Framework\Model
{
	protected $primaryKey='term_id';	

	public function __construct($attributes=array())
	{
		global $wpdb;
		$this->table = $wpdb->prefix.'terms';
		return parent::__construct($attributes);
	}

    public function add_filters_to($filter_chain)
    {
        $filter_chain->filter('name', new \WPMVC\Framework\Filters\StripTags());
    }

	public function add_rules_to($validator)
	{
		$validator->rule('required', array('name', 'slug'));
		$validator->rule('slug', 'slug');
	}

	public function roles()
	{
		return array(
			'slug_generator' => array('\WPMVC\Framework\Roles\SlugGenerator',
				'slug_source_attribute' => 'name',
				'slug_target_attribute' => 'slug'));
	}

}
