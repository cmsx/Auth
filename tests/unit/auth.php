<?php

require_once __DIR__ . '/../init.php';

use CMSx\Auth as BaseAuth;
use CMSx\Auth\Exception;

class Auth extends BaseAuth
{
  //Изображаем находящийся в сессии токен
  protected $token;

  public function __construct($manager = null, $session_autostart = true, $token = null)
  {
    if ($token) {
      $this->token = $token;
    }
    parent::__construct($manager, $session_autostart);
  }

  public function validatePass($pass)
  {
    if (!is_numeric($pass)) {
      static::ThrowError(static::ERR_BAD_PASSWORD);
    }

    return true;
  }

  public function validateUsername($user)
  {
    if (!preg_match('/^[a-zA-Z]+$/', $user)) {
      static::ThrowError(static::ERR_BAD_USERNAME);
    }

    return true;
  }

  // Мок для запроса в БД
  protected function findUser($user, $pass)
  {
    if ($user == 'abc' && $pass == 123) {
      return $this->mockUser();
    }

    return false;
  }

  protected function findUserByToken($token)
  {
    if ($token == 'abc') {
      return $this->mockUser();
    }

    return false;
  }

  protected function saveToken($user_id, $remindme = true)
  {
    //Ничего не делаем
  }

  protected function cleanup()
  {
    //Ничего не делаем
  }

  // Изображаем находящийся в сессии токен
  protected function getToken()
  {
    return $this->token;
  }

  protected function mockUser()
  {
    $u = new BaseAuth\User();
    $u->set('name', 'Hello');
    $u->grantRole(BaseAuth\User::ROLE_ADMIN);

    return $u;
  }
}

class AuthTest extends PHPUnit_Framework_TestCase
{
  function testLogin()
  {
    $a = new Auth(X::DB());

    $this->assertFalse($a->check(), 'Пользователь не авторизован');
    $this->assertFalse($a->check(BaseAuth\User::ROLE_ADMIN), 'Пользователь не администратор');

    try {
      $a->login('!!!!', 123);
      $this->fail('Недопустимые символы в логине');
    } catch (Exception $e) {
      $this->assertEquals(Auth::ERR_BAD_USERNAME, $e->getCode(), 'Код ошибки 1');
    }

    try {
      $a->login('abc', 'abc');
      $this->fail('Недопустимые символы в пароле');
    } catch (Exception $e) {
      $this->assertEquals(Auth::ERR_BAD_PASSWORD, $e->getCode(), 'Код ошибки 2');
    }

    try {
      $a->login('abc', 666);
      $this->fail('Пользователь не существует');
    } catch (Exception $e) {
      $this->assertEquals(Auth::ERR_WRONG_LOGIN_OR_PASS, $e->getCode(), 'Код ошибки 3');
    }

    $a->login('abc', 123);

    $this->assertTrue($a->check(), 'Пользователь авторизован');
    $this->assertTrue($a->check(BaseAuth\User::ROLE_ADMIN), 'Пользователь - администратор');
    $this->assertEquals('Hello', $a->getUser()->get('name'), 'Пользователь сохранен в объекте Auth');
  }

  function testReEnter()
  {
    $a = new Auth(X::DB(), true, 'cde');
    $this->assertFalse($a->check(), 'Неверный токен');

    $a = new Auth(X::DB(), true, 'abc');
    $this->assertTrue($a->check(), 'Пользователь авторизовался по токену');

    return $a;
  }

  /** @depends testReEnter */
  function testLogout(Auth $a)
  {
    $a->logout();
    $this->assertFalse($a->check(), 'Пользователь вышел');
  }
}