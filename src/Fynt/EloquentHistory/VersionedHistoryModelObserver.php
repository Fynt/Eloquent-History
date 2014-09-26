<?php namespace Fynt\EloquentHistory;

use \Auth;
use \History;
use \Versions;

class VersionedHistoryModelObserver {

  const ACTION_CREATE = 'create';
  const ACTION_PUBLISH = 'publish';
  const ACTION_UPDATE = 'update';
  const ACTION_DELETE = 'delete';

  public function created($model)
  {
    History::register(Auth::user(), self::ACTION_CREATE, $model);
  }

  public function published($model)
  {
    History::register(Auth::user(), self::ACTION_PUBLISH, $model); 
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
