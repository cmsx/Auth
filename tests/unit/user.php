<?php

require_once __DIR__ . '/../init.php';

use CMSx\Auth\User;
use CMSx\Auth\User\Exception;

class TestUser extends User
{
  const ROLE_SUPERHERO = 2;
  const ROLE_SUPEREVIL = 4;

  protected static $roles = array(
    self::ROLE_SUPERHERO => 'Супергерой',
    self::ROLE_SUPEREVIL => 'Суперзлодей',
  );
}

class UserTest extends PHPUnit_Framework_TestCase
{
  function testRole()
  {
    $u = new User;

    $this->assertFalse($u->hasRole(User::ROLE_ADMIN), 'Не админ 1');
    $this->assertFalse($u->isAdmin(), 'Не админ 2');

    $u->grantRole(User::ROLE_ADMIN);

    $this->assertTrue($u->hasRole(User::ROLE_ADMIN), 'Админ 1');
    $this->assertTrue($u->isAdmin(), 'Админ 2');

    $u = new TestUser;

    try {
      $u->grantRole(TestUser::ROLE_ADMIN);
      $this->fail('Роль Администратора не включена');
    } catch (Exception $e) {
      $this->assertEquals(User::ERR_WRONG_ROLE, $e->getCode(), 'Код ошибки 1');
    }

    $u->grantRole(TestUser::ROLE_SUPEREVIL);
    $u->grantRole(TestUser::ROLE_SUPERHERO);

    $this->assertTrue($u->hasRole(TestUser::ROLE_SUPERHERO), 'Супергерой 1');
    $this->assertTrue($u->hasRole(TestUser::ROLE_SUPEREVIL), 'Суперзлодей 1');

    $u->rejectRole(TestUser::ROLE_SUPEREVIL);

    $this->assertTrue($u->hasRole(TestUser::ROLE_SUPERHERO), 'Супергерой 2');
    $this->assertFalse($u->hasRole(TestUser::ROLE_SUPEREVIL), 'Суперзлодей 2');

    $u->switchRole(TestUser::ROLE_SUPEREVIL);

    $this->assertFalse($u->hasRole(TestUser::ROLE_SUPERHERO), 'Супергерой 3');
    $this->assertTrue($u->hasRole(TestUser::ROLE_SUPEREVIL), 'Суперзлодей 3');

    try {
      $u->switchRole(TestUser::ROLE_SUPEREVIL | TestUser::ROLE_SUPERHERO);
      $this->fail('Можно установить только одну роль, но не на группу');
    } catch (Exception $e) {
      $this->assertEquals(User::ERR_WRONG_ROLE, $e->getCode(), 'Код ошибки 2');
    }
  }

  function testHash()
  {
    $exp = '87347c6ba864e8f5d0ad83eeed7a03964269f5ed5bd10aa16d8fb1146cd21ee14160b0b3713ce5258cdeebee'
      . '8e7e8db4017d8fcd5e5f087d2d5bb75893167794';
    $this->assertEquals($exp, User::HashPassword('user', 'qwerty'), 'Хеш от логина и пароля');

    $this->assertNotEquals($exp, User::HashPassword('user2', 'qwerty'), 'Хеш зависит от логина');
  }
}