<?php

if(stripos($_SERVER['PHP_SELF'], 'sys/index.php')) {            // Если выполняется обычный запрос
    list($root, $query) = explode('sys/index.php', $_SERVER['PHP_SELF']);
}
else {                                                          // Иначе выполняется ajax-запрос
    $root = '';
    $query = $_SERVER['PHP_SELF'];
}

define('ROOT', $_SERVER['DOCUMENT_ROOT'] . $root);              // Константа корневого пути

// Определение констант для автоподключения классов
define('SYS',        ROOT . '/');                               // Определение директории с классами системы
define('CONTROLLER', ROOT . '/app/controller/');                // Определение директории с классами контроллеров
define('MODEL',      ROOT . '/app/model/');                     // Определение директории с классами модели

define('BLOCKS',     ROOT . '/view/blocks/');                   // Константа директории блоков

require 'core.php';                                             // Подключение ядра
require ROOT . '/settings.php';                                 // Настройки работы фреймворка

define('DEV', ten\core::$settings['develop']);                  // Вкл/выкл режима разработчика

if(ten\core::$settings['clearURI']) {                           // Если задана маршрутизация только относительного пути

    define('URI', $query . (($_SERVER['QUERY_STRING']) ?        // Константа чистого запроса
        '?' . $_SERVER['QUERY_STRING'] : '')
    );
} else {
    define('URI', $_SERVER['REQUEST_URI']);                     // Константа полного запроса
}