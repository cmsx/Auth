<?php

namespace CMSx\Auth\Schema;

use CMSx\DB;
use CMSx\DB\Schema;

class Users extends Schema
{
  public function getTable()
  {
    return 'users';
  }

  protected function init()
  {
    $this->getQuery()
      ->addId()
      ->addBool('is_active')
      ->addInt('role')
      ->addChar('login')
      ->addChar('password')
      ->addChar('name')
      ->addChar('email')
      ->addChar('phone')
      ->addTimeCreated()
      ->setType(DB::TYPE_InnoDB)
      ->addIndex('is_active', 'login', 'password')
    ;
  }
}