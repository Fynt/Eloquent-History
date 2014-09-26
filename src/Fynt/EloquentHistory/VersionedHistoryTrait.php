<?php namespace Fynt\EloquentHistory;

use Fynt\EloquentVersions\VersionedTrait;

trait VersionedHistoryTrait {

  use VersionedTrait;

  public static function bootVersionedHistoryTrait()
  {
    static::observe(new VersionedHistoryModelObserver);
  }

}
