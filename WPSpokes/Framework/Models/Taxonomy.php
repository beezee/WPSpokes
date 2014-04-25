<?php

namespace WPSpokes\Framework\Models;

class Taxonomy extends \WPSpokes\Framework\Model
{
  protected $guarded = array('count');
	protected $primaryKey='term_taxonomy_id';
	protected $table_inheritance_attribute = 'taxonomy';
	private $_name;

	public function setNameAttribute($name)
	{
		$this->_name = $name;		
	}

	public function get_name()
	{
		return $this->_name
			?: $this->value('term.name');
	}

  public function setSlugAttribute($slug)
  {
    return;
  }

	public function get_slug()
	{
		return $this->value('term.slug');
	}


	public static $classes_inheriting_from_table 
		= array('\WPSpokes\Framework\Models\Tag' => 'post_tag', 
			'\WPSpokes\Framework\Models\Category' => 'category');

	public function __construct($attributes=array())
	{
		global $wpdb;
		$this->table = $wpdb->prefix.'term_taxonomy';
		return parent::__construct($attributes);
	}

  public static function synchronize_counts()
  {
    global $wpdb;
    $sql = 'update wp_term_taxonomy as tt 
      left join (select term_taxonomy_id as tid, count(term_taxonomy_id) as real_count
                  from wp_term_relationships as tr
                  left join wp_posts as p on tr.object_id = p.ID
                  where p.post_status = "publish"
                  group by tid)
        as rc 
      on rc.tid = tt.term_taxonomy_id
      set tt.count = rc.real_count';
    $wpdb->query($sql);
  }

	public function posts()
	{
		global $wpdb;
		return $this->belongsToMany('\WPSpokes\Framework\Models\Post',
			$wpdb->prefix.'term_relationships', 'term_taxonomy_id', 'object_id');
	}

	public function term()
	{
		return $this->belongsTo('\WPSpokes\Framework\Models\Term', 'term_id');
	}


	public function roles()
	{
		return array(
			'term_interface' => array('\WPSpokes\Framework\Roles\TermInterface'));
	}

  public function newQuery($excludeDeleted = true)
  {
    return parent::newQuery($excludeDeleted)->with('term');
  }
}
