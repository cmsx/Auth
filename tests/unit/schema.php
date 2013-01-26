<?php

require_once __DIR__ . '/../init.php';

use CMSx\Auth;
use CMSx\DB;
use CMSx\DB\Connection;

use CMSx\Auth\Schema\Sessions;
use CMSx\Auth\Schema\Users;

class SchemaTest extends PHPUnit_Framework_TestCase
{
  protected static $prefix;

  function testUsers()
  {
    $this->needConnection();
    createUsers();
    $this->dropAll();
  }

  function testSessions()
  {
    $this->needConnection();

    createUsers();
    createSessions();

    $this->dropAll();
  }

  /** Дроп таблиц в правильной последовательности */
  protected function dropAll()
  {
    dropSessions();
    dropUsers();
  }

  protected function needConnection()
  {
    try {
      Connection::Get();
    } catch (Exception $e) {
      $this->markTestSkipped('Не настроено подключение к БД: см. файл tests/config.php');
    }
  }

  public static function setUpBeforeClass()
  {
    self::$prefix = DB::GetPrefix();
    DB::SetPrefix('test_');
  }

  public static function tearDownAfterClass()
  {
    DB::SetPrefix(self::$prefix);
  }
}
