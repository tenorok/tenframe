<?php

/**
 * Маршрутизация запросов
 * @version 0.1.1
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
            2) ten\core::$get->key1;
               ten\core::$get->key2;
               ten\core::$get->key3;

    Получение значения GET-переменной:
        $value = ten\core::$get->key;
*/

namespace ten;

class route extends core {

    /**
     * GET-маршрут
     *
     * @param  array $route Данные о маршруте
     * @return null         Маршрут не прошёл
     * @return mixed        Результат выполнения колбека
     */
    public static function get($route) {
        if($_SERVER['REQUEST_METHOD'] != 'GET') return;
        return self::request(array_merge(self::$default, $route), $_GET);
    }

    /**
     * POST-маршрут
     *
     * @param  array $route Данные о маршруте
     * @return null         Маршрут не прошёл
     * @return mixed        Результат выполнения колбека
     */
    public static function post($route) {
        if($_SERVER['REQUEST_METHOD'] != 'POST') return;
        return self::request(array_merge(self::$default, $route), $_POST);
    }

    /**
     * AJAX-маршрут
     *
     * @param  array $route Данные о маршруте
     * @return null         Маршрут не прошёл
     * @return mixed        Результат выполнения колбека
     */
    public static function ajax($route) {
        if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') return;
        $route = array_merge(self::$default, $route);
        $type = strtoupper($route['type']);
        if($_SERVER['REQUEST_METHOD'] != $type) return;
        return self::request($route, self::getData());
    }

    /**
     * Колбек вызывается всегда
     *
     * @param  array $route Данные о маршруте
     * @return mixed        Результат выполнения колбека
     */
    public static function always($route) {
        return self::call($route, self::getData());
    }

    /**
     * Продолжить проведение маршрутов
     */
    public static function next() {
        self::$called = false;
    }

    /**
     * Возвращает актуальный массив данных запроса
     *
     * @return array Данные запроса
     */
    private static function getData() {
        return $_SERVER['REQUEST_METHOD'] == 'GET'? $_GET : $_POST;
    }

    // TODO: сделать private и геттер
    public static $called = false;                                                  // Флаг для определения была ли уже вызвана функция по текущему маршруту

    private static $default = array(                                                // Стандартные данные о маршруте
        'rule' => array(),
        'dev' => false,
        'next' => false,
        'type' => 'get'                                                             // Для AJAX-запросов
    );

    /**
     * Проведение маршрута
     *
     * @param  array $route Данные о маршруте
     * @param  array $data  Данные запроса
     * @return null         Маршрут не прошёл
     * @return mixed        Результат выполнения колбека
     */
    private static function request($route, $data) {
        if(self::$called || $route['dev'] && !DEV) return;

        $url = self::parseUrl();                                                    // Разбор строки запроса

        foreach(self::getUrlParts($route['url']) as $path) {                        // Цикл по разобранным путям из данных о маршруте
            if(self::isStar($path)) return self::call($route, $data);
            if(count($url) != count($path)) continue;

            foreach($path as $i => $part) {                                         // Цикл по частям маршрутного пути
                $var = self::isVar($url[$i], $part, $route['rule']);

                if(is_null($var) && $url[$i] != $part) {                            // Если часть пути не является переменной и не совпадает с соответствующей частью строки запроса
                    self::unsetVars();                                              // Очистить переменные строки запроса
                    continue 2;                                                     // Перейти к следующему пути из данных о маршруте
                }
            }

            !$route['next'] && self::$called = true;

            return self::call($route, $data);
        }
    }

    /**
     * Проверка на маршрут-звёздочку
     *
     * @param  array $path Разобранный путь
     * @return bool
     */
    private static function isStar($path) {
        return count($path) == 1 && $path[0] == '*';
    }

    /**
     * Вызов колбека
     *
     * @param  array $route Данные о маршруте
     * @param  array $data  Данные запроса
     * @return mixed        Результат выполнения колбека
     */
    private static function call($route, $data) {
        return call_user_func_array($route['call'], array($data));
    }

    /**
     * Проверка части маршрута на переменную и её обработка
     *
     * @param  string                $url  Часть запроса
     * @param  string                $part Часть маршрута
     * @param  array                 $rule Правила для переменных
     * @return null                        Часть маршрута не является переменной
     * @return string|int|float|bool       Значение установленной переменной
     */
    private static function isVar($url, $part, $rule) {
        $var = self::is('var', $part);
        if(!$var) return null;

        if(!array_key_exists($var, $rule)) {                                        // Если для переменной не задано правило
            return self::setVar($var, $url);                                        // то переменная устанавливается без проверки
        }

        $assertVar = self::testRule($rule[$var], $url);
        if(!is_null($assertVar)) {                                                  // Если переменная прошла проверку
            return self::setVar($var, $assertVar);                                  // то переменной устанавливается значение проверенного типа данных
        }
    }

    /**
     * Проверка части запроса на правила
     *
     * @param  string         $rule Правило для части запроса
     * @param  string         $url  Часть запроса
     * @return null                 Часть запроса не прошла проверку
     * @return string               Пройдена проверка на регулярное выражение
     * @return int|float|bool       Пройден проверочный шаблон
     */
    private static function testRule($rule, $url) {
        $template = self::is('ruleTemplate', $rule);
        if(!$template) {                                                            // Если правило для части запроса не является проверочным шаблоном
            return preg_match($rule, $url)? $url : null;                            // то оно является регулярным выражением
        }

        return self::testRuleTemplate($template, $url);
    }

    /**
     * Тестирование проверочных шаблонов
     *
     * @param  string         $template Имя шаблона
     * @param  string         $url      Часть запроса
     * @return null                     Часть запроса не прошла проверку
     * @return int|float|bool           Пройден проверочный шаблон
     * @throws \Exception               Неизвестный проверочный шаблон
     */
    private static function testRuleTemplate($template, $url) {

        switch($template) {
            case 'numeric':
                return is_numeric($url)? +$url : null;
            case 'int':
                return is_int(+$url)? +$url : null;
            case 'float':
                return is_float(+$url)? +$url : null;
            case 'natural':
                return (is_int(+$url) && +$url > 0)? +$url : null;
            case 'bool':
                if($url === 'true') return true;
                if($url === 'false') return false;
                return null;
        }

        throw new \Exception(message::exception('Undefined rule template: ' . $template));
    }

    private static $templates = array(                                              // Шаблоны для проверки на спецзначения
        'var'          => '/^\{(.+)\}$/',                                           // Переменная
        'ruleTemplate' => '/^\((.+)\)$/'                                            // Проверочный шаблон
    );

    /**
     * Проверка на спецзначение
     *
     * @param  string $template Имя спецзначения
     * @param  string $obj      Объект к проверке
     * @return false            Объект не прошёл проверку
     * @return string           Имя прошедшего проверку объекта
     */
    private static function is($template, $obj) {
        return !preg_match(self::$templates[$template], $obj, $match)? false : $match[1];
    }

    private static $url;                                                            // Объект для хранения переменных строки запроса

    /**
     * Возвращает объект переменных строки запроса
     *
     * @return object Объект переменных строки запроса
     */
    public static function url() {
        return self::$url;
    }

    /**
     * Устанавливает переменную строки запроса
     *
     * @param  string                 $key Имя переменной
     * @param  string|int|float|bool  $val Значение
     * @return string|int|float|bool       Установленное значение
     */
    private static function setVar($key, $val) {
        !self::$url && self::unsetVars();
        return self::$url->$key = $val;
    }

    /**
     * Обнуляет объект переменных строки запроса
     */
    private static function unsetVars() {
        self::$url = new \stdClass;
    }

    /**
     * Разбор путей из данных о маршруте
     *
     * @param  string|array $path Путь или массив путей
     * @return array              Массив разобранных путей
     */
    private static function getUrlParts($path) {

        is_string($path)?
            $paths[0] = $path :
            $paths    = $path;

        $pathArr = array();
        foreach($paths as $p) {
            array_push($pathArr, self::parseUrl($p));
        }

        return $pathArr;
    }

    /**
     * Разбор одного пути
     *
     * @param  string [$url] Путь
     * @return array         Разобранный путь
     */
    private static function parseUrl($url = null) {
        return preg_split('/\//', $url ?: parent::getUrl()['path'], -1, PREG_SPLIT_NO_EMPTY);
    }

//    private static $routes_default = array(                                // Умолчания для системных маршрутов
//        'type'    => 'GET',
//        'asserts' => array(),
//        'dev'     => false                                                 // Проводить маршрут всегда
//    );
//
//    public static $routes = array();                                       // Системные маршруты
//
//    /**
//     * Функция проведения системных маршуртов
//     *
//     */
//    public static function routes() {
//
//        foreach(self::$routes as $route) {                                 // Цикл по системным маршрутам
//
//            foreach(self::$routes_default as $key => $val)                 // Установка значений по умолчанию
//                if(!isset($route[$key]))                                   // для незаданных опций
//                    $route[$key] = $val;
//
//            if(!$route['dev'] || $route['dev'] && DEV)                     // Если маршрут надо проводить всегда или только для режима разработчика и режим включен
//                self::request($route['type'], $route['url'], $route['callback'], $route['asserts']);
//        }
//    }
}