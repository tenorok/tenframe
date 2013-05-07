<?php

session_start();

require 'define.php';

spl_autoload_register(array('ten\core', 'auto_load'));          // Включение автоподгрузки классов

if(isset(ten\core::$settings['autoload'])) {                    // Добавление путей автоматической загрузки классов
    ten\core::$paths = array_merge(
        ten\core::$paths,
        ten\core::$settings['autoload']
    );
}

if(!DEV)                                                        // Если выключен режим разработчика
    error_reporting(0);                                         // Отключение отображения ошибок интерпретатора
else
    error_reporting(E_ALL);                                     // Включение отображения всех ошибок интерпретатора

register_shutdown_function(array('ten\core', 'shutdown'));      // Указание метода, который будет вызван по окончании выполнения всего скрипта

if(
    $_SERVER['PHP_SELF'] != '/' . TEN_PATH . '/index.php' &&    // Если текущий адрес не index.php
    preg_match('/\.php$/', $_SERVER['PHP_SELF'])          &&    // а какой-то другой php-файл
    file_exists(ROOT .     $_SERVER['PHP_SELF'])                // и он существует
) {
    ten\route::$called = true;                                  // Маршрут считается проведённым
}

if(isset(ten\core::$settings['mysql']) && ten\core::$settings['mysql']) {

    $mysql = ten\core::$settings['mysql'];

    ten\orm::connect(                                           // Подключение к mysql
        $mysql['host'],
        $mysql['user'],
        $mysql['password']
    );

    ten\orm::db($mysql['database']);                            // Выбор базы данных
}

ten\module::init(ten\core::$settings['modules']);               // Инициализация модулей

unset($root, $query, $mysql);