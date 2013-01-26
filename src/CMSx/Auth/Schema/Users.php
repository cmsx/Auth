<?php

namespace CMSx\Auth\Schema;

use CMSx\DB\Schema;
use CMSx\DB;

class Users extends Schema
{
  protected function init()
  {
    $this->table = 'users';
    $this->query = DB::Create($this->table)
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

  public function fillTable()
  {

  }
}