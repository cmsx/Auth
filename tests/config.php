<?php

class X extends CMSx\X
{

}

/** Для выполнения части тестов необходимо указать подключение к тестовой базе */
X::AddConnection('localhost', 'test', 'test', 'test', 'UTF8');
define('PREFIX', 'test_');

function createUsers()
{
  $u = new \CMSx\Auth\Schema\Users(X::DB());
  $u->createTable(true);
  $u->fillTable();
}

function createSessions()
{
  $s = new \CMSx\Auth\Schema\Sessions(X::DB());
  $s->createTable(true);
  $s->fillTable();
}

function dropUsers()
{
  $u = new \CMSx\Auth\Schema\Users(X::DB());
  X::DB()->drop($u->getTable())->execute();
}

function dropSessions()
{
  $s = new \CMSx\Auth\Schema\Sessions(X::DB());
  X::DB()->drop($s->getTable())->execute();
}