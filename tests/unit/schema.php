<?php

require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/../tmp/User.php';

use CMSx\Auth;

class SchemaTest extends PHPUnit_Framework_TestCase
{
  protected static $prefix;

  function testUserCreation()
  {
    $u = $this->createUser();

    $this->assertEquals(1, $u->getId(), 'ID пользователя');

    $this->assertFalse(User::FindForAuth('bad', 'auth'), 'Пользователь не найден');

    $u1 = User::FindForAuth('test', 'qwerty');
    $this->assertNotEmpty($u1, 'Пользователь найден');

    $this->assertEquals('Test User', $u1->getName(), 'Пользователь найден корректно');
  }

  function testTokenUsage()
  {
    $this->createUser();

    $t1 = $this->createToken();
    $t2 = $this->createToken(true);

    $u = User::FindByToken($t2);
    $this->assertFalse($u, 'Просроченный токен');

    $u = User::FindByToken($t1, '192.168.1.1');
    $this->assertFalse($u, 'Верный токен, неверный IP');

    $u = User::FindByToken($t1);
    $this->assertNotEmpty($u, 'Юзер найден');
    $this->assertEquals('Test User', $u->getName(), 'Получен верный пользователь');
  }

  function testTokenCreation()
  {
    $u1 = $this->createUser();
    $token = User::CreateToken($u1->getId(), strtotime('+1 day'));

    $u2 = User::FindByToken($token);
    $this->assertNotEmpty($u2, 'Пользователь найден');
    $this->assertEquals($u1->getName(), $u2->getName(), 'Данные пользователя верны');
  }

  function testInactiveUser()
  {
    $this->createUser(false);
    $t = $this->createToken();

    $this->assertFalse(Auth\User::FindForAuth('test', 'qwerty'), 'Авторизация неактивного пользователя');
    $this->assertFalse(Auth\User::FindByToken($t), 'Поиск по токену');
  }

  protected function createUser($active = true)
  {
    $u = new User;
    $u->setIsActive($active);
    $u->setRole(User::ROLE_ADMIN);
    $u->setLogin('test');
    $u->setName('Test User');
    $u->setPassword('qwerty');
    $u->save();

    return $u;
  }

  protected function createToken($expired = false)
  {
    $s = new Auth\Schema\Sessions(X::DB());
    $token = md5(uniqid('', true));

    X::DB()->insert($s->getTable())
      ->set('token', $token)
      ->set('user_id', 1)
      ->set('ip', ip2long('127.0.0.1'))
      ->set('expires_at', date('Y-m-d H:i', strtotime($expired ? '-1 day' : '+1 day')))
      ->execute();

    return $token;
  }

  protected function createAll()
  {
    createUsers();
    createSessions();
  }

  /** Дроп таблиц в правильной последовательности */
  protected function dropAll()
  {
    dropSessions();
    dropUsers();
  }

  protected function setUp()
  {
    $this->dropAll();
    $this->createAll();
  }

  protected function tearDown()
  {
    $this->dropAll();
  }
}
