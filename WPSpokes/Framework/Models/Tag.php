<?php

namespace WPSpokes\Framework\Models;

class Tag extends \WPSpokes\Framework\Models\Taxonomy
{

  public function newQuery($excludeDeleted = true)
  {
    return parent::newQuery($excludeDeleted)->where('taxonomy', '=', 'post_tag');
  }

  public function on_saving()
  {
    $this->taxonomy = 'post_tag';
    return true;
  }
}
