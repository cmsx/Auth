<?php

namespace CMSx\Auth;

use CMSx\DB\Item;
use CMSx\Auth\User\Exception;

class User extends Item
{
  /** Роль администратор */
  const ROLE_ADMIN = 1;

  /** Ошибка - роль не существует */
  const ERR_WRONG_ROLE = 10;

  protected static $roles = array(
    self::ROLE_ADMIN => 'Администратор'
  );

  /**
   * Менеджер подключения к БД
   *
   * @return DB
   */
  function getManager()
  {
    return X::DB();
  }

  /** Функция возвращает имя таблицы в БД */
  function getTable()
  {
    return 'users';
  }

  /** Проверка наличия роли у пользователя */
  public function hasRole($role)
  {
    return (bool)($this->get('role') & $role);
  }

  /** Добавление роли пользователю */
  public function grantRole($role)
  {
    if (!static::GetRoleName($role)) {
      throw new Exception('Роль '.$role.' не существует', static::ERR_WRONG_ROLE);
    }

    $this->set('role', ($this->get('role') | $role));
  }

  /** Запрет роли пользователю */
  public function rejectRole($role)
  {
    $this->set('role', ($this->get('role') & ~ $role));
  }

  /** Переключение роли - отключает все другие роли */
  public function switchRole($role)
  {
    if (!static::GetRoleName($role)) {
      throw new Exception('Роль '.$role.' не существует', static::ERR_WRONG_ROLE);
    }

    $this->set('role', $role);
  }

  /** Проверка наличия роли Администратор */
  public function isAdmin()
  {
    return $this->hasRole(static::ROLE_ADMIN);
  }

  /** Список возможных ролей пользователя */
  public static function GetRoles()
  {
    return static::$roles;
  }

  /** Название роли пользователя */
  public static function GetRoleName($role)
  {
    $r = static::GetRoles();

    return isset($r[$role]) ? $r[$role] : false;
  }
}