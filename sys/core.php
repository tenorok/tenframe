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
            2) get::$arg->key1;
               get::$arg->key2;
               get::$arg->key3;

Получение значения GET-переменной:
        $value = core::$get->key;

    Подключение ядра:
        require 'sys/core.php';
        
    Включение автоподгрузки классов:
        spl_autoload_register(array('core', 'auto_load'));
    
    Парсинг blitz-шаблонов:
        echo core::block(array(                                        // Функция всегда принимает в качестве параметра массив
            
            'mod'   => 'modulename',                                   // Имя модуля. Если шаблон находится в модуле
            'block' => 'blockname',                                    // Обязательный. Имя блока
            'view'  => 'viewname',                                     // Имя шаблона. (По умолчанию: имя блока)
             
            'parse' => array(                                          // Массив парсинга
                'tplvar1' => 'val',                                    // Имя_переменной_в_шаблоне => значение
                'tplvar2' => core::block(array(...))                   // В качестве значения может быть другой блок. Вложенность не ограничена
            ),

            'context' => array(                                        // Массив контекстов begin-end
                                                                       // Контекст не может иметь следующие имена: array, parse, if, !if

                'context1',                                            // Простая активация контекста
                'context2' => array(                                   // Итерирование контекста и парсинг переменных. Контекст => массив_опций
                    'array' => array(                                  // Массив значений к перебору
                        array('key' => 'val1'),                        // элементом массива может быть как массив,
                        (new stdClass)->key = 'val2'                   // так и объект
                    ),
                    'parse' => array(                                  // Массив парсинга
                         'tplvar1' => 'key'                            // Имя_переменной_в_шаблоне => ключ_массива_или_объекта
                    ),
                    
                    'context3' => array(                               // Вложенность контекстов не ограничена
                        'parse' => array(                              // Если для текущего контекста не указан array,
                            'tplvar1' => 'key'                         // будут использованы переменные текущей итерации массива родителя
                        ),
                        'if'  => 'key',                                // Контекст будет проитерирован, если переменная массива (или объекта) с ключом key истинна
                        // или
                        '!if' => 'key'                                 // Контекст будет проитерирован, если переменная массива (или объекта) с ключом key ложна
                    ),
                    
                    'context4' => array(
                        'array' => 'subarray',                         // Если в качестве array указана строка
                                                                       // то это ключ массива контекста-родителя, которому соответствует вложенный массив (вложенность массивов не ограничена)
                        'parse' => array(                              // В таком случае
                            'tplvar1' => 'subkey'                      // в качестве ключей массива будут использоваться ключи вложенного массива
                        )
                    )
                ),
                'context5' => array(
                    'if' => $boolean,                                  // Контекст будет проитерирован, если переменная $boolean истинна
                    'parse' => array(...)
                ),
                'context6' => array(...)                               // Количество контекстов не ограничено
            )
        ));

    Синтаксис tenhtml-шаблонов:
        Типовые имена файлов: <view_name>.tenhtml

        Специальные символы:
            "%" - блок
            "." - элемент или модификатор
            "&" - микс

        Можно использовать привычные комментарии:
            "//" - однострочные
            "/*" - и многострочные

        Зарезервированные переменные:
            {this} - имя текущего блока

        В tenhtml JSON-подобный синтаксис:
            %page: {                                                // <div class="page">

                .__header: 'Content in header.',                        // <div class="page__header">Content in header.</div>

                section.__body: {                                       // <section class="page__body">
                    h1: 'Title of page',                                //     <h1>Title of page</h1>
                    ul%menu._side_left: {                               //     <ul class="menu menu_side_left">
                        for.items: {                                    //         {{ begin items}}
                            li                                          //         <li class="menu__item menu__item_selected">{{ $name }}</li>
                                .__item
                                ._selected: '{name}'
                        }                                               //         {{ end }}
                    }                                                   //     </ul>
                },                                                      // </section>

                .__footer: [                                            // <div class="page__footer">
                    'First paragraph.',                                 //     First paragraph.
                    {
                        a.__contact: {                                  //     <a href="/" class="page__contact">Link text.</a>
                            attr: {
                                href: "/"
                            },
                            content: 'Link text.'
                        },
                        img: {                                          //     <img src="image.png" alt="{logo}">
                            attr: {
                                src: 'image.png',
                                alt: '\\{logo\\}'
                            }
                        },
                        input: {                                        //     <input type="checkbox" name="box" {{ if($biggest, "checked") }}>
                            attr: {
                                type: 'checkbox',
                                name: 'box',
                                bool: {
                                    checked: '{biggest}'
                                }
                            }
                        },
                        mytag/                                          //     <mytag class="
                            .__logo                                     //                   page__logo
                            ._size_xl                                   //                   page__logo_size_xl
                            ._color_red                                 //                   page__logo_color_red
                            .__link                                     //                   page__link
                            ._align_right                               //                   page__logo_align_right page__link_align_right
                            &mytag__class: {                            //                   mytag__class"
                            attr: {
                                data-num: 100,                          //            data-num="100"
                                selected: true,                         //            selected
                                bool: {
                                    hided: '{visibility}',              //            {{ if($visibility, "hided") }}
                                }
                            }
                        }                                               //     >
                    },
                    'Second paragraph.'                                 //     Second paragraph.
                ],                                                      // </div>
                p: 'Current block: {this}'                              // <p>Current block: page</p>
            }                                                       // </div>

        Ключевые слова:
            1)  for.$$$                                             // контекст шаблонизатора, где $$$ - имя контекста
            2)  attr: {                                             // объект атрибутов тега
                    bool: {                                         // объект одиночных атрибутов по переменной
                        attribute: '{variable}'                     // атрибут "attribute" установится, если
                    }                                               // переменная $variable, переданная шаблону будет положительна
                }
            3)  content: '' | [] | {}                               // свойство для хранения содержимого тега
            4)  doctype: 'html'                                     // <!doctype html>
            5)  html: { ... }                                       // <html> ... </html>
            6)  head: { ... }                                       // <head> ... </head>
            7)  title: 'text'                                       // <title>text</title>
            8)  lang: 'ru'                                          // <meta http-equiv="Content-Language" content="ru">
            9)  charset: 'utf-8'                                    // <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            10) favicon: '/assets/images/favicon.ico'               // <link type="ico" rel="shortcut icon" href="/assets/images/favicon.ico">
            11) css: '/assets/css/style.css'                        // <link type="text/css" rel="stylesheet" href="/assets/css/style.css">
            12) js: '/assets/js/html5.js'                           // <script src="/assets/js/html5.js"></script>
            13) ie: { ... }                                         // <!--[if IE]> ... <![endif]-->
            14) ie<8: { ... }                                       // <!--[if lt IE 8]> ... <![endif]-->
            15) ie<9: { ... }                                       // <!--[if lt IE 9]> ... <![endif]-->
            16) body: { ... }                                       // <body> ... </body>
            17) ctx%blockname                                       // Установка контекста блока без DOM-узла

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

    private static $compressTplFolder = '/assets/__autogen__compressed';                // Директория для хранения сжатых шаблонов

    /**
     * Функция парсинга блоков
     * 
     * @param  array $options Параметры парсинга блока
     * @return string
     */
    public static function block($options) {

        foreach($options as $opt => $val)                                               // Переприсваивание массива опций в самостоятельные переменные
            $$opt = $val;

        if(!isset($view))                                                               // Если представление не указано
            $view = $block;                                                             // его имя соответствует имени блока

        $blocks = (isset($mod)) ? ROOT . '/mod/' . $mod . '/view/blocks/' : BLOCKS;     // Изменение начального пути, если указан модуль

        $extensions = array('tenhtml', 'tpl');                                          // Расширения файлов шаблонов в порядке приоритета

        foreach($extensions as $ext) {                                                  // Поиск существующих шаблонов

            $file = $blocks . $block . '/view/' . $view . '.' . $ext;                   // Полный путь к шаблону

            if($ext == 'tenhtml' && core::$settings['tenhtml']) {                       // Если рассматриваемое расширение tenhtml и включена его настройка

                if(DEV && file_exists($file)) {                                         // Если включен режим разработчика и шаблон существует
                    $file = thtml::savetenhtml($file);                                  // то его нужно преобразовать в простой шаблон
                }
                else {                                                                  // Иначе нужно просто взять уже сгенерированный простой шаблон
                    $file = ROOT . core::$compressTplFolder . ten_text::ldel($file, ROOT);
                }

                if(file_exists($file)) {                                                // Если этот уже сгенерированный простой шаблон существует
                    break;                                                              // то рассматривать менее приоритетные расширения не нужно
                }
            }
        }

        if(core::$settings['compressHTML'] && $ext != 'tenhtml') {                      // Если HTML нужно сжимать

            if(DEV) {                                                                   // Если включен режим разработчика
                ten_file::autogen(                                                      // Сохранение сжатого шаблона
                    core::$compressTplFolder . ten_text::ldel($file, ROOT),
                    core::compressHTML(file_get_contents($file)),
                    false
                );
            }

            $blocks = (isset($mod)) ?
                ROOT . core::$compressTplFolder . '/mod/' . $mod . '/view/blocks/' :
                ROOT . core::$compressTplFolder . '/view/blocks/';

            $compressedFile = $blocks . $block . '/view/' . $view . '.tpl';             // Полный путь к сжатому шаблону

            if(file_exists($compressedFile)) {
                $file = $compressedFile;
            }
        }

        $tpl = new Blitz($file);                                                        // Получение шаблона

        if(isset($context))                                                             // Если требуется контекст begin-end
            foreach($context as $ctx => $val)                                           // Цикл по контекстам
                core::context($tpl, $ctx, $val);

        if(!isset($parse))                                                              // Если не задан элемент parse
            $parse = array();                                                           // нужно его присвоить

        return $tpl->parse($parse);                                                     // Получение отпарсиного шаблона
    }

    /**
     * Компрессирует HTML
     * 
     * @param  string $html HTML-строка
     * @return string       Сжатая HTML-строка
     */
    public static function compressHTML($html) {

        return preg_replace('#(?ix)(?>[^\S ]\s*|\s{2,})(?=(?:(?:[^<]++|<(?!/?(?:textarea|pre)\b))*+)(?:<(?>textarea|pre)\b|\z))#', '', $html);
    }

    private static $ctx_reserve = array(                                                // Зарезервированные переменные, именами которых не могут называться контексты
        'array', 'parse', 'if', '!if'
    );
    private static $ctx_last_array_element;                                             // Переменная для хранения текущего элемента контекста родителя при рекурсивном вызове
    private static $ctx_parent_array;                                                   // Переменная для хранения массива родителя при рекурсивном вызове

    /**
     * Рекурсивная функция парсинга контекстов
     * 
     * @param Blitz  $tpl Объект шаблона
     * @param string $ctx Имя контекста
     * @param array  $val Массив с описанием контекста
     */
    private static function context($tpl, $ctx, $val) {

        if(is_array($val)) {                                                            // Если контекст нужно проитерировать
            
            if(
                !isset($val['array']) &&                                                // Если у текущего контекста не задан массив к перебору
                !empty(core::$ctx_last_array_element)                                   // и существует последний элемент предыдущего контекста
            ) {
                self::iterateContextArray(
                    $tpl, $ctx, $val,
                    array(core::$ctx_last_array_element)                                // то текущий контекст нужно отпарсить в соответствии с текущим элементов контекста родителя
                );
            }
            else if(isset($val['array'])) {                                             // Иначе если массив задан в обычном виде массива
                self::iterateContextArray($tpl, $ctx, $val, $val['array']);             // и нужно его сохранить в качестве родительского массива
            }
            else {                                                                      // Иначе массива к перебору нет
                self::iterateContext($tpl, $ctx, $val);                                 // и нужно просто отпарсить контекст
            }
        }
        else                                                                            // Иначе контекст нужно просто активировать
            $tpl->iterate($val);                                                        // Активация контекста
    }

    /**
     * Итерация контекста по массиву
     *
     * @param Blitz  $tpl   Объект шаблона
     * @param string $ctx   Имя контекста
     * @param array  $val   Массив с описанием контекста
     * @param array  $array Массив к итерации
     */
    private static function iterateContextArray($tpl, $ctx, $val, $array) {

        foreach($array as $i => $element) {                                             // Цикл по массиву значений к присваиванию

            if(!self::resolveCondition($val, $element))                                 // Если условие итерирования не проходит
                break;                                                                  // то выполнять парсинг не нужно

            core::$ctx_last_array_element = $element;                                   // Сохранение последнего обработанного элемента массива контекста

            $tmp = array();                                                             // Временный массив для хранения сопоставленных значений текущей итерации

            if(isset($val['parse']))                                                    // Если переменная parse задана
                foreach($val['parse'] as $parse_key => $parse_val) {                    // Цикл по массиву ключей: переменная_шаблона => ключ_массива_значений

                    $tmp_val =
                        (is_object($element)) ?                                         // Если текущий элемент массива значений является объектом
                        $element->$parse_val :                                          // требуется такой способ получения его значения
                        $element [$parse_val];                                          // Иначе это массив и требуется иной способ получения значения

                    $tmp[$parse_key] = $tmp_val;                                        // Добавление элемента с текущим значением во временный массив
                }

            $tpl->block($ctx, $tmp);                                                    // Парсинг текущей итерации

            self::iterateContextKeys($tpl, $ctx, $val, $element);                       // Проитерировать остальные ключи контекста
        }
    }

    /**
     * Простая итерация контекста
     *
     * @param Blitz  $tpl Объект шаблона
     * @param string $ctx Имя контекста
     * @param array  $val Массив с описанием контекста
     */
    private static function iterateContext($tpl, $ctx, $val) {

        if(!self::resolveCondition($val)) {                                             // Если условие не проходит
            return;                                                                     // то итерировать контекст не нужно
        }

        $tmp = array();                                                                 // Временный массив для хранения сопоставленных значений текущей итерации
            
        if(isset($val['parse'])) {                                                      // Если переменная parse задана
            foreach($val['parse'] as $parse_key => $parse_val) {                        // Цикл по массиву ключей: переменная_шаблона => ключ_массива_значений
                $tmp[$parse_key] = $parse_val;                                          // Добавление элемента с текущим значением во временный массив
            }
        }

        $tpl->block($ctx, $tmp);                                                        // Парсинг текущей итерации

        self::iterateContextKeys($tpl, $ctx, $val);                                     // Проитерировать остальные ключи контекста
    }

    /**
     * Проверка условий
     *
     * @param  array         $val     Массив с описанием контекста
     * @param  array|object  $element Текущий элемент с полем к проверке на условие
     * @return boolean                Результат проверки
     */
    private static function resolveCondition($val, $element = false) {
        return !(
            isset($val['if'])  &&                                                       // Если задано положительное условие
            (
                is_string($val['if']) &&                                                // и значением условия является имя ключа массива или объекта
                (
                    is_array($element) &&                                               // и элемент является массивом
                    (
                        !isset($element[$val['if']]) ||                                 // и переменная не существует
                              !$element[$val['if']]                                     // или она отрицательна
                    ) ||
                    is_object($element) &&                                              // или элемент является объектом
                    (
                        !isset($element->$val['if']) ||                                 // и переменная не существует
                              !$element->$val['if']                                     // или она отрицательна
                    )
                ) ||

                is_bool($val['if']) &&                                                  // или к проверке передано логическое значение
                !$val['if']                                                             // и оно не выполняется

            ) ||

            isset($val['!if']) &&                                                       // или задано отрицательное условие
            (
                is_string($val['!if']) &&                                               // и значением условия является имя ключа массива или объекта
                (
                    is_array($element) &&                                               // и элемент является массивом
                    (
                        isset($element[$val['!if']]) &&                                 // и существует переменная к проверке
                              $element[$val['!if']]                                     // и она положительна
                    ) ||
                    is_object($element) &&                                              // или элемент является объектом
                    (
                        isset($element->$val['!if']) &&                                 // и существует переменная к проверке
                              $element->$val['!if']                                     // и она положительна
                    )
                ) ||

                is_bool($val['!if']) &&                                                 // или к проверке передано логическое значение
                $val['!if']                                                             // и оно выполняется
            )
        );
    }

    /**
     * Пробежка по всем ключам контекста (в том числе по вложенным контекстам)
     *
     * @param Blitz         $tpl     Объект шаблона
     * @param string        $ctx     Имя контекста
     * @param array         $val     Массив с описанием контекста
     * @param array|object  $element Текущий элемент итерирования контекста по массиву
     */
    private static function iterateContextKeys($tpl, $ctx, $val, $element = false) {

        foreach($val as $ctx2 => $arr) {                                                // Цикл по элементам контекста
            if(
                !in_array($ctx2, self::$ctx_reserve) ||                                 // Если ключ не является служебной переменной
                !$ctx2                                                                  // или ключа не существует
            ) {                                                                         // То это вложенный контекст
                if(!$ctx2) {                                                            // Если ключа не существует
                    $arr = $ctx . '/' . $arr;                                           // то этот контекст нужно просто активировать
                }
                else if(
                    isset($arr['array']) &&                                             // Если задана опция массива
                    is_string($arr['array'])                                            // в текстовом виде
                ) {
                    self::$ctx_parent_array = $element;                                 // Задание родительской таблицы в соответствии с текущим элементом

                    if(isset(self::$ctx_parent_array[$arr['array']]))                   // Если существует массив предыдущего контекста
                        $arr['array'] = self::$ctx_parent_array[$arr['array']];
                    else                                                                // Иначе не существует такого массива
                        continue;                                                       // и нужно перейти к следующей итерации
                }

                self::context($tpl, $ctx . '/' . $ctx2, $arr);                          // Рекурсивный вызов вложенного контекста
            }
        }
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