<?php

namespace CMSx\Auth;

use CMSx\DB\Item;
use CMSx\Auth\User\Exception as UserException;
use CMSx\DB;

class User extends Item
{
  /** Роль администратор */
  const ROLE_ADMIN = 1;

  /** Ошибка - роль не существует */
  const ERR_WRONG_ROLE = 10;

  /** Таблица с сессиями */
  protected static $sessions = 'sessions';

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
    return \X::DB();
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
      throw new UserException('Роль '.$role.' не существует', static::ERR_WRONG_ROLE);
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
      throw new UserException('Роль '.$role.' не существует', static::ERR_WRONG_ROLE);
    }

    $this->set('role', $role);
  }

  /** Проверка наличия роли Администратор */
  public function isAdmin()
  {
    return $this->hasRole(static::ROLE_ADMIN);
  }

  public function load($id = null)
  {
    parent::load($id);
    unset($this->vars['password']);

    return $this;
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

  /** Хеш от логина и пароля */
  public static function HashPassword($login, $pass)
  {
    return hash('whirlpool', hash('sha256', $login) . $pass);
  }

  /**
   * Поиск пользователя при авторизации
   *
   * @return $this
   */
  public static function FindForAuth($username, $password)
  {
    return static::FindOne(
      array(
        'is_active' => true,
        'login'     => $username,
        'password'  => static::HashPassword($username, $password)
      )
    );
  }

  /**
   * Поиск пользователя по токену
   *
   * @return $this
   */
  public static function FindByToken($token, $ip = null)
  {
    $w = array(
      'u.is_active' => 1,
      's.token'     => $token,
      '`expires_at` > NOW()'
    );
    $q = static::getManager()
      ->select(static::$sessions . ' s')
      ->columns('u.*')
      ->join(static::getTable() . ' u', 's.user_id = u.id');

    if ($ip) {
      $w[] = '`ip` = INET_ATON(:ip)';
      $q->bind('ip', $ip);
    }

    $q->where($w);

    return $q->fetchObject(get_called_class());
  }

  /** Создание токена для сессии */
  public static function CreateToken($user_id, $expire)
  {
    $token = md5(uniqid('token', true));
    $db = static::getManager();

    $q = 'INSERT INTO `' . $db->getPrefix() . static::$sessions . '` '
      . '(`user_id`, `ip`, `token`, `expires_at`) VALUES '
      . '(:user_id, INET_ATON(:ip), :token, :expires_at)';

    $arr = array(
      'user_id'    => $user_id,
      'token'      => $token,
      'ip'         => !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1',
      'expires_at' => date('Y-m-d H:i', $expire),
    );

    $db->query($q, $arr);

    return $token;
  }

  /** Удаление токена для сессии */
  public static function DeleteToken($token)
  {
    static::getManager()
      ->delete(static::$sessions)
      ->where('`token` = :token OR `expires_at` < NOW()')
      ->bind('token', $token)
      ->execute();

    return true;
  }

  protected function beforeSave($is_insert)
  {
    if ($p = $this->get('password')) {
      $this->set(
        'password',
        static::HashPassword($this->get('login'), $p)
      );
    }
  }

}