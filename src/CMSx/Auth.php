<?php

namespace CMSx;

class Auth
{
  /** Хеш от логина и пароля */
  public static function HashPassword($login, $pass)
  {
    return hash('whirlpool', hash('sha256', $login) . $pass);
  }
}