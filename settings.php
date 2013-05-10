<?php

ten\core::$settings = array(                // Параметры работы фреймворка

    'develop'  => true,                     // Режим разработки
    'clearURI' => true,                     // Маршрутизировать относительный путь
//    'autoprefix' => '__autogen__',          // Префикс для автоматически сгенерированных файлов

    // Для compressHTML и tenhtml в качестве значения можно указать путь до директории, в которой будут храниться сгенерированные шаблоны
    'compressHTML' => true,                 // Сжимать отдаваемый HTML (для tpl-шаблонов)
    'tenhtml'  => true,                     // Использовать tenhtml-шаблоны (автоматически сжимаются)

    'autoload' => array(                    // Пути для автоматической загрузки классов в порядке приоритета
        '/app/controller/',
        '/app/model/'
    ),

    'mysql' => array(                       // Подключение к БД
        'host'     => 'localhost',
        'user'     => 'root',
        'password' => '',
        'database' => 'tmod_shop'
    ),

    'modules' => array(                     // Подключаемые модули
        'admin',
        'shop'
    )

);