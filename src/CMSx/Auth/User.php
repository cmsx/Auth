<?php

namespace CMSx\Auth;

use CMSx\DB\Item;

class User extends Item
{
  /** Роль администратор */
  const ROLE_ADMIN = 1;

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
    return $this->get('role') | $role;
  }

  /** Проверка наличия роли Администратор */
  public function isAdmin()
  {
    return $this->hasRole(static::ROLE_ADMIN);
  }
}