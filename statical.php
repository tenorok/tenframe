<?php

ten\statical::involve(array(                            // Markdown для модулей
    'markdown.css',
    'highlight/github.css',
    'highlight.js'
), array(
    'path' => array(
        'css' => '/assets/css/',
        'js'  => '/assets/js/'
    ),
    'output_file' => 'markdown.tpl',
    'prefix' => GEN,
    'hash' => false
));

ten\statical::involve(array(                            // Файлы библиотек и плагинов
    'vendor.js',
    'jquery.placeholder_ten.js',
    'jquery.hoverDelay.js'
), array(
    'path' => array(
        'js' => '/assets/js/'
    ),
    'output_file' => 'libs.tpl',
    'prefix' => GEN,
    'hash' => false
));

ten\statical::involve(array(                            // Основные файлы
    'main.css',
    'main.js'
), array(
    'path' => array(
        'css' => '/assets/css/',
        'js'  => '/assets/js/'
    ),
    'output_file' => 'require.tpl',
    'prefix' => GEN
));