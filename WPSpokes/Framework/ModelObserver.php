<?php

namespace WPSpokes\Framework;

class ModelObserver
{

	public function creating($model)
	{
		return $model->on_creating() and $model->call_method_on_roles('creating', array($model));
	}

	public function created($model)
	{
		return $model->on_created() and $model->call_method_on_roles('created', array($model));
	}

	public function updating($model)
	{
		return $model->on_updating() and $model->call_method_on_roles('updating', array($model));
	}

	public function updated($model)
	{
		return $model->on_updated() and $model->call_method_on_roles('updated', array($model));
	}

	public function saving($model)
	{
		return $model->on_saving() and $model->call_method_on_roles('saving', array($model));
	}

	public function saved($model)
	{
		return $model->on_saved() and $model->call_method_on_roles('saved', array($model));
	}

	public function deleting($model)
	{
		return $model->on_deleting() and $model->call_method_on_roles('deleting', array($model));
	}

	public function deleted($model)
	{
		return $model->on_deleted() and $model->call_method_on_roles('deleted', array($model));
	}

	public function restoring($model)
	{
		return $model->on_restoring() and $model->call_method_on_roles('restoring', array($model));
	}

	public function restored($model)
	{
		return $model->on_restored() and $model->call_method_on_roles('restored', array($model));
	}

}
