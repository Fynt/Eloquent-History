<?php namespace Fynt\EloquentHistory;

use Carbon\Carbon;

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
  public function __construct(DatabaseManager $db, Repository $config)
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
  public function add($action, Model $model, $user = null)
  {
    $userId = $this->getUserId($user);

    if ($model->exists()) {
      $table = $this->getHistoryTable();
      $key = $model->getKeyName();
      return $table->insert([
        'user_id' => $userId,
        'action' => $action,
        'object_id' => $model->$key,
        'object_table' => get_class($model),
        'created_at' => new Carbon
      ]);
    }

    return false;
  }

  /**
   * Get all history events.
   * @param int $limit Default unlimited
   * @return array
   */
  public function all($limit = null)
  {
    $table = $this->getHistoryTable();
    if ($limit)
      $table->take($limit);
    return $table->get();
  }

  /**
   * Get all history events for the provided user.
   * @param UserInterface|user $user
   * @param int|null | $limit
   * @return array
   */
  public function allForUser($user, $limit = null)
  {
    $userId = $this->getUserId($user);
    $table = $this->getHistoryTable();
    $table->whereUserId($userId);

    if ($limit)
      $table->take($limit);

    return $table->get();
  }

  /**
   * Get all history that relates the the specific model supplied.
   * @param Model $model
   * @param int|null $limit
   * @return array
   */
  public function allForModel(Model $model, $limit = null)
  {
    if (! $model->exists())
      return [];

    $key = $model->getKeyName();
    $table = $this->getHistoryTable();
    $table->whereObjectTable(get_class($model));
    $table->whereObjectId($model->$key);

    if ($limit)
      $table->take($limit);

    return $table->get();
  }

  /**
   * Get all history for the type of model provided.
   * @param Model $model
   * @param int|null $limit
   * @return array
   */
  public function allForModelType(Model $model, $limit = null)
  {
    $table = $this->getHistoryTable();
    $table->whereObjectTable(get_class($model));

    if ($limit)
      $table->take($limit);

    return $table->get();
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

  /**
   * Get the value for user ID from mixed input.
   * @param Model|int|null $user
   * @return int|null
   */
  protected function getUserId($user)
  {
    if ($user instanceof UserInterface) {
      $userId = $user->getAuthIdentifier();
    } else if (is_numeric($user)) {
      $userId = $user;
    } else {
      $userId = null;
    }
    return $userId;
  }

}
