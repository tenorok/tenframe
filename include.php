<?php

ten_file::include_files(array(							// Markdown для модулей
	'markdown.css',
	'highlight/github.css',
	'highlight.js'
), array(
	'path' => array(
		'css' => '/assets/css/',
		'js'  => '/assets/js/'
	),
	'output_file' => '/view/include/markdown.tpl',
	'hash' => false
));

ten_file::include_files(array(							// Файлы библиотек и плагинов
	'jquery-1.7.2.min.js',
	'jquery-ui-1.8.21.min.js',
	'modernizr-2.5.3.js',
	'jquery.placeholder_ten.js',
	'jquery.hoverDelay.js',
	'jquery.maskedinput-1.3.js',
	'handlebars-1.0.0.beta.6.js'
), array(
	'path' => array(
		'js' => '/assets/js/'
	),
	'output_file' => '/view/include/libs.tpl',
	'hash' => false
));

ten_file::include_files(array(							// Файлы для режима разработчика
	array(
		'href'      => 'main.less',
		'rel'       => 'stylesheet/less',
		'data-file' => 'main.css'
	),
	array(
		'href'      => 'print.less',
		'rel'       => 'stylesheet/less',
		'data-file' => 'print.css'
	),
	'less-1.3.0.min.js'
), array(
	'path' => array(
		'less' => '/assets/css/',
		'js'   => '/assets/js/'
	),
	'output_file' => '/view/include/developer.tpl',
	'hash' => false
));

ten_file::include_files(array(							// Основные файлы
	'main.css',
	'main.js'
), array(
	'path' => array(
		'css' => '/assets/css/',
		'js'  => '/assets/js/'
	),
	'output_file' => '/view/include/require.tpl'
));