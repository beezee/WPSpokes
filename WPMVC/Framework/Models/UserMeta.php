<?php

namespace WPMVC\Framework\Models;

class UserMeta extends \WPMVC\Framework\Model
{
    protected $primaryKey = 'umeta_id';
    protected $table = 'wp_usermeta';

    public function user()
    {
        return $this->belongsTo('\WPMVC\Framework\Models\User', 'user_id');
    }

}
