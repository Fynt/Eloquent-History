<?php namespace Fynt\EloquentHistory;

trait HistoryTrait {

  public static function bootHistoryTrait()
  {
    static::observe(new ModelObserver);
  }

}
