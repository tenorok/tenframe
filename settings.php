<?php

/**
 * Параметры работы фреймворка
 */

ten\core::settings(array(

    'develop' => true,
    'debug' => true,

    'devFiles' => array(
        'get',              // Выкачивание файлов
        'join',             // Сборка файлов
        'css',              // CSS препроцессоры
        'statical'          // Подключение файлов
    ),
    'files' => array(
        'routes'            // Подключение файла маршрутизации
    ),

    'mysql' => true,
    'tenhtml' => '/assets/' . GEN . 'tenhtml/',
    'compressHTML' => '/assets/' . GEN . 'compressed/',
    'modules' => array(
        'admin',
        'shop'
    )
));