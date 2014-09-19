<?php namespace Fynt\EloquentHistory;

use \DB;
use \User;
use \Config;

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
  public static function register($user, $action, $model)
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

  public static function getHistory($limit=null)
  {
    return self::getHistoryTable()->take($limit)->get();
  }

  public static function getHistoryForUser($user, $limit=null)
  {
    $userId = ($user instanceof User) ? $user->id : null;
    return self::getHistoryTable()
      ->whereUserId($userId)
      ->take($limit)
      ->get();
  }

  public static function getHistoryForModel($model, $limit=null)
  {
    return self::getHistoryTable()
      ->whereObjectTable(get_class($model))
      ->take($limit)
      ->get();
  }

}
