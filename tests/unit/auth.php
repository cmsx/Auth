<?php

require_once __DIR__ . '/../init.php';

use CMSx\Auth;

class AuthTest extends PHPUnit_Framework_TestCase
{
  function testHash()
  {
    $exp =
      '87347c6ba864e8f5d0ad83eeed7a03964269f5ed5bd10aa16d8fb1146cd21ee14160b0b3713ce5258cdeebee8e7e8db4017d8fcd5e5f087d2d5bb75893167794';
    $this->assertEquals($exp, Auth::HashPassword('user', 'qwerty'), 'Хеш от логина и пароля');

    $this->assertNotEquals($exp, Auth::HashPassword('user2', 'qwerty'), 'Хеш зависит от логина');
  }

  function testInitWithoutSess()
  {
    try {
      Auth::Init(false);
      $this->fail('Без запущенной сессии выбрасывается исключение');
    } catch (\CMSx\Auth\Exception $e) {
      $this->assertEquals(Auth::ERR_SESSION, $e->getCode(), 'Код ошибки по сессии');
    }

    try {
      Auth::Init();
      Auth::CheckSessionStarted();
    } catch (\CMSx\Auth\Exception $e) {
      $this->fail('Инициализация с автостартом сессии - сессия не создалась');
    }
  }
}