<?php

/**
 * Базовый класс tenframe
 * @version 0.0.2
 */

/* Использование

    Приведение существующих путей.
        Если файла не существует, то вернётся false.

        Если файл существует: __DIR__ . '/testResolveRealPath/one/cat/cat.txt'.
        ten\core::resolveRealPath(__DIR__, 'testResolveRealPath', 'one/cat/', '..', 'cat', 'cat.txt');

        Абсолютный путь добавится автоматически: __DIR__ . '/tenframe/test/core'.
        ten\core::resolveRealPath('tenframe', '/test', 'core/');

    Приведение несуществующих путей в относительном виде.
        ten\core::resolveRelativePath('path', 'to', 'dir', 'or', 'file');

    Приведение несуществующих путей в абсолютном виде.
        Вне зависимости от существования файла: __DIR__ . '/virtualPath/one/cat/cat.txt'.
        Абсолютный путь добавится по необходимости.
        ten\core::resolvePath(__DIR__, 'virtualPath', 'one/cat/', '..', 'cat', 'cat.txt');

    Подключение файла.
        Возвращает файл или false в случае его отсутствия.
        ten\core::requireFile('/path/to/file.php');

    Подключение PHP-файлов из директории.
        Возвращает массив путей подключенных файлов.
        ten\core::requireDir('/path/to/dir/');

    Рекурсивное подключение PHP-файлов из всех директорий внутри директории.
        Возвращает массив путей подключенных файлов.
        ten\core::requireDirRecursive(
            '/path/to/dir/',
            0                                           // Количество уровней вложенности, начиная с нуля. По умолчанию: -1
        );

    Получить массив информации о текущем URL:
        ten\core::getUrlInfo();                         // Возвращается: http://php.net/manual/ru/function.parse-url.php

    Получить текущий URL:
        ten\core::getUrl();                             // Например: http://tenframe/path/to/

    Получить строку запроса к текущему файлу:
        ten\core::getCurrentPageUrl();                  // Например: http://tenframe/tenframe/index.php
*/

namespace ten;

class core {

    protected static $paths = array('/');                                  // Массив с директориями классов
    public static $startTime;                                              // Время начала выполнения скрипта
    public static $required = array();                                     // Подключенные файлы классов
    
    /**
     * Функция автоматической подгрузки необходимых файлов
     *
     * @param string $class Имя подключаемого класса (оно должно соответствовать имени файла, в котором находится класс)
     */
    public static function auto_load($class) {

        foreach(self::$paths as $dir) {

            $path = str_replace(                                           // Замена символов в строке вызова метода tenframe
                array('__', 'ten\\mod\\', 'ctr\\',           'mod\\',      'ten\\',     '\\'),
                array('/',  TEN_MODULES,  'app/controller/', 'app/model/', TEN_CLASSES, '/'),
                strtolower($class)
            );

            if(self::requireFile($dir . $path . '.php')) break;            // Подключение файла
        }
    }

    /**
     * Подключение файлов
     *
     * @param  string  $file Путь до файла
     * @return mixed         Файл или false в случае его отсутствия
     */
    public static function requireFile($file) {

        $file = self::resolvePath($file);                                  // Приведение пути к корректному виду

        if(is_file($file)) {                                               // Если файл существует
            array_push(self::$required, $file);                            // Добавление в массив подключенных файлов классов
            return require $file;                                          // его нужно подключить
        } else return false;
    }

    /**
     * Подключение всех php-файлов директории
     *
     * @param  string $dir Путь до директории
     * @return array       Массив путей
     */
    public static function requireDir($dir) {
        $dirList = new \DirectoryIterator(self::resolvePath($dir));
        return self::requireDirFiles($dirList);
    }

    /**
     * Рекурсивное подключение php-файлов всех вложенных директорий
     *
     * @param  string  $dir   Путь до базовой директории
     * @param  integer $depth Глубина рекурсии, начиная с нуля
     * @return array          Массив путей
     */
    public static function requireDirRecursive($dir, $depth = -1) {
        $dirList  = new \RecursiveDirectoryIterator(self::resolvePath($dir));
        $iterator = new \RecursiveIteratorIterator($dirList);
        $iterator->setMaxDepth($depth);
        return self::requireDirFiles($iterator);
    }

    /**
     * Подключение php-файлов в директории
     *
     * @param  object $list Массив объектов директории
     * @return array        Массив путей подключенных файлов
     */
    private static function requireDirFiles($list) {

        $requiredFiles = array();

        foreach($list as $object) {
            if($object->isFile() && $object->getExtension() == 'php') {
                $path = $object->getPathname();
                self::requireFile($path);
                array_push($requiredFiles, $path);
            }
        }

        return $requiredFiles;
    }

    public static $define = array();                                       // Константы

    /**
     * Объявление константы
     *
     * @param $name  Имя константы
     * @param $value Значение константы
     */
    private static function define($name, $value) {
        define($name, $value);
        self::$define[$name] = $value;
    }

    protected static $settings = array(                                    // Параметры работы фреймворка
        'develop' => false,                                                // Режим разработки
        'autoprefix' => '__autogen__',                                     // Префикс для автоматически сгенерированных файлов

        // Для compressHTML и tenhtml в качестве значения нужно указать путь до директории, в которой будут храниться сгенерированные шаблоны
        'compressHTML' => false,                                           // Сжимать отдаваемый HTML (для tpl-шаблонов)
        'tenhtml' => false,                                                // Использовать tenhtml-шаблоны (автоматически сжимаются)

        'devFiles' => array(),                                             // Файлы, подключаемые только в режиме разработчика
        'files' => array(),                                                // Файлы, подключаемые всегда

        'autoload' => array(                                               // Пути для автоматической загрузки классов в порядке приоритета
            '/app/controller/',
            '/app/model/'
        ),

        'statical' => '/view/statical/',                                   // Путь до путей к статическим файлам

        'mysql' => false,
//        'mysql' => array(                                                  // Подключение к БД (true | array())
//            'host'     => 'localhost',
//            'user'     => 'root',
//            'password' => '',
//            'database' => ''
//        ),

        'modules' => array(),                                              // Подключаемые модули
        'debug' => false,                                                  // Включить вывод всей отладочной информации
//        'debug' => array(                                                  // Выводить только заданную отладочную информацию
//            'autogen',                                                     // Все автоматически сгенерированные файлы
//            'statical',                                                    // Сгенерированные подключения статических файлов
//            'get',                                                         // Выкачанные файлы
//            'join',                                                        // Объединённые файлы
//            'tenhtml',                                                     // Шаблоны, сгенерированные из tenhtml
//            'tpl',                                                         // Шаблоны, использованные для формирования страницы
//            'orm',                                                         // Выполненные SQL-запросы
//            'define',                                                      // Константы
//            'required',                                                    // Файлы подключенных классов
//            'time'                                                         // Время выполнения всего скрипта
//        )
    );

    private static $default = array(                                       // Стандартные параметры работы фреймворка
        'mysql' => array(
            'host' => 'localhost',
            'user' => 'root'
        )
    );

    /**
     * Слияние стандартных настроек и заданных пользователем
     *
     * @param array $settings Настройки пользователя
     */
    public static function settings($settings) {
        self::$settings = array_merge(self::$settings, $settings);
    }

    /**
     * Инициализация, применение настроек tenframe
     *
     */
    public static function init() {

        self::$startTime = microtime(true);                                // Сохранение времени начала выполнения скрипта

        session_start();
        spl_autoload_register(array('self', 'auto_load'));                 // Включение автоподгрузки классов
        register_shutdown_function(array('ten\core', 'shutdown'));         // Указание метода, который будет вызван по окончании выполнения всего скрипта

        self::initStart();                                                 // Базовая инициализация

        self::define('GEN', file::$autoprefix);                            // Константа префикса автоматически сгенерированных файлов
        self::requireFile('/settings.php');                                // Подключение настроек работы tenframe

        self::setUrlInfo(self::getUrl());
        self::define_DEV();                                                // Определение константы DEV

        self::$paths = array_merge(                                        // Добавление путей автоматической загрузки классов
            self::$paths,
            self::$settings['autoload']
        );

        if(self::$settings['mysql']) {                                     // Подключение к mysql
            $mysql = array_merge(
                self::$default['mysql'],
                (is_array(self::$settings['mysql'])) ? self::$settings['mysql'] : array()
            );
            orm::connect(                                                  // Подключение
                $mysql['host'],
                $mysql['user'],
                (isset($mysql['password'])) ? $mysql['password'] : false
            );
            if(isset($mysql['database'])) {
                orm::db($mysql['database']);                               // Выбор базы данных
            }
        }

        html::$tenhtmlFolder = self::$settings['tenhtml'];                 // Использование tenhtml
        tpl::$compressTplFolder = self::$settings['compressHTML'];         // Компрессия отдаваемого HTML
        file::$autoprefix = self::$settings['autoprefix'];                 // Префикс для автоматически сгенерированных файлов
        statical::$path = self::$settings['statical'];                     // Путь для хранения путей к статическим файлам

        module::init();                                                    // Инициализация модулей
        self::require_options();                                           // Подключение файлов опций
    }

    /**
     * Инициализация для тестов
     *
     */
    public static function initTest() {
        self::initStart();
        self::requireDir(TEN_PATH . '/classes/');
        self::requireDirRecursive(TEN_PATH . '/test/', 1);
    }

    /**
     * Базовая инициализация
     *
     * @return string Строка запроса
     */
    private static function initStart() {

        self::define('TEN_PATH', 'tenframe');                              // Константа директории tenframe
        self::define('TEN_CLASSES', TEN_PATH . '/classes/');               // Константа директории для хранения классов tenframe
        self::define('TEN_MODULES', '/mod/');                              // Константа директории модулей

        $query = self::define_ROOT();                                      // Определение константы ROOT
        self::define('BLOCKS', self::resolvePath('/view/blocks/'));       // Константа директории блоков

        self::requireFile('/vendor/autoload.php');                         // Composer autoloader

        return $query;
    }

    /**
     * Определение константы ROOT
     *
     */
    private static function define_ROOT() {
        self::define('ROOT', implode('/', array_slice(explode('/', __DIR__), 0, -1)));
    }

    private static $url;                                                   // Массив информации об URL

    /**
     * Установить массив информации о текущем URL
     *
     * @param  string $url Строка запроса
     * @return array       Массив информации
     */
    protected static function setUrlInfo($url) {
        return self::$url = parse_url($url);
    }

    /**
     * Получить массив информации о текущем URL
     *
     * @return array Массив информации
     */
    public static function getUrlInfo() {
        return self::$url;
    }

    /**
     * Получить текущий URL
     *
     * @return string URL
     */
    public static function getUrl() {
        return self::getProtocol() . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * Получить строку запроса к текущему файлу
     *
     * @return string Строка запроса
     */
    public static function getCurrentPageUrl() {
        return
            self::getProtocol() . '://' .
            $_SERVER['HTTP_HOST'] .
            $_SERVER['SCRIPT_NAME'] .
            (!empty($_SERVER['QUERY_STRING'])? '?' . $_SERVER['QUERY_STRING'] : '');
    }

    /**
     * Получить текущий протокол
     *
     * @return string Протокол
     */
    private static function getProtocol() {
        return strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === false? 'http' : 'https';
    }

    /**
     * Определение константы DEV (флаг режима разработчика)
     *
     */
    private static function define_DEV() {

        self::define('DEV', self::$settings['develop']);                   // Вкл/выкл режима разработчика

        if(!DEV)                                                           // Если выключен режим разработчика
            error_reporting(0);                                            // Отключение отображения ошибок интерпретатора
        else
            error_reporting(E_ALL);                                        // Включение отображения всех ошибок интерпретатора
    }

    /**
     * Подключение файлов
     *
     */
    private static function require_options() {

        if(self::dev(DEV)) {                                               // Если включен режим разработчика
            foreach(self::$settings['devFiles'] as $file) {                // Подключение файлов для разработки
                self::requireFile($file . '.php');
            }
        }

        foreach(self::$settings['files'] as $file) {                       // Подключение общих файлов
            self::requireFile($file . '.php');
        }
    }

    /**
     * Приведение существующих путей
     *
     * @param  string           Arguments Любое количество строк к объединению
     * @return string | boolean           Приведённый путь или false, если путь не существует
     */
    public static function resolveRealPath() {
        $args = implode('/', func_get_args());                             // Объединение всех аргументов в строку

        $path = '/' . implode('/', array_filter(explode('/', $args)));     // Удаление лишних слешей

        if(!self::hasRoot($path)) {
            $path = ROOT . $path;
        }

        return realpath($path);                                            // Приведённый путь
    }

    /**
     * Приведение несуществующих путей в относительном виде
     *
     * @param  string Arguments Любое количество строк к объединению
     * @return string           Приведённый путь
     */
    public static function resolveRelativePath() {
        $args = implode('/', func_get_args());                             // Объединение всех аргументов в строку

        return array_reduce(explode('/', $args), function($a, $b) {        // Реализация realpath()
            if($a === 0) $a = '/';
            if($b === '' || $b === '.') return $a;
            if($b === '..') return dirname($a);
            return preg_replace('/\/+/', '/', $a . '/' . $b);
        });
    }

    /**
     * Приведение несуществующих путей в абсолютном виде
     *
     * @param  string Arguments Любое количество строк к объединению
     * @return string           Приведённый путь
     */
    public static function resolvePath() {
        $path = call_user_func_array(array('self', 'resolveRelativePath'), func_get_args());
        return !self::hasRoot($path)? ROOT . $path : $path;
    }

    /**
     * Является ли путь абсолютным
     *
     * @param  string $path Путь
     * @return boolean
     */
    private static function hasRoot($path) {
        return !!preg_match('/^' . str_replace('/', '\/', ROOT) . '/', $path);
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
            file::autogen('/view/include/dev.js', 'tenframe.dev=' . (($dev) ? 'true;' : 'false;'));
            $ret = true;                                                   // то надо вернуть true, чтобы собрать JS-файлы с новым значением
        }
        else                                                               // Иначе режим разработчика выключен
            $ret = false;

        $_SESSION['DEV'] = $dev;                                           // Присваивание текущего значения флага режима разработчика

        return $ret;
    }

    /**
     * Функция выполняется после завершения работы всего скрипта
     * 
     */
    public static function shutdown() {

        tpl::not_found(array(                                              // Если ни один маршрут не был проведён, значит страница не найдена
            'sysauto' => true                                              // Опция символизирует возврат автоматической страницы 404
        ));

        if(isset(orm::$mysqli))
            orm::$mysqli->close();                                         // Разрыв соединения с базой данных

        debug::init(self::$settings['debug']);                             // Напечатать отладочную информацию в соответствии с переданными опциями
    }
}