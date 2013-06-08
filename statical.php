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
    'jquery-1.8.3.min.js',
    'jquery-ui-1.9.2.min.js',
    'modernizr-2.5.3.js',
    'jquery-bem.js',
    'jquery.placeholder_ten.js',
    'jquery.hoverDelay.js',
    'jquery.maskedinput-1.3.js',
    'handlebars-1.0.0.beta.6.js'
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