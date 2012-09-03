<?php

require 'sys/require.php';									// Общие подключения

if(core::dev(DEV)) {										// Если включен режим разработчика

	require 'merge.php';									// Сборка файлов
	require 'include.php';									// Подключение файлов
}

require 'sys/request.php';									// Подключение функций обработки маршрутов
require 'routes.php';										// Подключение файла маршрутизации

core::not_found(array(										// Если ни один маршрут из route.php не был проведён, значит страница не найдена
	'sysauto' => true										// Опция символизирует возврат автоматической страницы 404
));

// orm::$mysqli->close();										// Разрыв соединения с базой данных