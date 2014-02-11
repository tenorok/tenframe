<?php

$join = new ten\join([
    'directory' => 'blocks/',
    'before' => "\n/* {filename} begin */\n",
    'after' => "\n/* {filename} end */\n"
]);

// Сборка js-файлов блоков
$join->extension('js', [
    'priority' => ['blocks/tenframe/tenframe.js'],
    'save' => 'assets/js/main.js'
]);

// Сборка less-файлов блоков
$join->extension('less', [
    'save' => 'assets/css/main.less'
]);