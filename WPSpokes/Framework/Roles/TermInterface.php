<?php

namespace WPSpokes\Framework\Roles;

class TermInterface extends \WPSpokes\Framework\Role
{

	public function creating()
	{
		$t = new \WPSpokes\Framework\Models\Term();
		$t->name = $this->owner->name;
		if (!$t->save())
    {
      foreach($t->errors as $key => $errors)
        $this->owner->add_error($key, $errors);
      return false;
    }
		$this->owner->term()->associate($t);
    return true;
	}

	public function saved()
	{
		if (!$this->owner->name)
			return true;
		$this->owner->term->name = $this->owner->name;
		return $this->owner->term->save();
	}

}
