<?php

namespace WPSpokes\Framework\Models;

class Post extends \WPSpokes\Framework\Model
{
  protected $hidden = array('post_password');
  protected $guarded = array('post_date', 'post_password');
	protected $primaryKey = 'ID';

	public function __construct($attributes=array())
	{
		global $wpdb;
		$this->table = $wpdb->prefix.'posts';
		return parent::__construct($attributes);
	}

  public function author()
  {
      return $this->belongsTo('\WPSpokes\Framework\Models\User', 'post_author');
  }

	public function taxonomies()
	{
		global $wpdb;
		return $this->belongsToMany('\WPSpokes\Framework\Models\Taxonomy', 
			$wpdb->prefix.'term_relationships', 'object_id', 'term_taxonomy_id');
	}

  public function get_tags()
  {
    return \_::filter($this->taxonomies, function($t) { return $t->taxonomy == 'post_tag'; });
  }
	

  public function get_categories()
  {
    return \_::filter($this->taxonomies, function($t) { return $t->taxonomy == 'category'; });
  }
  public function add_filters_to($filter_chain)
  {
      $filter_chain->filter(array('post_content', 'post_excerpt'),
          new \WPSpokes\Framework\Filters\Purify());
      $filter_chain->filter('post_title', 
          new \WPSpokes\Framework\Filters\StripTags());
  }

	public function add_rules_to(\Valitron\Validator $validator)
	{
		$validator->rule('required', 
            array('post_status', 'post_author', 'post_name', 'post_type',  'post_title'));
		$validator->rule('in', 'post_status', array('publish', 'draft', 'pending', 'future', 'trash'));
		$validator->rule('slug', 'post_name');
        $validator->rule('exists', 'post_author', array('class_name' => '\WPSpokes\Framework\Models\User'))
            ->message('Post Author must be an existing user');
		$validator->rule('dateFormat',
			array('post_date', 'post_date_gmt', 'post_modified', 'post_modified_gmt'),
				'Y-m-d H:i:s');
	}

	public function get_is_being_published()
	{
		return $this->isDirty('post_status')
			and $this->post_status === 'publish';
	}

	public function update_post_dates()
	{
		$this->post_modified = date('Y-m-d H:i:s');
		$this->post_modified_gmt = gmdate('Y-m-d H:i:s');
		if (!$this->is_being_published and $this->post_date and $this->post_date_gmt)
			return;
		$this->post_date = $this->post_modified;
		$this->post_date_gmt = $this->post_modified_gmt;
	}

	public function validate()
	{
    $this->post_type = 'post';
		$this->update_post_dates();
		return parent::validate();
	}

	public function roles()
	{
		return array(
			'slug_generator' => array('\WPSpokes\Framework\Roles\SlugGenerator',
								'slug_source_attribute' => 'post_title',
								'slug_target_attribute' => 'post_name'));
	}

	public function scopeStatus($query, $status)
	{
		return $query->where('post_status', '=', $status);
	}

	public function scopeType($query, $type)
	{
		return $query->where('post_type', '=', $type);
	}

  public function newQuery($excludeDeleted = true)
  {
    return parent::newQuery($excludeDeleted)->where('post_type', '=', 'post');
  }
}
