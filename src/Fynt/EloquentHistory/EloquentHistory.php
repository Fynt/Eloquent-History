<?php namespace Fynt\EloquentHistory;

class EloquentHistory {

  /**
   * Get the table name, where we will save the versions from the Laravel config.
   *
   * @return Illuminate\Database\Query\Builder
   */
  public static function getHistoryTable()
  {
    $tableName = Config::get('eloquent-history::table');
    return DB::table($tableName);
  }

  /**
   * Registers a new history entry.
   *
   * @param User|null $user
   * @param string $action
   * @param Elequent $model
   * @return bool
   */
  public static function register($user, $action, Eloquent $model)
  {
    return true;
  }

}
