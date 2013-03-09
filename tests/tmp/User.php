<?php

use CMSx\Auth\User as BaseUser;

/**
 * Пример класса унаследованного от базового
 */
class User extends BaseUser
{
  public function getId()
  {
    return $this->get('id');
  }

  public function setId($id)
  {
    return $this->set('id', $id);
  }

  public function getIsActive()
  {
    return $this->get('is_active');
  }

  public function setIsActive($is_active)
  {
    return $this->set('is_active', $is_active);
  }

  public function getRole()
  {
    return $this->get('role');
  }

  public function setRole($role)
  {
    return $this->set('role', $role);
  }

  public function getLogin()
  {
    return $this->get('login');
  }

  public function setLogin($login)
  {
    return $this->set('login', $login);
  }

  public function setPassword($password)
  {
    return $this->set('password', $password);
  }

  public function getName()
  {
    return $this->get('name');
  }

  public function setName($name)
  {
    return $this->set('name', $name);
  }

  public function getEmail()
  {
    return $this->get('email');
  }

  public function setEmail($email)
  {
    return $this->set('email', $email);
  }

  public function getPhone()
  {
    return $this->get('phone');
  }

  public function setPhone($phone)
  {
    return $this->set('phone', $phone);
  }

  public function getCreatedAt($format = null)
  {
    return $this->getAsDate('created_at', $format);
  }

  public function setCreatedAt($created_at)
  {
    return $this->setAsDate('created_at', $created_at);
  }
}