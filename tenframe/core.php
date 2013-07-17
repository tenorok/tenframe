<?php

/**
 * Базовый класс tenframe
 * @version 0.0.2
 */

/* Использование

    Приведение путей к корректному виду:
        Корневой путь добавится автоматически:
            ten\core::resolve_path(                     // Путь до папки: /Users/name/project/one/two/third/four/
                'one//two///',
                'third',
                'four/'
            );
        Корневой путь не добавится, если он уже есть:
            ten\core::resolve_path(                     // Путь до файла: /Users/name/project/one/two/third/four
                ROOT,
                'one//two///',
                'third',
                'four'
            );

    Подключение файла.
    Возвращает файл или false в случае его отсутствия.
        core::requireFile('/path/to/file.php');

    Подключение PHP-файлов из директории.
    Возвращает массив путей подключенных файлов.
        core::requireDir('/path/to/dir/');

    Рекурсивное подключение PHP-файлов из всех директорий внутри директории.
    Возвращает массив путей подключенных файлов.
        core::requireDirRecursive('/path/to/dir/');
*/

namespace ten;

class core {

    public static $paths = array('/');                                     // Массив с директориями классов
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

        $file = self::resolve_path($file);                                 // Приведение пути к корректному виду

        if(is_file($file)) {                                               // Если файл существует
            array_push(self::$required, $file);                            // Добавление в массив подключенных файлов классов
            return require $file;                                          // его нужно подключить
        } else return false;
    }

    /**
     * Декоратор для методов подключения php-файлов в директориях
     *
     * @param  string   $dir      Путь до директории
     * @param  closure  $callback Инструкции к применению над каждым объектом в директории
     * @return array              Массив путей
     */
    private static function requireDirDecorate($dir, $callback) {

        $dirInfo = new \DirectoryIterator(self::resolve_path($dir));       // Получение информации об объектах директории
        static $requiredFiles = array();                                   // Массив путей подключенных файлов

        foreach($dirInfo as $object) {                                     // Цикл по объектам директории
            $file = call_user_func($callback, $object);                    // Выполнение необходимых действий с объектом
            $file && array_push($requiredFiles, $file);                    // Если был подключен файл, то его надо добавить в массив
        }

        return $requiredFiles;
    }

    /**
     * Подключение всех php-файлов директории
     *
     * @param  string $dir Путь до директории
     * @return array       Массив путей
     */
    public static function requireDir($dir) {
        return self::requireDirDecorate($dir, function($object) {
            return core::requireDirFile($object);
        });
    }

    /**
     * Рекурсивное подключение php-файлов всех вложенных директорий
     *
     * @param  string $dir Путь до базовой директории
     * @return array       Массив путей
     */
    public static function requireDirRecursive($dir) {
        return self::requireDirDecorate($dir, function($object) {
            if($object->isDir() && !$object->isLink() && !$object->isDot()) {
                core::requireDirRecursive($object->getPathname());
            } else {
                return core::requireDirFile($object);
            }
        });
    }

    /**
     * Подключение файла в директории
     * @private
     *
     * @param  object           $object Объект с информацией о файле
     * @return string | boolean         Путь до подключенного файла или false в случае отказа подключения
     */
    public static function requireDirFile($object) {
        if($object->isFile() && $object->getExtension() == 'php') {
            $path = $object->getPathname();
            self::requireFile($path);
            return $path;
        } else return false;
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

        self::define_URI();                                                // Определение константы URI
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
        self::define('BLOCKS', self::resolve_path('/view/blocks/'));       // Константа директории блоков

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

    /**
     * Определение константы URI
     *
     */
    private static function define_URI() {
        // TODO: Проверка на AJAX-запрос
        self::define('URI', $_SERVER['REQUEST_URI']);
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
            (substr($arguments, -1) == '/') ?                              // Если последним символом был слеш
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

        route::routes();                                                   // Проведение системных маршрутов

        tpl::not_found(array(                                              // Если ни один маршрут не был проведён, значит страница не найдена
            'sysauto' => true                                              // Опция символизирует возврат автоматической страницы 404
        ));

        if(isset(orm::$mysqli))
            orm::$mysqli->close();                                         // Разрыв соединения с базой данных

        debug::init(self::$settings['debug']);                             // Напечатать отладочную информацию в соответствии с переданными опциями
    }
}