<?php namespace Fynt\EloquentHistory;

trait HistoryTrait {

  public static function bootSearchableTrait()
  {
    static::observe(new ModelObserver);
  }

}
