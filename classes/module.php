<?php

// Класс работы с БД
require_once 'idb.php';
require_once 'db.php';

// Класс работы с файлами настроек
require_once 'iconfig.php';
require_once 'config.php';

// Класс работы с пользователями
require_once 'iusers.php';
require_once 'users.php';

// Класс работы с utm-метками
require_once 'iutm.php';
require_once 'utm.php';

// Класс конструирования HTML-элементов
require_once 'ihtmlelements.php';
require_once 'htmlelements.php';

if($activeModelsArray) {
  
  /* Стандартные классы */

  if( in_array('db', $activeModelsArray) ) {
    $db = new DB();
    $db->defaultConnect();
  }

  if( in_array('config', $activeModelsArray) ) {
    $config = new Config();
  }

  if( in_array('users', $activeModelsArray) ) {
    $users = new Users();
  }

  if( in_array('utm', $activeModelsArray) ) {
    $utm = new Utm();
  }

  if( in_array('htmlElements', $activeModelsArray) ) {
    $htmlElements = new HtmlElements();
  }

  /* Классы модулей */

  if( in_array('report', $activeModelsArray) ) {
    require_once 'report.php';
    $report = new Report();
  }

  if( in_array('check', $activeModelsArray) ) {
    require_once 'check.php';
    $check = new Check();
  }

  if( in_array('new-words', $activeModelsArray) ) {
    require_once 'new-words.php';
    $newWords = new NewWords();
  }

  if( in_array('settings', $activeModelsArray) ) {
    require_once 'settings.php';
    $settings = new Settings();
  }

}

?>


