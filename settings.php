<?php

core::$settings = array(                    // Параметры работы фреймворка
    
    'develop'  => true,                     // Режим разработки
    'clearURI' => true,                     // Маршрутизировать относительный путь
    'compressHTML' => true,                 // Сжимать отдаваемый HTML
    
    'mysql'    => array(                    // Подключение к БД
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