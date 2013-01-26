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

  /** Инициализация авторизации */
  public static function Init($session_autostart = true)
  {
    self::CheckSessionStarted($session_autostart);
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
        session_start();
      } else {
        static::ThrowError(self::ERR_SESSION);
      }
    }
  }
}