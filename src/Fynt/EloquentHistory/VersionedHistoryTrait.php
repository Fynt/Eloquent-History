<?php namespace Fynt\EloquentHistory;

trait VersionedHistoryTrait {

  public static function bootVersionedHistoryTrait()
  {
    static::observe(new VersionedHistoryModelObserver);
  }

}
