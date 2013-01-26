<?php

use CMSx\DB\Connection;

/** Для выполнения части тестов необходимо указать подключение к тестовой базе */
//new Connection('localhost', 'igor', 'qwerty', 'cmsx_test', 'utf8');

function createUsers()
{
  $u = new \CMSx\Auth\Schema\Users();
  $u->createTable(true);
  $u->fillTable();
}

function createSessions()
{
  $s = new \CMSx\Auth\Schema\Sessions();
  $s->createTable(true);
  $s->fillTable();
}

function dropUsers()
{
  $u = new \CMSx\Auth\Schema\Users();
  \CMSx\DB::Drop($u->getTable())->execute();
}

function dropSessions()
{
  $s = new \CMSx\Auth\Schema\Sessions();
  \CMSx\DB::Drop($s->getTable())->execute();
}