<?php namespace Fynt\EloquentHistory;

use Illuminate\Auth\UserInterface;
use Illuminate\Config\Repository;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;

class History {

  protected $db;

  protected $config;

  /**
   * @param DatabaseManager $db
   * @param Repository $config
   */
  public function __construct($db, $config)
  {
    $this->db = $db;
    $this->config = $config;
  }

  /**
   * Adds a new history event.
   * @param string $action
   * @param Model $model
   * @param UserInterface|int|null $user
   * @return bool
   */
  public function add($action, $model, $user = null)
  {
    if ($user instanceof UserInterface) {
      $userId = $user->getAuthIdentifier();
    } else if (is_numeric($user)) {
      $userId = $user;
    } else {
      $userId = null;
    }

    if ($model->exists()) {
      $table = $this->getHistoryTable();
      $key = $model->getKeyName();
      return $table->insert([
        'user_id' => $userId,
        'action' => $action,
        'object_id' => $model->$key,
        'object_table' => get_class($model)
      ]);
    }

    return false;
  }

  /**
   * Get all history events.
   * @param int $limit Default unlimited
   * @return
   */
  public function all($limit = null)
  {
    $table = $this->getHistoryTable();
    if ($limit)
      $table->take($limit);
    return $table->get();
  }

  public function getHistoryForUser($user, $limit=null)
  {
    $userId = ($user instanceof User) ? $user->id : null;
    return self::getHistoryTable()
      ->whereUserId($userId)
      ->take($limit)
      ->get();
  }

  public function getHistoryForModel($model, $limit=null)
  {
    return self::getHistoryTable()
      ->whereObjectTable(get_class($model))
      ->take($limit)
      ->get();
  }

  /**
   * Get the table name, where we will save the versions from the Laravel config.
   *
   * @return Illuminate\Database\Query\Builder
   */
  protected function getHistoryTable()
  {
    $tableName = $this->config->get('eloquent-history::table');
    return $this->db->table($tableName);
  }

}
