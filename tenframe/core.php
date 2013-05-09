<?php

/**
 * Базовый класс tenframe
 * @version 0.0.2
 */

/* Использование

    Приведение путей к корректному виду:
        Корневой путь добавится автоматически:
            ten\core::resolvePath(                      // Путь до папки: /Users/name/project/one/two/third/four/
                'one//two///',
                'third',
                'four/'
            );
        Корневой путь не добавится, если он уже есть:
            ten\core::resolvePath(                      // Путь до файла: /Users/name/project/one/two/third/four
                ROOT,
                'one//two///',
                'third',
                'four'
            );

    Подключение include-файлов:
        echo ten\core::includes(
            'libs, developer, require',                 // Обязательный. Файлы с именами 'developer' и 'dev' подключаются только при включенном режиме разработчика
            '__autogen__'                               // Префикс перед именами файлов (по умолчанию отсутствует)
        );
*/

namespace ten;

class core {
    
    public static $settings;                                               // Параметры работы фреймворка
    public static $get;                                                    // Объект, который используется из приложения для обращения к GET-переменным
    public static $paths = array('/');                                     // Массив с директориями классов
    
    /**
     * Функция автоматической подгрузки необходимых файлов
     *
     * @param string $class Имя подключаемого класса (оно должно соответствовать имени файла, в котором находится класс)
     */
    public static function auto_load($class) {

        foreach(self::$paths as $dir) {
            
            $path = str_replace(                                           // Замена символов в строке вызова метода tenframe
                array('__', 'ten\\', '\\'),
                array('/', TEN_CLASSES . '/', '/'),
                strtolower($class)
            );

            $file = self::resolve_path($dir, $path . '.php');               // Приведение пути к корректному виду

            if(is_file($file)) {                                           // Если файл существует
                require $file;                                             // его нужно подключить
                break;
            }
        }
    }

    /**
     * Приведение путей к корректному виду с дополнением до абсолютного расположения
     *
     * @param  string Arguments Любое количество строк к объединению
     * @return string           Приведённый путь
     */
    public static function resolve_path() {
        $arguments = implode('/', func_get_args());                        // Объединение всех аргументов в строку
        $path = self::remove_path_slashes($arguments);                     // Удаление лишних слешей

        if(!preg_match('/^' . str_replace('/', '\/', ROOT) . '/', $path))  // Если в пути не указана корневая директория
            $path = self::remove_path_slashes(ROOT . $path);               // то её надо добавить

        return $path . (                                                   // Приведённый путь
            (substr($arguments, 0, -1) == '/') ?                           // Если последним символом был слеш
                '/' :                                                      // то его надо оставить
                ''
        );
    }

    /**
     * Удаление лишних слешей из пути
     *
     * @param  string $path Путь с лишними слешами
     * @return string       Путь без лишних слешей
     */
    private static function remove_path_slashes($path) {
        $path = explode('/', $path);                                       // Разбить путь на части в массив
        $path = array_filter($path);                                       // Удалить пустые элементы массива
        return '/' . implode('/', $path);                                  // Снова объединить элементы в строку
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

            file::autogen('/view/include/dev.js', 'core.dev=' . (($dev) ? 'true;' : 'false;'));
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

        if(isset(orm::$mysqli))
            orm::$mysqli->close();                                         // Разрыв соединения с базой данных
    }
}