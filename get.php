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
        'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.js',
        'https://raw.github.com/digitalBush/jquery.maskedinput/1.3.1/dist/jquery.maskedinput.min.js',
        'https://raw.github.com/necolas/normalize.css/master/normalize.css',
        'https://raw.github.com/isagalaev/highlight.js/master/src/styles/github.css' => 'highlight.github.css',
        'https://raw.github.com/jasonm23/markdown-css-themes/gh-pages/markdown10.css' => 'markdown.css'
    ),
    'path' => array(
        'css' => '/assets/css/vendor/',
        'js'  => '/assets/js/vendor/'
    )
));

ten\join::files(array(
    'files' => array('http://yandex.st/highlightjs/7.3/highlight.min.js'),
    'output_file' => '/assets/js/vendor/highlight.js',
    'after' => 'hljs.initHighlightingOnLoad();',
    'compress' => false
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