<?php

session_start();

list($root, $query) = explode('sys/index.php', $_SERVER['PHP_SELF']);

define('ROOT', $_SERVER['DOCUMENT_ROOT'] . $root);              // Константа корневого пути

// Определение констант для автоподключения классов
define('SYS',        ROOT . '/sys/classes/');                   // Определение директории с классами системы
define('CONTROLLER', ROOT . '/app/controller/');                // Определение директории с классами контроллеров
define('MODEL',      ROOT . '/app/model/');                     // Определение директории с классами модели

define('BLOCKS',     ROOT . '/view/blocks/');                   // Константа директории блоков

require 'core.php';                                             // Подключение ядра
require ROOT . '/settings.php';                                 // Настройки работы фреймворка

define('DEV', core::$settings['develop']);                      // Вкл/выкл режима разработчика

if(core::$settings['clearURI']) {                               // Если задана маршрутизация только относительного пути

    define('URI', $query . (($_SERVER['QUERY_STRING']) ?        // Константа чистого запроса
        '?' . $_SERVER['QUERY_STRING'] : '')
    );
} else {
    define('URI', $_SERVER['REQUEST_URI']);                     // Константа полного запроса
}

spl_autoload_register(array('core', 'auto_load'));              // Включение автоподгрузки классов

if(!DEV)                                                        // Если выключен режим разработчика
    error_reporting(0);                                         // Отключение отображения ошибок интерпретатора
else
    error_reporting(E_ALL);                                     // Включение отображения всех ошибок интерпретатора

register_shutdown_function(array('core', 'shutdown'));          // Указание метода, который будет вызван по окончании выполнения всего скрипта

if(
    $_SERVER['PHP_SELF'] != '/sys/index.php'     &&             // Если текущий адрес не index.php
    preg_match('/\.php$/', $_SERVER['PHP_SELF']) &&             // а какой-то другой php-файл
    file_exists(ROOT .     $_SERVER['PHP_SELF'])                // и он существует
) {
    core::$called = true;                                       // Маршрут считается проведённым
}

if(isset(core::$settings['mysql']) && core::$settings['mysql']) {

    $mysql = core::$settings['mysql'];
    
    orm::connect(                                               // Подключение к mysql
        $mysql['host'],
        $mysql['user'],
        $mysql['password']
    );
    
    orm::db($mysql['database']);                                // Выбор базы данных
}

mod::init(core::$settings['modules']);                          // Инициализация модулей

unset($root, $query, $mysql);