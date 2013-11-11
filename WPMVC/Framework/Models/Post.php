<?php

namespace WPMVC\Framework\Models;

class Post extends \WPMVC\Framework\Model
{
	public $timestamps=false;
	protected $primaryKey = 'ID';

	public function __construct($attributes=array())
	{
		global $wpdb;
		$this->table = $wpdb->prefix.'posts';
		return parent::__construct($attributes);
	}
	
	public function add_rules_to($validator)
	{
		$validator->rule('required', array('post_status', 'post_name', 'post_type',  'post_title'));
		$validator->rule('in', 'post_status', array('publish', 'draft', 'pending', 'future', 'trash'));
		$validator->rule('slug', 'post_name');
		$validator->rule('sanitize', array('post_content', 'post_excerpt'), $this);
		$validator->rule('strip_tags', 'post_title', $this);
		$validator->rule('dateFormat',
			array('post_date', 'post_date_gmt', 'post_modified', 'post_modified_gmt'),
				'Y-m-d H:i:s');
	}
}
