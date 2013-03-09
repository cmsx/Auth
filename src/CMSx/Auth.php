<?php

namespace CMSx;

use CMSx\Auth\Exception;

class Auth
{
  /** Ошибка - сессия не запущена */
  const ERR_SESSION = 10;

  protected static $errors = array(
    self::ERR_SESSION => 'Сессия не запущена'
  );

  /** @var DB */
  protected $manager;

  /** Инициализация авторизации */
  public function __construct($manager = null, $session_autostart = true)
  {
    if (!is_null($manager)) {
      $this->setManager($manager);
    }

    self::CheckSessionStarted($session_autostart);
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

  /** Хеш от логина и пароля */
  public static function HashPassword($login, $pass)
  {
    return hash('whirlpool', hash('sha256', $login) . $pass);
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
}