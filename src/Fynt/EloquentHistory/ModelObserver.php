<?php namespace Fynt\EloquentHistory;

use \Auth;
use \History;

class ModelObserver {

  const ACTION_CREATE = 'create';
  const ACTION_UPDATE = 'update';
  const ACTION_DELETE = 'delete';

  public function created($model)
  {
    History::register(Auth::user(), self::ACTION_CREATE, $model);
  }

  public function updated($model)
  {
    History::register(Auth::user(), self::ACTION_UPDATE, $model);
  }

  public function deleted($model)
  {
    History::register(Auth::user(), self::ACTION_DELETE, $model);
  }

}
