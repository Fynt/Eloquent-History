<?php namespace Fynt\EloquentHistory;

class EloquentHistory {

  /**
   * Get the table name, where we will save the versions from the Laravel config.
   *
   * @return Illuminate\Database\Query\Builder
   */
  public static function getVersionsTable()
  {
    $tableName = Config::get('eloquent-history::table');
    return DB::table($tableName);
  }

}
