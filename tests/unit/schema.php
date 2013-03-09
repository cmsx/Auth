<?php

require_once __DIR__ . '/../init.php';

use CMSx\Auth;
use CMSx\DB;

class SchemaTest extends PHPUnit_Framework_TestCase
{
  protected static $prefix;

  function testTableCreation()
  {
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
}
