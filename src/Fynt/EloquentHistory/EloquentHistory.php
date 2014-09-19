<?php namespace Fynt\EloquentHistory;

use \User;

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
    $userId = ($user instanceof User) ? $user->id : null;

    if($model->id) {
      $creationDate = date('Y-m-d H:i:s');

      return self::getHistoryTable()->insert([
        'user_id' => $userId,
        'action' => $action,
        'object_id' => $model->id,
        'object_table' => get_class($model),
        'created_at' => $creationDate,
        'updated_at' => $creationDate
      ]);
    }

    return false;
  }

}
