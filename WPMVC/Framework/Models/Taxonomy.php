<?php

namespace WPMVC\Framework\Models;

class Taxonomy extends \WPMVC\Framework\Model
{
	protected $primaryKey='term_taxonomy_id';
	protected $table_inheritance_attribute = 'taxonomy';

	public static $classes_inheriting_from_table 
		= array('\WPMVC\Framework\Models\Tag' => 'post_tag', 
			'\WPMVC\Framework\Models\Category' => 'category');

	public function __construct($attributes=array())
	{
		global $wpdb;
		$this->table = $wpdb->prefix.'term_taxonomy';
		return parent::__construct($attributes);
	}

	public function posts()
	{
		global $wpdb;
		return $this->belongsToMany('\WPMVC\Framework\Models\Post',
			$wpdb->prefix.'term_relationships', 'term_taxonomy_id', 'object_id');
	}

	public function term()
	{
		return $this->belongsTo('\WPMVC\Framework\Models\Term', 'term_id');
	}


	public function roles()
	{
		return array(
			'term_interface' => array('\WPMVC\Framework\Roles\TermInterface'));
	}
}
