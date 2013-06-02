<?php

/**
 * Параметры работы фреймворка
 */

ten\core::settings(array(

    'develop' => true,
    'debug' => true,

    'mysql' => true,
    'tenhtml' => '/assets/' . GEN . 'tenhtml/',
    'compressHTML' => '/assets/' . GEN . 'compressed/',
    'modules' => array(
        'admin',
        'shop'
    )
));