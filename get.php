<?php

ten\join::files(array(                              // Сборка i-bem
    'files' => array(
        'https://raw.github.com/bem/bem-bl/master/blocks-common/i-jquery/__inherit/i-jquery__inherit.js',
        'https://raw.github.com/bem/bem-bl/master/blocks-common/i-jquery/__identify/i-jquery__identify.js',
        'https://raw.github.com/bem/bem-bl/master/blocks-common/i-jquery/__is-empty-object/i-jquery__is-empty-object.js',
        'https://raw.github.com/bem/bem-bl/master/blocks-common/i-jquery/__debounce/i-jquery__debounce.js',
        'https://raw.github.com/bem/bem-bl/master/blocks-common/i-jquery/__observable/i-jquery__observable.js',
        'https://raw.github.com/bem/bem-bl/master/blocks-common/i-bem/i-bem.js',
        'https://raw.github.com/bem/bem-bl/master/blocks-common/i-bem/__internal/i-bem__internal.js',
        'https://raw.github.com/bem/bem-bl/master/blocks-common/i-bem/__dom/i-bem__dom.js',
        'https://raw.github.com/bem/bem-bl/0.3/blocks-common/i-bem/__dom/_init/i-bem__dom_init_auto.js'
    ),
    'output_file' => '/assets/js/vendor/i-bem.js',
    'before'      => "\n\n/**\n * Load from:\n * {filename}\n */\n\n",
    'compress' => false
));

ten\get::files(array(                               // Выкачивание необходимых библиотек
    'files' => array(
        'http://code.jquery.com/jquery-latest.js',
        'http://modernizr.com/downloads/modernizr-latest.js',
        'https://raw.github.com/digitalBush/jquery.maskedinput/1.3.1/dist/jquery.maskedinput.min.js',
        'https://raw.github.com/necolas/normalize.css/master/normalize.css'
    ),
    'path' => array(
        'css' => '/assets/css/vendor/',
        'js'  => '/assets/js/vendor/'
    )
));

ten\join::files(array(                              // Сборка внешних CSS
    'files'       => 'ext: css',
    'input_path'  => '/assets/css/vendor/',
    'output_file' => '/assets/css/vendor.css'
));

ten\join::files(array(                              // Сборка внешних JS-библиотек
    'files'       => 'ext: js',
    'priority'    => array(
        '/assets/js/vendor/jquery-latest.js',
        '/assets/js/vendor/modernizr-latest.js',
        '/assets/js/vendor/i-bem.js'
    ),
    'input_path'  => '/assets/js/vendor/',
    'output_file' => '/assets/js/vendor.js'
));