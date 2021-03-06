<?php

namespace WPSpokes\Framework\Models;

class Category extends \WPSpokes\Framework\Models\Taxonomy
{

  public function newQuery($excludeDeleted = true)
  {
    return parent::newQuery($excludeDeleted)->where('taxonomy', '=', 'category');
  }

  public function on_saving()
  {
    $this->taxonomy = 'category';
    return true;
  }
}
