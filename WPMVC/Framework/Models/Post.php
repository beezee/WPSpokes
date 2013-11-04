<?php

namespace WPMVC\Framework\Models;

class Post extends \WPMVC\Framework\Model
{
	public $timestamps=false;

	public function __construct($attributes=array())
	{
		global $wpdb;
		$this->table = $wpdb->prefix.'posts';
		return parent::__construct($attributes);
	}
}
