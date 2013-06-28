<?php

ten\join::files(array(                            // Сборка всех js-файлов
    'files'       => 'ext: js',
    'priority'    => array('/view/objects/core.js'),
    'output_file' => '/assets/js/main.js',
    'after'       => "\n"
));

ten\join::files(array(                            // Сборка основных стилей и необходимых библиотек
    'files'       => 'reg: /\.style|\.import/',
    'output_file' => '/assets/css/main.less',
    'after'       => "\n"
));

ten\join::files(array(                            // Сборка стилей для печати и необходимых библиотек
    'files'       => 'reg: /\.print|\.import/',
    'output_file' => '/assets/css/print.less',
    'after'       => "\n"
));

ten\join::files(array(                            // Сборка ie-стилей
    'files'       => 'ext: ie567, ie',
    'output_file' => '/assets/css/style.{ext}.css',
    'after'       => "\n"
));