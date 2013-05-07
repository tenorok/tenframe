<?php

session_start();

require 'define.php';

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

    torm::connect(                                              // Подключение к mysql
        $mysql['host'],
        $mysql['user'],
        $mysql['password']
    );

    torm::db($mysql['database']);                               // Выбор базы данных
}

tmod::init(core::$settings['modules']);                         // Инициализация модулей

unset($root, $query, $mysql);