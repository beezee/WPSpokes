<?php

namespace WPSpokes\Framework\Models;

class UserMeta extends \WPSpokes\Framework\Model
{
    protected $primaryKey = 'umeta_id';
    protected $table = 'wp_usermeta';

    public function user()
    {
        return $this->belongsTo('\WPSpokes\Framework\Models\User', 'user_id');
    }

}
