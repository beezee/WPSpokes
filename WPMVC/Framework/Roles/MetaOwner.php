<?php

namespace WPMVC\Framework\Roles;
use \_;

class MetaOwner extends \WPMVC\Framework\Role
{
    public $meta_key_attribute = 'meta_key';
    public $meta_value_attribute = 'meta_value';
    public $relationship = 'general_meta';
    public $meta_class_name = 'GeneralMeta';

    public function get_related_meta()
    {
        $meta = $this->owner->{$this->relationship};
        return _::sortBy($meta, function($m){
            return $m->pk * -1;
        });
    }

    public function get_meta()
    {
        return new MetaWrapper($this->related_meta, $this->owner);
    }

    public function get_all_meta()
    {
        return new MetaArrayWrapper($this->related_meta, $this->owner);
    }

    public function add_meta($key, $value)
    {
        $class_name = $this->meta_class_name;
        $meta = new $class_name();
        $meta->{$this->meta_key_attribute} = $key;
        $meta->{$this->meta_value_attribute} = $value;
        $this->owner->{$this->relationship}()->save($meta);
    }

    public function update_meta($key, $value)
    {
        $wrapper = new RawMetaWrapper($this->related_meta, $this->owner);
        if (!$meta = $wrapper->$key)
            return $this->add_meta($key, $value);
        $meta->{$this->meta_value_attribute} = $value;
        $meta->save();
    }

    public function delete_meta($key, $value=false)
    {
        $wrapper = new RawMetaArrayWrapper($this->related_meta, $this->owner);
        $value_attribute = $this->meta_value_attribute;
        $matches = ($value)
            ? _::filter($wrapper->$key, function($m) use($value, $value_attribute){
                       return $m->$value_attribute === $value;
              })
            : $wrapper->$key;
        foreach($matches as $match)
            $match->delete();
    }
        
}


class MetaWrapper
{
    protected  $_meta;
    protected  $_owner;

    public function __construct($meta, $owner)
    {
        $this->_meta = $meta;
        $this->_owner = $owner;
    }

    public function find($name)
    {
        $owner = $this->_owner;
        return ($match = _::find($this->_meta, function($m) use ($name, $owner) {
                return $m->{$owner->meta_key_attribute} === $name; }))
            ? $match->{$owner->meta_value_attribute}
            : false;
    }
                
    public function __get($name)
    {
        return $this->find($name);
    }

}

class MetaArrayWrapper extends MetaWrapper
{
    public function find($name)
    {
        $owner = $this->_owner;
        return _::map(_::filter($this->_meta, function($m) use ($name, $owner) {
                    return $m->{$owner->meta_key_attribute} === $name; }),
                        function($m) use ($owner) {
                            return $m->{$owner->meta_value_attribute}; });
    }
}

class RawMetaWrapper extends MetaWrapper
{

    public function find($name)
    {
        $owner = $this->_owner;
        return ($match = _::find($this->_meta, function($m) use ($name, $owner) {
                return $m->{$owner->meta_key_attribute} === $name; }))
            ? $match
            : false;
    }
}

class RawMetaArrayWrapper extends MetaWrapper
{
    public function find($name)
    {
        $owner = $this->_owner;
        return _::filter($this->_meta, function($m) use ($name, $owner) {
                    return $m->{$owner->meta_key_attribute} === $name; });
    }
}
