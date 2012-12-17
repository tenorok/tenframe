<?php

session_start();

define('ROOT', $_SERVER['DOCUMENT_ROOT']);						// Константа корневого пути

define('SELF', '/' . implode('/', array_slice(					// Чистый $_SERVER['PHP_SELF'] без index.php
	explode('/', $_SERVER['PHP_SELF']), 2
)));

define('BLOCKS',     ROOT . '/view/blocks/');					// Константа директории блоков

// Определение констант для автоподключения классов
define('SYS',        ROOT . '/sys/classes/');					// Определение директории с классами системы
define('CONTROLLER', ROOT . '/app/controller/');				// Определение директории с классами контроллеров
define('MODEL',      ROOT . '/app/model/');						// Определение директории с классами модели

define('DEV', true);											// Вкл/выкл режима разработчика

require 'core.php';												// Подключение ядра
spl_autoload_register(array('core', 'auto_load'));				// Включение автоподгрузки классов

if(!DEV)														// Если выключен режим разработчика
	error_reporting(0);											// Отключение отображения ошибок интерпретатора
else
	error_reporting(E_ALL);										// Включение отображения всех ошибок интерпретатора

register_shutdown_function(array('core', 'shutdown'));			// Указание метода, который будет вызван по окончании выполнения всего скрипта

if(
	$_SERVER['PHP_SELF'] != '/index.php'         &&				// Если текущий адрес не index.php
	preg_match('/\.php$/', $_SERVER['PHP_SELF']) && 			// а какой-то другой php-файл
	file_exists(ROOT .     $_SERVER['PHP_SELF'])				// и он существует
)
	core::$called = true;										// Маршрут считается проведённым

orm::connect('localhost', 'root', '');          				// Подключение к mysql
orm::db('tmod_shop');											// Выбор базы данных

mod::init(array('admin', 'shop'));								// Инициализация модулей