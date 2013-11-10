<?php

namespace WPMVC\Framework\Models;

class Post extends \WPMVC\Framework\Model
{
	public $timestamps=false;
	protected $primaryKey = 'ID';

	public function add_rules_to($validator)
	{
		$validator->rule('required', 'post_title');
		$validator->rule('dateFormat',
			array('post_date', 'post_date_gmt', 'post_modified', 'post_modified_gmt'),
				'Y-m-d H:i:s');
	}

	public function __construct($attributes=array())
	{
		global $wpdb;
		$this->table = $wpdb->prefix.'posts';
		return parent::__construct($attributes);
	}
}
