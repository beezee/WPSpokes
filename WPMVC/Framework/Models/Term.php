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

}
