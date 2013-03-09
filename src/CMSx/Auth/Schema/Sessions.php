<?php

namespace CMSx\Auth\Schema;

use CMSx\DB;
use CMSx\DB\Schema;

class Sessions extends Schema
{
  public function getTable()
  {
    return 'sessions';
  }

  protected function init()
  {
    $this->getQuery()
      ->addChar('token')
      ->addUniqueIndex('token')
      ->addForeignId('user_id')
      ->addForeignKey('user_id', 'users', 'id', DB::FOREIGN_CASCADE)
      ->addInt('ip')
      ->addTime('expires_at')
      ->addIndex('user_id', 'expires_at')
    ;
  }
}