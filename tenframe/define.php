<?php

define('TEN_PATH', 'tenframe');                                 // Константа директории tenframe
define('TEN_CLASSES', TEN_PATH . '/classes');                   // Константа директории для хранения классов tenframe

if(stripos($_SERVER['PHP_SELF'], TEN_PATH . '/index.php')) {    // Если выполняется обычный запрос
    list($root, $query) = explode(
        TEN_PATH . '/index.php',
        $_SERVER['PHP_SELF']
    );
}
else {                                                          // Иначе выполняется ajax-запрос
    $root = '';
    $query = $_SERVER['PHP_SELF'];
}

define('ROOT', $_SERVER['DOCUMENT_ROOT'] . $root);              // Константа корневого пути
define('BLOCKS', ROOT . '/view/blocks/');                       // Константа директории блоков

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