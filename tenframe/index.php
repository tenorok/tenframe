<?php

/**
 * Tenframe - PHP framework: github.com/tenorok/tenframe
 * @copyright 2012–2013 Artem Kurbatov, tenorok.ru
 * @license MIT license
 */

require 'require.php';                                  // Общие подключения

if(ten\core::dev(DEV)) {                                // Если включен режим разработчика

    require '../merge.php';                             // Сборка файлов
    require '../include.php';                           // Подключение файлов
}

require 'request.php';                                  // Подключение функций обработки маршрутов
require '../routes.php';                                // Подключение файла маршрутизации