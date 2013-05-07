<?php

ten\join::files(array(                            // Сборка всех js-файлов
    'priority'    => array('/view/core.js', '/view/routes.js'),
    'files'       => 'ext: js',
    'output_file' => '/assets/js/main.js'
));

ten\join::files(array(                            // Сборка основных стилей и необходимых библиотек
    'files'       => 'reg: /\.style|\.import/',
    'output_file' => '/assets/css/main.less'
));

ten\join::files(array(                            // Сборка стилей для печати и необходимых библиотек
    'files'       => 'reg: /\.print|\.import/',
    'output_file' => '/assets/css/print.less'
));

ten\join::files(array(                            // Сборка ie-стилей
    'files'       => 'ext: ie567, ie',
    'output_file' => '/assets/css/style.{ext}.css'
));