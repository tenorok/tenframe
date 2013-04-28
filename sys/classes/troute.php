<?php

/**
 * Маршрутизация запросов
 * @version 0.0.1
 */

/* Использование

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
*/

class troute extends core {

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
            self::$called ||                                               // Если маршрут был проведён
            $_SERVER['REQUEST_METHOD'] != $type                            // или метод вызова не соответствует
        )
            return false;                                                  // то маршрут обрабатывать не нужно

        if(gettype($url) == 'string') {                                    // Если у маршрута один адрес

                if(trim($url) == '*')
                    return self::callback($type, $callback);

                $pathArr[0] = self::parse_urn($url);                       // Путь текущего адреса
        }
        else                                                               // Иначе передан массив адресов
            foreach($url as $p => $path)                                   // Цикл по адресам маршрутов
                $pathArr[$p] = self::parse_urn($path);                     // Путь каждого адреса

        $urn  = self::parse_urn();                                         // Текущий URN

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

            self::$called = true;                                          // Изменение флага для определения, что по текущему маршруту уже проведён роут

            return self::callback($type, $callback, $args);
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

        foreach(self::$routes as $route) {                                 // Цикл по системным маршрутам

            foreach(self::$routes_default as $key => $val)                 // Установка значений по умолчанию
                if(!isset($route[$key]))                                   // для незаданных опций
                    $route[$key] = $val;

            if(!$route['dev'] || $route['dev'] && DEV)                     // Если маршрут надо проводить всегда или только для режима разработчика и режим включен
                self::request($route['type'], $route['url'], $route['callback'], $route['asserts']);
        }
    }

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
}