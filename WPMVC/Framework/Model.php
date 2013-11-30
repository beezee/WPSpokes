<?php

namespace WPMVC\Framework;

class Model extends \Illuminate\Database\Eloquent\Model
{

	private $_roles=array();
	private $_errors=array();
	protected $table_inheritance_attribute = false;
	public $timestamps=false;

	public static $classes_inheriting_from_table = array();

	public function __construct($attributes=array())
	{
		parent::__construct($attributes);
		foreach($this->roles() as $name => $role)
			$this->add_role($name, Component::create($role));
		if ($this->inherits_from_table)
			$this->setAttribute($this->table_inheritance_attribute, 
				$this->table_inheritance_type);
	}

	public static function boot()
	{
		parent::boot();
		$class = get_called_class();
		$class::observe(new \WPMVC\Framework\ModelObserver());
	}

	public function add_rules_to(\Valitron\Validator $validator)
	{
		return $validator;
	}

	public function get_inherits_from_table()
	{
		$class = get_called_class();
		return array_key_exists(get_called_class(), $class::$classes_inheriting_from_table);
	}

	public function get_table_inheritance_type_value()
	{
		$class = get_called_class();
		return $class::$classes_inheriting_from_table[get_called_class()];
	}

	public function get_table_inheritance_type_class_from_value($value)
	{
		$class = get_called_class();
		return array_search($value, $class::$classes_inheriting_from_table);
	}

	public function newQuery($excludeDeleted=true)
	{
		$builder = parent::newQuery($excludeDeleted);
		if (!$this->inherits_from_table)
			return $builder;
		$builder->where($this->table_inheritance->attribute, 
			'=', $this->table_inheritance_type_value);
		return $builder;
	}

	public function newFromBuilder($attributes=array())
	{
		if (!$this->table_inheritance_attribute)	
			return parent::newFromBuilder($attributes);
		if (!$class = $this->get_table_inheritance_type_class_from_value(
				$attributes[$this->table_inheritance_attribute]))
			return parent::newFromBuilder($attributes);
		$instance = new $class();
		$instance->exists = true;
		$instance->setRawAttributes((array) $attributes, true);
		return $instance;
	}

    public function add_filters_to($filter_chain)
    {
        return;
    }

    public function filter()
    {
        $filter_chain = new \WPMVC\Framework\FilterChain();
        $this->add_filters_to($filter_chain);
        $filter_chain->apply_to($this);
    }

	public function validate()
	{
        $this->filter();
		if (!$this->call_method_on_roles('validating'))
			return false;
		$validator = new \Valitron\Validator($this->toArray());
		$this->add_rules_to($validator);
		$this->call_method_on_roles('add_rules_to', array($validator));
		$valid = $validator->validate();
		$this->_errors = $validator->errors();
		if (!$this->call_method_on_roles('validated'))
			return false;
		if ($valid)
			return true;
		return false;
	}

	public function save($options = array())
	{
		if (isset($options['validate']) and !$options['validate'])
			return parent::save($options);
		if (!$this->validate())
			return false;
		return parent::save($options);
	}

	public function get_errors()
	{
		return $this->_errors;
	}

    public function get_pk()
    {
        return $this->{$this->primaryKey};
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
		return parent::__get($name);
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
		return parent::__set($name, $value);
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
		return parent::__isset($name);
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
		return parent::__unset($name);
	}

	public function __call($name,$parameters)
	{
		if($this->_roles!==null)
			foreach($this->_roles as $object)
				if($object->enabled && method_exists($object,$name))
					return call_user_func_array(array($object,$name),$parameters);
		if(class_exists('Closure', false) && $this->can_get_property($name) && $this->$name instanceof Closure)
						return call_user_func_array($this->$name, $parameters);
		return parent::__call($name, $parameters);
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

	public function call_method_on_roles($method, $arguments=array())
	{
		$return = true;
		foreach($this->roles as $role)
			if (method_exists($role, $method))
				if (!call_user_func_array(array($role, $method), $arguments))
					$return = false;
		return $return;
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
