<?php

/**
 * Tenframe - PHP framework: github.com/tenorok/tenframe
 * @copyright 2012–2013 Artem Kurbatov, tenorok.ru
 * @license MIT license
 */

require 'sys/require.php';                                  // Общие подключения

if(core::dev(DEV)) {                                        // Если включен режим разработчика

    require 'merge.php';                                    // Сборка файлов
    require 'include.php';                                  // Подключение файлов
}

require 'sys/request.php';                                  // Подключение функций обработки маршрутов
require 'routes.php';                                       // Подключение файла маршрутизации