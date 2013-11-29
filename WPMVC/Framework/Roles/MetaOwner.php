<?php

namespace WPMVC\Framework\Roles;
use \_;

class MetaOwner extends \WPMVC\Framework\Role
{
    public $meta_key_attribute = 'meta_key';
    public $meta_value_attribute = 'meta_value';
    public $relationship = 'general_meta';

    public function get_meta()
    {
        return new MetaWrapper($this->owner->{$this->relationship}, $this->owner);
    }

    public function get_all_meta()
    {
        return new MetaWrapper($this->owner->{$this->relationship}, $this->owner, $array=true);
    }
}


class MetaWrapper
{
    private $_meta;
    private $_owner;
    private $_array=false;

    public function __construct($meta, $owner, $array=false)
    {
        $this->_meta = $meta;
        $this->_owner = $owner;
        $this->_array = $array;
    }

    public function first($name)
    {
        $owner = $this->_owner;
        return ($match = _::find($this->_meta, function($m) use ($name, $owner) {
                return $m->{$owner->meta_key_attribute} === $name; }))
            ? $match->{$owner->meta_value_attribute}
            : false;
    }

    public function all($name)
    {
        $owner = $this->_owner;
        return _::map(_::filter($this->_meta, function($m) use ($name, $owner) {
                    return $m->{$owner->meta_key_attribute} === $name; }),
                        function($m) use ($owner) {
                            return $m->{$owner->meta_value_attribute}; });
    }
                
    public function __get($name)
    {
        return ($this->_array) 
            ? $this->all($name)
            : $this->first($name);
    }

}
