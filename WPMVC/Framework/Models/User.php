<?php

namespace WPMVC\Framework\Models;

class User extends \WPMVC\Framework\Model
{

    protected $primaryKey = 'ID';
    protected $table = 'wp_users';

	public function usermeta()
	{
		return $this->hasMany('\WPMVC\Framework\Models\UserMeta', 'user_id');
	}

    public function add_filters_to($filter_chain)
    {
        $filter_chain->filter(array('user_login', 'display_name', 'user_nicename'),
            new \WPMVC\Framework\Filters\StripTags());
    }

    public function add_rules_to($validator)
    {
        $validator->rule('required',
            array('user_login', 'user_pass', 'user_email', 
                'user_nicename', 'user_registered', 'display_name'));
        $validator->rule('email', 'user_email');
        $validator->rule('slug', 'user_nicename');
        $validator->rule('dateFormat', 'user_registered', 'Y-m-d H:i:s');
    }

    public function scopeCurrent($query)
    {
        $query->where('ID', '=', get_current_user_id());
    }

    public function set_password($password)
    {
        $this->user_pass = wp_hash_password($password);
    }    

    public function password_is_valid($password)
    {
        return wp_check_password($password, $this->user_pass);
    }

    public function validate()
    {
        if (!$this->user_registered)
            $this->user_registered = date('Y-m-d H:i:s');
        return parent::validate();
    }

	public function roles()
	{
		return array(
			'slug_generator' => array('\WPMVC\Framework\Roles\SlugGenerator',
								'slug_source_attribute' => 'display_name',
								'slug_target_attribute' => 'user_nicename'),
            'meta_owner' => array('\WPMVC\Framework\Roles\MetaOwner',
                                'relationship' => 'usermeta',
                                'meta_class_name' => '\WPMVC\Framework\Models\UserMeta'),
        );
	}
}
