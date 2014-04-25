<?php

namespace WPSpokes\Framework\Models;

class Term extends \WPSpokes\Framework\Model
{
  protected $fillable = array('name', 'slug');
	protected $primaryKey='term_id';	

	public function __construct($attributes=array())
	{
		global $wpdb;
		$this->table = $wpdb->prefix.'terms';
		return parent::__construct($attributes);
	}

    public function add_filters_to($filter_chain)
    {
        $filter_chain->filter('name', new \WPSpokes\Framework\Filters\StripTags());
    }

	public function add_rules_to(\Valitron\Validator $validator)
	{
		$validator->rule('required', array('name', 'slug'));
		$validator->rule('slug', 'slug');
	}

	public function roles()
	{
		return array(
			'slug_generator' => array('\WPSpokes\Framework\Roles\SlugGenerator',
				'slug_source_attribute' => 'name',
				'slug_target_attribute' => 'slug'));
	}

}
