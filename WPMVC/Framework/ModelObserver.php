<?php

namespace WPMVC\Framework;

class ModelObserver
{

	public function creating($model)
	{
		return $model->call_method_on_roles('creating', array($model));
	}

	public function created($model)
	{
		return $model->call_method_on_roles('created', array($model));
	}

	public function updating($model)
	{
		return $model->call_method_on_roles('updating', array($model));
	}

	public function updated($model)
	{
		return $model->call_method_on_roles('updated', array($model));
	}

	public function saving($model)
	{
		return $model->call_method_on_roles('saving', array($model));
	}

	public function saved($model)
	{
		return $model->call_method_on_roles('saved', array($model));
	}

	public function deleting($model)
	{
		return $model->call_method_on_roles('deleting', array($model));
	}

	public function deleted($model)
	{
		return $model->call_method_on_roles('deleted', array($model));
	}

	public function restoring($model)
	{
		return $model->call_method_on_roles('restoring', array($model));
	}

	public function restored($model)
	{
		return $model->call_method_on_roles('restored', array($model));
	}

}
