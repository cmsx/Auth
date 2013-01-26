<?php

namespace CMSx\Auth\Schema;

use CMSx\DB\Schema;
use CMSx\DB;

class Sessions extends Schema
{
  protected function init()
  {
    $this->table = 'sessions';
    $this->query = DB::Create($this->table)
      ->addChar('token', 250)
      ->addUniqueIndex('token')
      ->addForeignId('user_id')
      ->addForeignKey('user_id', 'users', 'id', DB::FOREIGN_CASCADE)
      ->addInt('ip')
      ->addTime('expires_at')
      ->addIndex('user_id', 'expires_at')
    ;
  }
}