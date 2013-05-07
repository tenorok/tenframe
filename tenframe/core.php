<?php

/**
 * Базовый класс фреймворка
 * @version 1.5.9
 */

/* Использование
        
    Включение автоподгрузки классов:
        spl_autoload_register(array('core', 'auto_load'));

    Подключение include-файлов:
        echo ten\core::includes(
            'libs, developer, require',                                // Обязательный. Файлы с именами 'developer' и 'dev' подключаются только при включенном режиме разработчика
            '__autogen__'                                              // Префикс перед именами файлов (по умолчанию отсутствует)
        );
*/

namespace ten;

defined('SYS')        or die('Core error: System path is not declared!');
defined('CONTROLLER') or die('Core error: Controller path is not declared!');
defined('MODEL')      or die('Core error: Model path is not declared!');

// Класс ядра
class core {
    
    public static $settings;                                               // Параметры работы фреймворка
    public static $get;                                                    // Объект, который используется из приложения для обращения к GET-переменным
    public static $paths = array(SYS, CONTROLLER, MODEL);                  // Массив с директориями классов
    
    /**
     * Функция автоматической подгрузки необходимых файлов
     *
     * @param string $class Имя подключаемого класса (оно должно соответствовать имени файла, в котором находится класс)
     */
    public static function auto_load($class) {
        
        foreach(self::$paths as $dir) {
            
            $path = str_replace(                                           // Замена символов в строке вызова метода tenframe
                array('__', '\\', 'ten'),
                array('/', '/', 'tenframe/classes'),
                strtolower($class));
            
            $file = $dir . $path . '.php';

            if(is_file($file)) {
                require $file;
                break;
            }
        }
    }

    /**
     * Функция сохранения флага режима разработчика в JS
     * 
     * @param boolean $dev Флаг режима разработчика
     */
    public static function dev($dev = false) {

        if(
            isset($_SESSION['DEV']) && $_SESSION['DEV'] && !$dev ||        // Если режим разработчика был включен, а сейчас его выключили
            $dev                                                           // или он просто включен
        ) {

            ten_file::autogen('/view/include/dev.js', 'core.dev=' . (($dev) ? 'true;' : 'false;'));
            $ret = true;                                                   // то надо вернуть true, чтобы собрать JS-файлы с новым значением
        }
        else                                                               // Иначе режим разработчика выключен
            $ret = false;

        $_SESSION['DEV'] = $dev;                                           // Присваивание текущего значения флага режима разработчика

        return $ret;
    }

    private static $include_dev = array('developer', 'dev');               // Массив имён файлов, которые подключаются только при включенном режиме разработчика

    /**
     * Функция подключения include-файлов
     * 
     * @param  string $files  Имена include-файлов
     * @param  string $prefix Префикс перед именами include-файлов
     * @return string
     */
    public static function includes($files, $prefix = '') {

        $includes = '';                                                    // Переменная для конкатенации содержимого файлов

        foreach(explode(',', $files) as $file) {                           // Цикл по массиву переданных имён файлов

            $file = trim($file);                                           // Обрезание пробелов с обеих сторон имени текущего файла
            
            if(in_array($file, self::$include_dev) && !DEV)                // Если текущий файл требуется для режима разработчика и режим разработчика выключен
                continue;                                                  // то его подключать не нужно и выполняется переход к следующему файлу
            
            $includes .= file_get_contents(ROOT . '/view/include/' . $prefix . $file . '.tpl');		// Конкатенация содержимого текущего файла
        }

        return $includes;                                                  // Возвращение результата конкатенации содержимого файлов
    }

    /**
     * Функция выполняется после завершения работы всего скрипта
     * 
     */
    public static function shutdown() {

        route::routes();                                                   // Проведение системных маршрутов

        tpl::not_found(array(                                              // Если ни один маршрут не был проведён, значит страница не найдена
            'sysauto' => true                                              // Опция символизирует возврат автоматической страницы 404
        ));

        if(isset(ten\orm::$mysqli))
            ten\orm::$mysqli->close();                                     // Разрыв соединения с базой данных
    }
}