<?php

// Version 1.5.9
// From 09.02.2013

/*    core
    
    Маршрутизация (route.php):
        get|post(                                                      // Проведение GET- и POST-запросов осуществляется одинаково
            
            string|array,                                              // Первым параметром является адрес или массив адресов
            Например:
                Один адрес:
                    '/'
                    '/url/my/'
                    '/url/{id}/'
                Несколько адресов:
                    array(
                        '/',
                        '/url/my/',
                        '/url/{id}/'
                    )
                Любой адрес:                                           // Такой вызов будет проведён всегда
                    '*'                                                // при этом, он не останавливает проведение последующих маршрутов
                                                                       // поэтому его рекомендуется прописывать самым первым, чтобы другие маршруты не остановили проведение, когда очередь дойдёт до него
            
            'controller->method',                                      // Контроллер и его метод, который будет вызван при проведении маршрута
            
            array(                                                     // Правила для переменных
                Например:
                    'id'   => '/\d+/',
                    'name' => '/^myname$/'
            )
        );

        Получить переменную в вызываемом методе контроллера можно двумя способами:
            1) method(key1, key2, ..., key3)
            2) core::$get->key1;
               core::$get->key2;
               core::$get->key3;

    Получение значения GET-переменной:
        $value = core::$get->key;
        
    Включение автоподгрузки классов:
        spl_autoload_register(array('core', 'auto_load'));

    Подключение include-файлов:
        echo core::includes(
            'libs, developer, require',                                // Обязательный. Файлы с именами 'developer' и 'dev' подключаются только при включенном режиме разработчика
            '__autogen__'                                              // Префикс перед именами файлов (по умолчанию отсутствует)
        );

    Вывод страницы 404:
        core::not_found(array(
            'title'   => 'title',                                      // По умолчанию: "Страница не найдена"
            'header'  => 'header,                                      // По умолчанию: "Страница не найдена"
            'content' => 'content'                                     // По умолчанию: ""
        ));
*/

defined('SYS')        or die('Core error: System path is not declared!');
defined('CONTROLLER') or die('Core error: Controller path is not declared!');
defined('MODEL')      or die('Core error: Model path is not declared!');

// Класс ядра
class core {
    
    public static $settings;                                               // Параметры работы фреймворка
    
    public static $paths = array(SYS, CONTROLLER, MODEL);                  // Массив с директориями классов
    
    /**
     * Функция автоматической подгрузки необходимых файлов
     *
     * @param string $class Имя подключаемого класса (оно должно соответствовать имени файла, в котором находится класс)
     */
    public static function auto_load($class) {
        
        foreach(core::$paths as $dir) {
            
            $path = str_replace('__', '/', strtolower($class));            // Двойное подчёркивание заменяется на слеш
            
            $file = $dir . $path . '.php';
            
            if(is_file($file)) {
                require $file;
                break;
            }
        }
    }
    
    /**
     * Функция разбора адресной строки на части
     *
     * @param string $urn URN для обработки
     * @return array
     */
    public static function parse_urn($urn = null) {
        
        if(is_null($urn))
            $urn = URI;
            
        return preg_split('/\//', $urn, -1, PREG_SPLIT_NO_EMPTY);
    }
    
    public static $called = false;                                         // Флаг для определения была ли уже вызвана функция по текущему маршруту
    
    /**
     * Функция обработки маршрутов, отправленных методами GET и POST
     *
     * @param string $type     Тип запроса [GET || POST]
     * @param string $url      Путь, указанный в роуте
     * @param string $callback Класс->Метод для вызова
     * @param array  $asserts  Массив регулярных выражений для проверки {переменных}
     * @return boolean
     */
    public static function request($type, $url, $callback, $asserts = array()) {
        
        if(
            core::$called ||                                               // Если маршрут был проведён
            $_SERVER['REQUEST_METHOD'] != $type                            // или метод вызова не соответствует
        )
            return false;                                                  // то маршрут обрабатывать не нужно

        if(gettype($url) == 'string') {                                    // Если у маршрута один адрес
                
                if(trim($url) == '*')
                    return core::callback($type, $callback);

                $pathArr[0] = core::parse_urn($url);                       // Путь текущего адреса
        }
        else                                                               // Иначе передан массив адресов
            foreach($url as $p => $path)                                   // Цикл по адресам маршрутов
                $pathArr[$p] = core::parse_urn($path);                     // Путь каждого адреса

        $urn  = core::parse_urn();                                         // Текущий URN
        
        foreach($pathArr as $p => $path) {                                 // Цикл по маршрутам

            if(count($urn) != count($path))                                // Если количество частей URN и пути разное
                continue;                                                  // значит надо вызывать следующий маршрут в index.php
            
            $args = array();                                               // Объявление массива аргументов
            
            for($part = 0; $part < count($urn); $part++)
                if(preg_match('|^\{(.*)\}$|', $path[$part], $match))       // Если часть пути является {переменной}
                    if(!isset($asserts[$match[1]]) ||                      // Если для этой переменной не назначено регулярное выражение
                        preg_match($asserts[$match[1]], $urn[$part])) {    // или если переменная проходит проверку регулярным выражением
                        $args[$match[1]] = $urn[$part];                    // Запись переменной в массив аргументов для дальнейшей передачи функции
                        self::set_get_arg($match[1], $urn[$part]);         // Добавление пары ключ-значение в объект для работы с переменными
                    }
                    else {                                                 // Иначе переменная не проходит проверку регулярным выражением
                        self::unset_get_args();                            // Нужно очистить объект переменных
                        continue 2;                                        // и вызывать следующий маршрут в index.php
                    }
                else                                                       // иначе часть пути не является переменной
                    if($urn[$part] != $path[$part]) {                      // и если часть URN не совпадает с частью пути
                        self::unset_get_args();                            // Нужно очистить объект переменных
                        continue 2;                                        // и вызывать следующий маршрут в index.php
                    }
            
            core::$called = true;                                          // Изменение флага для определения, что по текущему маршруту уже проведён роут

            return core::callback($type, $callback, $args);
        }
    }

    /**
     * Функция обработки колбека
     * 
     * @param string $type     Тип запроса [GET || POST]
     * @param string $callback Класс->Метод для вызова
     * @param array  $args     Массив переданных аргументов
     */
    private static function callback($type, $callback, $args = array()) {

        $call = explode('->', $callback);                                  // Разбор callback на две части: 1) До стрелки и 2) После стрелки
        
        if(method_exists($call[0], $call[1]))                              // Если метод существует
            call_user_func_array(                                          // Вызов
                array($call[0], $call[1]),                                 // из класса $call[0] метода с именем $call[1]
                $args                                                      // и параметрами из массива $args
            );
        else
            tmsg::error(                                                   // Иначе метод не существует
                '[' . $type . '] Route error: Function is undefined: '
                . $call[0] . '->' . $call[1]
            );
    }

    private static $routes_default = array(                                // Умолчания для системных маршрутов
        'type'    => 'GET',
        'asserts' => array(),
        'dev'     => false                                                 // Проводить маршрут всегда
    );
    
    public static $routes = array();                                       // Системные маршруты

    /**
     * Функция проведения системных маршуртов
     * 
     */
    public static function routes() {

        foreach(core::$routes as $route) {                                 // Цикл по системным маршрутам
            
            foreach(core::$routes_default as $key => $val)                 // Установка значений по умолчанию
                if(!isset($route[$key]))                                   // для незаданных опций
                    $route[$key] = $val;
            
            if(!$route['dev'] || $route['dev'] && DEV)                     // Если маршрут надо проводить всегда или только для режима разработчика и режим включен
                core::request($route['type'], $route['url'], $route['callback'], $route['asserts']);
        }
    }

    public static $get;                                                    // Объект, который используется из приложения для обращения к GET-переменным

    /**
     * Функция добавления свойства для объекта self::$get
     *
     * @param string $key Имя GET-переменной
     * @param string $val Значение GET-переменной
     */
    public static function set_get_arg($key, $val) {

        self::$get->$key = $val;
    }

    /**
     * Функция удаления всех свойств объекта self::$get
     *
     */
    public static function unset_get_args() {

        if(count(self::$get))                                              // Если объект аргументов содержит хотя бы одно значение
            foreach(get_object_vars(self::$get) as $key => $val)
                self::$get->$key = '';
    }
    
    private static $default_404_options = array(                           // Дефолтные параметры для ненайденной страницы
        'title'   => 'Страница не найдена',
        'header'  => 'Страница не найдена',
        'content' => '',
        'sysauto' => false
    );
    
    /**
     * Функция возврата ошибки 404
     *
     * @param  array $options Массив опций [title, header, content]
     * @return boolean false
     */
    public static function not_found($options = array()) {
        
        if(
            core::$called              &&                                  // Если маршрут был проведён
            isset($options['sysauto']) &&                                  // и функция вызывается автоматически с главной страницы после всех роутов
            $options['sysauto']
        )
            return false;                                                  // то страница найдена и ошибка 404 не нужна

        header('HTTP/1.1 404 Not Found');
        
        foreach(core::$default_404_options as $key => $val)                // Установка значений по умолчанию
            if(!isset($options[$key]))                                     // для незаданных опций
                $options[$key] = $val;
        
        $template = new Blitz(ROOT . '/view/blocks/html/view/404.tpl');
        
        die($template->parse(array(
            'title'   => $options['title'],
            'header'  => $options['header'],
            'content' => $options['content']
        )));
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

    private static $include_dev = array('developer', 'dev');                                        // Массив имён файлов, которые подключаются только при включенном режиме разработчика

    /**
     * Функция подключения include-файлов
     * 
     * @param  string $files  Имена include-файлов
     * @param  string $prefix Префикс перед именами include-файлов
     * @return string
     */
    public static function includes($files, $prefix = '') {

        $includes = '';                                                                             // Переменная для конкатенации содержимого файлов

        foreach(explode(',', $files) as $file) {                                                    // Цикл по массиву переданных имён файлов

            $file = trim($file);                                                                    // Обрезание пробелов с обеих сторон имени текущего файла
            
            if(in_array($file, core::$include_dev) && !DEV)                                         // Если текущий файл требуется для режима разработчика и режим разработчика выключен
                continue;                                                                           // то его подключать не нужно и выполняется переход к следующему файлу
            
            $includes .= file_get_contents(ROOT . '/view/include/' . $prefix . $file . '.tpl');		// Конкатенация содержимого текущего файла
        }

        return $includes;                                                                           // Возвращение результата конкатенации содержимого файлов
    }

    /**
     * Функция выполняется после завершения работы всего скрипта
     * 
     */
    public static function shutdown() {

        core::routes();                                                    // Проведение системных маршрутов

        core::not_found(array(                                             // Если ни один маршрут не был проведён, значит страница не найдена
            'sysauto' => true                                              // Опция символизирует возврат автоматической страницы 404
        ));

        if(isset(torm::$mysqli))
            torm::$mysqli->close();                                         // Разрыв соединения с базой данных

        terr::get_error();                                                 // Обработка ошибок интерпретатора
    }
}