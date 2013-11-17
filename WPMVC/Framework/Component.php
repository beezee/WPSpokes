<?php
//credit https://github.com/yiisoft/yii/blob/1.1.14/framework/base/CComponent.php

namespace WPMVC\Framework;
use \Exception;

class Component 
{
	private $_roles=array();

	public static function create($args)
	{
		$class = array_shift($args);
		$instance = new $class();
		foreach($args as $property => $value)
			$instance->$property = $value;
		return $instance;
	}

	public function __construct()
	{
		foreach($this->roles() as $name => $role)
			$this->add_role($name, Component::create($role));
	}

	public function __get($name)
	{
		$getter='get_'.$name;
		if(method_exists($this,$getter))
			return $this->$getter();
		elseif(isset($this->_roles[$name]))
			return $this->_roles[$name];
		elseif(is_array($this->_roles))
			foreach($this->_roles as $object)
				if($object->enabled && (property_exists($object,$name) || $object->can_get_property($name)))
					return $object->$name;
		throw new Exception('Property "'.get_class($this).'.'.$name.'" is not defined.');
	}

	public function __set($name,$value)
	{
		$setter='set_'.$name;
		if(method_exists($this,$setter))
					return $this->$setter($value);
		elseif(is_array($this->_roles))
			foreach($this->_roles as $object)
				if($object->enabled && (property_exists($object,$name) || $object->can_set_property($name)))
					return $object->$name=$value;
		if(method_exists($this,'get_'.$name))
						throw new Exception('Property "'.get_class($this).'.'.$name.'" is read only.');
		else
						throw new Exception('Property "'.get_class($this).'.'.$name.'" is not defined.');
	}

	public function __isset($name)
	{
		$getter='get_'.$name;
		if(method_exists($this,$getter))
			return $this->$getter()!==null;
		elseif(is_array($this->_roles))
		{
			if(isset($this->_roles[$name]))
				return true;
			foreach($this->_roles as $object)
				if($object->enabled && (property_exists($object,$name) || $object->can_get_property($name)))
					return $object->$name!==null;
		}
		return false;
	}

	public function __unset($name)
	{
		$setter='set_'.$name;
		if(method_exists($this,$setter))
			$this->$setter(null);
		elseif(is_array($this->_roles))
		{
			if(isset($this->_roles[$name]))
				$this->remove_role($name);
			else
				foreach($this->_roles as $object)
					if($object->get_enabled())
					{
						if(property_exists($object,$name))
							return $object->$name=null;
						elseif($object->can_set_property($name))
							return $object->$setter(null);
					}
		}
		elseif(method_exists($this,'get_'.$name))
						throw new Exception('Property "'.get_class($this).'.'.$name.'" is read only.');
	}

	public function __call($name,$parameters)
	{
		if($this->_roles!==null)
			foreach($this->_roles as $object)
				if($object->enabled && method_exists($object,$name))
					return call_user_func_array(array($object,$name),$parameters);
		if(class_exists('Closure', false) && $this->can_get_property($name) && $this->$name instanceof Closure)
						return call_user_func_array($this->$name, $parameters);
		throw new Exception(get_class($this).' and its behaviors do not have a method or closure named "'.$name.'".');
	}

	public function roles()
	{
		return array();
	}

	public function get_role($role)
	{
		return isset($this->_roles[$role]) ? $this->_roles[$role] : null;
	}

	public function get_roles()
	{
		return $this->_roles;
	}

	public function remove_all_roles()
	{
		$this->_roles = array();
	}

	public function add_role($name, $role)
	{
		if(!($role instanceof \WPMVC\Framework\Role))
			return;
		$role->assign_to($this);
		return $this->_roles[$name]=$role;
	}

	public function remove_role($name)
	{
		if(!isset($this->_roles[$name]))
			return;
		$this->_roles[$name]->remove_from($this);
		$role=$this->_roles[$name];
		unset($this->_roles[$name]);
		return $role;
	}

	public function hasProperty($name)
	{
		return method_exists($this,'get_'.$name) || method_exists($this,'set_'.$name);
	}

	public function can_get_property($name)
	{
		return method_exists($this,'get_'.$name);
	}

	public function can_set_property($name)
	{
		return method_exists($this,'set_'.$name);
	}

	public function value($attribute, $default='', $model=false)
	{
		if (!$model)
			$model = $this;
		if(!is_scalar($attribute) and $attribute!==null)
			return call_user_func($attribute,$model);
		foreach(explode('.',$attribute) as $name)
		{
		    if(is_object($model) && isset($model->$name))
				$model=$model->$name;
		    elseif(is_array($model) && isset($model[$name]))
				$model=$model[$name];
		    else
				return $default;
		}
		return $model;
	}
}
