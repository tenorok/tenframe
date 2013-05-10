<?php

/**
 * Tenframe - PHP framework: github.com/tenorok/tenframe
 * @copyright 2012–2013 Artem Kurbatov, tenorok.ru
 * @license MIT license
 */

session_start();

require 'core.php';                                     // Подключение ядра
ten\core::init();

if(ten\core::dev(DEV)) {                                // Если включен режим разработчика

    require '../join.php';                              // Сборка файлов
    require '../statical.php';                          // Подключение файлов
}

if(
    $_SERVER['PHP_SELF'] != '/' . TEN_PATH . '/index.php' &&    // Если текущий адрес не index.php
    preg_match('/\.php$/', $_SERVER['PHP_SELF'])          &&    // а какой-то другой php-файл
    file_exists(ROOT .     $_SERVER['PHP_SELF'])                // и он существует
) {
    ten\route::$called = true;                                  // Маршрут считается проведённым
}

require 'request.php';                                  // Подключение функций обработки маршрутов
require '../routes.php';                                // Подключение файла маршрутизации