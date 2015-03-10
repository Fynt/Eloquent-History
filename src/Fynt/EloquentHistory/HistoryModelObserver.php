<?php namespace Fynt\EloquentHistory;

use \Auth;
use \History;

class HistoryModelObserver {

  const ACTION_CREATE = 'create';
  const ACTION_UPDATE = 'update';
  const ACTION_DELETE = 'delete';

  public function created($model)
  {
    History::add(self::ACTION_CREATE, $model, Auth::user());
  }

  public function updated($model)
  {
    History::add(self::ACTION_UPDATE, $model, Auth::user());
  }

  public function deleted($model)
  {
    History::add(self::ACTION_DELETE, $model, Auth::user());
  }

}
