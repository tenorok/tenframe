<?php

require 'sys/require.php';                                  // Общие подключения

if(core::dev(DEV)) {                                        // Если включен режим разработчика

    require 'merge.php';                                    // Сборка файлов
    require 'include.php';                                  // Подключение файлов
}

require 'sys/request.php';                                  // Подключение функций обработки маршрутов
require 'routes.php';                                       // Подключение файла маршрутизации