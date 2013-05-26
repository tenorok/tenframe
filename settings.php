<?php

/**
 * Параметры работы фреймворка
 */

ten\core::settings(array(

    'develop' => true,

    'mysql' => array(
        'host'     => 'localhost',
        'user'     => 'root',
        'password' => '',
        'database' => 'tmod_shop'
    ),

    'modules' => array(
        'admin',
        'shop'
    ),
    'debug' => true,
    'tenhtml' => '/assets/' . GEN . 'tenhtml/'
));