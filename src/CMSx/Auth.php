<?php

namespace CMSx;

use CMSx\Auth\User;
use CMSx\Auth\Exception;

class Auth
{
  /** Ошибка - сессия не запущена */
  const ERR_SESSION = 10;
  /** Некорректно указано имя пользователя */
  const ERR_BAD_USERNAME = 20;
  /** Некорректно указан пароль */
  const ERR_BAD_PASSWORD = 22;
  /** Логин или пароль неверные */
  const ERR_WRONG_LOGIN_OR_PASS = 30;

  /** Имя переменной в сессии для хранения токена */
  protected static $session_token = '_token';
  protected static $errors = array(
    self::ERR_SESSION             => 'Сессия не запущена',
    self::ERR_BAD_USERNAME        => 'Неверное имя пользователя',
    self::ERR_BAD_PASSWORD        => 'Неверный пароль',
    self::ERR_WRONG_LOGIN_OR_PASS => 'Пользователь не найден',
  );

  /** @var DB */
  protected $manager;

  /** @var User */
  protected $user;

  /** Инициализация авторизации */
  public function __construct($manager = null, $session_autostart = true)
  {
    if (!is_null($manager)) {
      $this->setManager($manager);
    }

    self::CheckSessionStarted($session_autostart);

    if ($token = $this->getToken()) {
      if (!$this->user = $this->findUserByToken($token)) {
        $this->cleanup();
      }
    }
  }

  /** @return User */
  public function getUser()
  {
    return $this->user ? : false;
  }

  /**
   * Проверка авторизован ли пользователь.
   * $role - обладает ли авторизованный пользователь нужной ролью
   */
  public function check($role = null)
  {
    if (!$u = $this->getUser()) {
      return false;
    }

    if ($role) {
      return $u->hasRole($role);
    }

    return true;
  }

  /** Авторизация по логину и паролю */
  public function login($user, $pass, $remindme = true)
  {
    $this->validateUsername($user);
    $this->validatePass($pass);

    if ($this->user = $this->findUser($user, $pass)) {
      $this->saveToken($this->getUser()->get('id'), $remindme);

      return true;
    }

    static::ThrowError(static::ERR_WRONG_LOGIN_OR_PASS);
  }

  /** Выход авторизованного пользователя */
  public function logout()
  {
    $this->user = false;
    $this->cleanup();
  }

  /** Проверка допустимого имени пользователя */
  public function validateUsername($user)
  {
//    Пример проверки логина:
//    if (!preg_match(REGULAR_LOGIN, $user)) {
//      static::ThrowError(static::ERR_BAD_USERNAME);
//    }

    return true;
  }

  /** Проверка допустимого пароля */
  public function validatePass($pass)
  {
//    Пример проверки пароля:
//    if (!preg_match(REGULAR_CLEAN, $pass)) {
//      static::ThrowError(static::ERR_BAD_PASSWORD);
//    }

    return true;
  }

  public function setManager(DB $manager)
  {
    $this->manager = $manager;

    return $this;
  }

  /** @return \CMSx\DB */
  public function getManager()
  {
    return $this->manager;
  }

  /**
   * Выброс исключения по коду ошибки
   * @throws \CMSx\Auth\Exception
   */
  public static function ThrowError($code)
  {
    throw new Exception(static::GetErrorMsg($code), $code);
  }

  /** Получение текста ошибки по коду */
  public static function GetErrorMsg($code)
  {
    return isset(static::$errors[$code])
      ? static::$errors[$code]
      : 'Неизвестная ошибка';
  }

  /** Проверка, что сессия запущена */
  public static function CheckSessionStarted($autostart = false)
  {
    $sid = session_id();
    if (empty($sid)) {
      if ($autostart) {
        @session_start();
        static::CheckSessionStarted();
      } else {
        static::ThrowError(self::ERR_SESSION);
      }
    }
  }

  /** Удаление токена из БД + чистка сессии и cookies */
  protected function cleanup()
  {
    if ($token = $this->getToken()) {
      $this->deleteToken($token);
    }

    setcookie(static::$session_token, false, strtotime('-1 day'), '/');
    unset($_SESSION[static::$session_token]);
  }

  /** Получение токена из Cookie или из сессии */
  protected function getToken()
  {
    if (!empty($_COOKIE[static::$session_token])) {
      return $_COOKIE[static::$session_token];
    } elseif (!empty($_SESSION[static::$session_token])) {
      return $_SESSION[static::$session_token];
    }

    return false;
  }

  /** Создание токена в БД, Сессии и установка Cookie */
  protected function saveToken($user_id, $remindme = true)
  {
    $expire = strtotime('+1 month');
    $token  = $this->createToken($user_id, $expire);

    if ($remindme) {
      setcookie(static::$session_token, $token, $expire, '/');
    } else {
      setcookie(static::$session_token, false, strtotime('yesterday'), '/');
    }

    $_SESSION[static::$session_token] = $token;
  }

  /** Сохранение токена в БД */
  protected function createToken($user_id, $expire)
  {
    return User::CreateToken($user_id, $expire);
  }

  /** Удаление токена из БД */
  protected function deleteToken($token)
  {
    return User::DeleteToken($token);
  }

  /** Поиск юзера по логину и паролю */
  protected function findUser($user, $pass)
  {
    return User::FindForAuth($user, $pass);
  }

  /** Поиск юзера по токену */
  protected function findUserByToken($token)
  {
    return User::FindByToken($token);
  }
}