<?php

//use Illuminate\Auth\UserInterface;
use Illuminate\Config\Repository;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

use Fynt\EloquentHistory\History;

class EloquentHistoryTest extends PHPUnit_Framework_TestCase {

  /**
   * @var Model
   */
  protected $model;

  /**
   * @var Repository
   */
  protected $config;

  /**
   * @var DatabaseManager;
   */
  protected $db;

  /**
   * @var EloquentHistory
   */
  protected $history;

  /**
   * @var Builder
   */
  protected $queryBuilder;

  public function setUp()
  {
    parent::setUp();
    // so many mocks...
    $this->model = Mockery::mock('MockModel'); // lawl
    $this->config = Mockery::mock('Repository');
    $this->db = Mockery::mock('DatabaseManager');
    $this->queryBuilder = Mockery::mock('Builder');

    $this->history = new History($this->db, $this->config);
  }

  public function tearDown()
  {
    Mockery::close();
  }

  public function testAdd()
  {
    $this->assertDbTableLoads();
    $this->assertModelIdAccessed();

    $this->queryBuilder->shouldReceive('insert')
      ->once()
      ->with([
          'user_id' => null,
          'action' => 'Testing',
          'object_id' => 1,
          'object_table' => get_class($this->model)
        ])
      ->andReturn(true);

    $result = $this->history->add('Testing', $this->model);
    $this->assertTrue($result);
  }

  public function testAddWithUserId()
  {
    $this->assertDbTableLoads();
    $this->assertModelIdAccessed();

    $this->queryBuilder->shouldReceive('insert')
      ->once()
      ->with([
          'user_id' => 1,
          'action' => 'Testing',
          'object_id' => 1,
          'object_table' => get_class($this->model)
        ])
      ->andReturn(true);

    $result = $this->history->add('Testing', $this->model, 1);
    $this->assertTrue($result);
  }

  public function testAddWithUser()
  {
    $this->assertDbTableLoads();
    $this->assertModelIdAccessed();

    $user = Mockery::mock('Illuminate\Auth\UserInterface')
      ->shouldReceive('getAuthIdentifier')
      ->andReturn(2)
      ->mock();

    $this->queryBuilder->shouldReceive('insert')
      ->once()
      ->with([
          'user_id' => 2,
          'action' => 'Testing',
          'object_id' => 1,
          'object_table' => get_class($this->model)
        ])
      ->andReturn(true);

    $result = $this->history->add('Testing', $this->model, $user);
    $this->assertTrue($result);
  }

  public function testAddFailsWithUnsavedModel()
  {
    $this->model->shouldReceive('exists')->once()->andReturn(false);
    $result = $this->history->add('Testing', $this->model);
    $this->assertFalse($result);
  }

  public function testAll()
  {
    $this->assertDbTableLoads();
    $this->queryBuilder->shouldReceive('get')->once()->andReturn([]);

    $result = $this->history->all();
    $this->assertEquals($result, []);
  }

  public function testAllWithLimit()
  {
    $this->assertDbTableLoads();
    $this->queryBuilder->shouldReceive('take')->once()->with(10);
    $this->queryBuilder->shouldReceive('get')->once()->andReturn([]);

    $result = $this->history->all(10);
    $this->assertEquals($result, []);
  }

  public function testAllForUser()
  {
    $user = Mockery::mock('Illuminate\Auth\UserInterface')
      ->shouldReceive('getAuthIdentifier')
      ->andReturn(2)
      ->mock();

    $this->assertDbTableLoads();
    $this->queryBuilder->shouldReceive('whereUserId')->once()->with(2);
    $this->queryBuilder->shouldReceive('get')->once()->andReturn([]);

    $result = $this->history->allForUser($user);
    $this->assertEquals($result, []);
  }

  public function testAllForUserWithLimit()
  {
    $user = Mockery::mock('Illuminate\Auth\UserInterface')
      ->shouldReceive('getAuthIdentifier')
      ->andReturn(2)
      ->mock();

    $this->assertDbTableLoads();
    $this->queryBuilder->shouldReceive('whereUserId')->once()->with(2);
    $this->queryBuilder->shouldReceive('take')->once()->with(10);
    $this->queryBuilder->shouldReceive('get')->once()->andReturn([]);

    $result = $this->history->allForUser($user, 10);
    $this->assertEquals($result, []);
  }

  public function testAllForModel()
  {
    $this->assertDbTableLoads();
    $this->assertModelIdAccessed();
    $this->queryBuilder->shouldReceive('whereObjectTable')->once()->with(get_class($this->model));
    $this->queryBuilder->shouldReceive('whereObjectId')->once()->with(1);
    $this->queryBuilder->shouldReceive('get')->once()->andReturn([]);

    $result = $this->history->allForModel($this->model);
    $this->assertEquals($result, []);
  }

  public function testAllForModelWithLimit()
  {
    $this->assertDbTableLoads();
    $this->assertModelIdAccessed();
    $this->queryBuilder->shouldReceive('whereObjectTable')->once()->with(get_class($this->model));
    $this->queryBuilder->shouldReceive('whereObjectId')->once()->with(1);
    $this->queryBuilder->shouldReceive('take')->once()->with(10);
    $this->queryBuilder->shouldReceive('get')->once()->andReturn([]);

    $result = $this->history->allForModel($this->model, 10);
    $this->assertEquals($result, []);
  }

  public function testAllForModelType()
  {
    $this->assertDbTableLoads();
    $this->queryBuilder->shouldReceive('whereObjectTable')->once()->with(get_class($this->model));
    $this->queryBuilder->shouldReceive('get')->once()->andReturn([]);

    $result = $this->history->allForModelType($this->model);
    $this->assertEquals($result, []);
  }

  public function testAllForModelTypeWithLimit()
  {
    $this->assertDbTableLoads();
    $this->queryBuilder->shouldReceive('whereObjectTable')->once()->with(get_class($this->model));
    $this->queryBuilder->shouldReceive('take')->once()->with(10);
    $this->queryBuilder->shouldReceive('get')->once()->andReturn([]);

    $result = $this->history->allForModelType($this->model, 10);
    $this->assertEquals($result, []);
  }


  protected function assertDbTableLoads()
  {
    $this->config->shouldReceive('get')->once()->with('eloquent-history::table')->andReturn('history');
    $this->db->shouldReceive('table')->once()->with('history')->andReturn($this->queryBuilder);
  }

  protected function assertModelIdAccessed()
  {
    $this->model->shouldReceive('exists')->once()->andReturn(true);
    $this->model->shouldReceive('getKeyName')->once()->andReturn('id');
    $this->model->shouldReceive('getAttribute')->once()->with('id')->andReturn(1);
  }
}

class MockModel extends Model {}
