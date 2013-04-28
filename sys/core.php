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

/*    get

    Получение значения GET-переменной:
        $value = get::$arg->key;
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
                        get::set_arg($match[1], $urn[$part]);              // Добавление пары ключ-значение в объект для работы с переменными
                    }
                    else {                                                 // Иначе переменная не проходит проверку регулярным выражением
                        get::unset_args();                                 // Нужно очистить объект переменных
                        continue 2;                                        // и вызывать следующий маршрут в index.php
                    }
                else                                                       // иначе часть пути не является переменной
                    if($urn[$part] != $path[$part]) {                      // и если часть URN не совпадает с частью пути
                        get::unset_args();                                 // Нужно очистить объект переменных
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
                    $file = core::savetenhtml($file);                                   // то его нужно преобразовать в простой шаблон
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
                    is_object($element) &&                                              // иkb элемент является объектом
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

    private static $tenhtmlFolder = '/assets/__autogen__tenhtml';                       // Директория для хранения шаблонов, сгенерированных из tenhtml

    private static $spec = array(                                                       // Массив зарезервированных специальных символов
        'block'   => '%',                                                               // Блок
        'elemmod' => '.',                                                               // Элемент или модификатор
        'mix'     => '&'                                                                // Миксованное значение
    );

    private static $keywords = array(                                                   // Массив ключевых слов tenhtml
        'for', 'attr', 'content',
        'doctype', 'html', 'head',
        'title', 'lang', 'charset', 'favicon',
        'css', 'js',
        'ie', 'ie<8', 'ie<9',
        'body'
    );

    private static $iterateSeparator = '___';                                           // Разделитель ключа и его порядкового номера

    /**
     * Генерирования шаблона из tenhtml
     *
     * @param  string $file Путь до tenhtml-шаблона
     * @return string       Путь до сгенерированного шаблона
     */
    private static function savetenhtml($file) {

        $tenhtml = preg_replace(                                                        // Удаление комментариев
            '/(\/\*([^*]|[\r\n]|(\*+([^*\/]|[\r\n])))*\*+\/)|(\/\/.*)/',
            '', file_get_contents($file)
        );

        $symbols = 'a-z0-9_\-\/<>';                                                     // Обычные символы, из которых состоят ключи

        $iterateSeparator = core::$iterateSeparator;

        $tenhtml = preg_replace_callback(                                               // Заключение ключей в кавычки
            '/'                                  .
                '('                              .
                    '['                          .
                        implode('', core::$spec) .
                        $symbols                 .
                    ']'                          .
                    '['                          .
                        '\t\s\n'                 .
                        implode('', core::$spec) .
                        $symbols                 .
                    ']*'                         .
                ')'                              .
                '(?=:\s+['                       .                                      // Перед значением ключа могут стоять пробелы
                    '\{|\[|\'|\"|'               .                                      // Ключ может начинаться со скобок или кавычек
                    '\d+|'                       .                                      // Ключ может быть числом
                    'true|false'                 .                                      // Ключ может быть булевым значением
                '])'                             .
            '/i',
            function($match) use ($iterateSeparator) {                                  // Обеспечение возможности использования одинаковых ключей объекта
                static $i = 0;
                return '"' . $match[1] . $iterateSeparator . ($i++) . '"';              // Каждый найденный ключ дополняется уникальным порядковым номером
            }, $tenhtml);

        $tenhtml = json_decode(                                                         // Декодирование в JSON-дерево
            '{'                                  .                                      // Обрамление в фигурные скобки для валидного JSON
                str_replace(                                                            // Замена экранированных одинарных кавычек в двойные и удаление переносов строк
                    array("\'", "'", "\n"),
                    array("&apos;", '"', ''),
                    $tenhtml)                    .
            '}');

        if(!$tenhtml) {                                                                 // Если не удалось получить JSON-дерево
            tmsg::error('invalid tenhtml in ' . $file);
        }

        $gentpl = '';

        foreach($tenhtml as $key => $content) {                                         // Цикл по корневым элементам шаблона
            $gentpl .= core::parsetenhtml($key, $content);
        }

        return ten_file::autogen(
            core::$tenhtmlFolder . ten_text::ldel($file, ROOT),
            $gentpl,
            ''
        );
    }

    private static $singleTags = array(                                                 // Список непарных html-тегов
        'area', 'base',  'br',   'col',  'hr',
        'img',  'input', 'link', 'meta', 'param'
    );

    /**
     * Рекурсивный парсинг tenhtml-блоков
     *
     * @param  string              $key     Ключ селектора
     * @param  string|object|array $content Содержимое блока
     * @param  string|false        $block   Имя блока, в контексте которого назначаются элементы и модификаторы
     * @return string                       Сгенерированный шаблон
     */
    private static function parsetenhtml($key, $content, $block = false) {

        $keyInfo = core::parsetenhtmlKey($key);                                         // Получение массива информации по ключу

        $keywordResult = core::parseKeyword($keyInfo, $content, $block);                // Парсинг ключевого слова

        if($keywordResult) {                                                            // Если ключевое слово было найдено
            return $keywordResult;                                                      // то нужно просто вернуть его результат
        }                                                                               // Иначе получен обычный селектор

        if($keyInfo['block']) {                                                         // Если в ключе указан блок
            $block = $keyInfo['block'];                                                 // Текущий блок нужно переназначить
        }
        else if(!$block) {                                                              // Иначе если текущий блок не имеется
            tmsg::error('Undefined block name');
        }

        return core::makeTag($keyInfo, $content, $block);                               // Формирование тега
    }

    /**
     * Рекурсивный парсинг контента
     *
     * @param  string              $inner   Строка, предваряющая парсингуемый контент
     * @param  string|object|array $content Содержимое блока
     * @param  string|false        $block   Имя контекстного блока
     * @return string                       Пропарсенный контент
     */
    private static function parseContent($inner, $content, $block = false) {

        switch(gettype($content)) {                                                     // Способ разбора зависит от типа данных

            case 'string':                                                              // Обычной строке надо только проставить переменные
                return $inner . self::setVar($content, $block);

            case 'object':                                                              // По объекту нужно пробежаться
                foreach($content as $key => $content) {

                    $clearKey = explode(self::$iterateSeparator, $key);                 // Получение чистого ключа объекта без порядкового номера

                    switch($clearKey[0]) {                                              // Способ разбора зависит от ключа объекта

                        case 'attr':                                                    // Объект атрибутов
                            $inner = self::setAttrs($inner, $content, $block);
                            break;

                        case 'content':                                                 // Массив контента
                            $inner = self::parseContent($inner, $content, $block);
                            break;

                        default:                                                        // Произвольный ключ (селектор)
                            $inner .= self::parsetenhtml($key, $content, $block);
                    }
                }
                return $inner;

            case 'array':                                                               // Нужно разобрать массив
                return $inner . self::parseArray($content, $block);
        }
    }

    /**
     * Установка атрибутов
     *
     * @param  string              $inner   Предваряющая строка, в последний тег которой будут добавлены атрибуты
     * @param  object              $content Объект атрибутов
     * @param  string|false        $block   Имя блока, в контексте которого устанавливаются переменные
     * @return string                       Тег с добавленными атрибутами
     */
    private static function setAttrs($inner, $content, $block) {

        $attributes = '';                                                                           // Подготовка строки под атрибуты

        foreach($content as $attr => $val) {

            $clearAttr = explode(self::$iterateSeparator, $attr);                                   // Получение чистого ключа атрибута без порядкового номера

            if(is_bool($val) && $val) {                                                             // Если атрибут = true
                $attributes .= ' ' . $clearAttr[0];                                                 // то это одиночный атрибут
                continue;
            }

            switch($clearAttr[0]) {                                                                 // Обработка по имени атрибута

                case 'bool':                                                                        // Одиночные атрибуты по переменной
                    if(is_object($val)) {                                                           // Одиночные атрибуты должны быть указаны в виде объекта
                        foreach($val as $name => $variable) {
                            $clearBoolName = explode(self::$iterateSeparator, $name);
                            $attributes .= ' {{ if($' . self::getVarName($variable) . ', "' . $clearBoolName[0] . '") }}';
                        }
                        continue;
                    }

                default:                                                                            // Обычный атрибут
                    $attributes .= ' ' . $clearAttr[0] . '="' . self::setVar($val, $block) . '"';   // Формирование строки атрибутов
            }
        }

        return substr_replace($inner, $attributes, strlen($inner) -1, 0);                           // Вставка сформированной строки перед закрывающей скобкой
    }

    /**
     * Формирование тега
     *
     * @param  array               $keyInfo Массив информации по ключу
     * @param  string|object|array $content Содержимое блока
     * @param  string|false        $block   Имя блока, в контексте которого назначаются элементы и модификаторы
     * @return string                       Сформированный тег и его содержимое
     */
    private static function makeTag($keyInfo, $content, $block = false) {

        $class = core::genClass($block, $keyInfo);                                      // Генерация атрибута class

        if(!empty($keyInfo['block']) && $keyInfo['tag'] == 'ctx') {                     // Если нужно добавить только контекст блока без DOM-узла
            return core::parseContent('', (($content) ? $content : ''), $block);
        }

        return core::parseContent(                                                      // Рекурсивный парсинг контента
            '<' .
                $keyInfo['tag'] .
                ((!empty($class)) ? ' class="' . $class . '"' : '') .
            '>',
            (($content) ? $content : ''),                                               // Если контент есть
            $block
        ) . (                                                                           // Если тег требуется закрыть
            (!in_array($keyInfo['tag'], core::$singleTags) && !$keyInfo['single']) ?
                '</' . $keyInfo['tag'] . '>' :
                ''
        );
    }

    /**
     * Формирование массива с информацией по ключу tenhtml-шаблона
     *
     * @param  string $key Ключ tenhtml-шаблона
     * @return array       Массив информации по ключу
     */
    private static function parsetenhtmlKey($key) {

        $clearKey = explode(core::$iterateSeparator, $key);                             // Получение чистого ключа объекта без порядкового номера

        $info = preg_split(                                                             // Разбор ключа на массив
            '/([' . implode('', core::$spec) . '])/',
            str_replace(' ', '', $clearKey[0]),                                         // Удаление всех пробелов из ключа
            -1,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );

        $single  = false;                                                               // По умолчанию считается, что тег парный
        $keyword = false;                                                               // По умолчанию считается, что ключевого слова нет

        if(!in_array($info[0], core::$spec)) {                                          // Если первый элемент не является одним из зарезервированных спецсимволов

            if(substr($info[0], -1) == '/') {                                           // Если указано принудительное закрытие тега
                $tag = substr($info[0], 0, -1);
                $single = true;                                                         // Тег является непарным
            }
            else {                                                                      // Иначе принудительное закрытие тега не указано
                $tag = $info[0];                                                        // то это явно указанное имя тега
            }

            array_shift($info);
        }
        else {                                                                          // Иначе имя тега не указано
            $tag = 'div';                                                               // и проставляется дефолтный тег
        }

        if(in_array($tag, core::$keywords)) {                                           // Если полученный тег является ключевым словом
            $keyword = $tag;                                                            // Нужно его сохранить
            $tag = false;                                                               // и удалить тег
        }

        $block   = false;                                                               // По умолчанию считается, что блок не указан
        $elemmod = array();                                                             // Массив для элементов и модификаторов
        $mix     = array();                                                             // Массив для миксов

        if(count($info) > 0) {                                                          // Если для узла заданы элементы, модификаторы и миксы

            for($p = 0; $p < count($info); $p++) {                                      // Цикл по массиву селектора

                switch($info[$p]) {                                                     // Если текущий элемент

                    case core::$spec['block']:                                          // имя блока
                        $p++;
                        $block = $info[$p];
                        continue;

                    case core::$spec['elemmod']:                                        // имя элемента или модификатора
                        $p++;
                        array_push($elemmod, $info[$p]);
                        continue;

                    case core::$spec['mix']:                                            // микс
                        $p++;
                        array_push($mix, $info[$p]);
                        continue;
                }
            }
        }

        return array(                                                                   // возврат информационного массива
            'keyword' => $keyword,
            'tag'     => $tag,
            'single'  => $single,
            'block'   => $block,
            'elemmod' => $elemmod,
            'mix'     => $mix
        );
    }

    /**
     * Парсинг ключевых слов
     *
     * @param  array               $keyInfo Массив информации по ключу
     * @param  string|object|array $content Содержимое блока
     * @param  string|false        $block   Имя блока, в контексте которого назначаются элементы и модификаторы
     * @return string|false                 Результат обработки ключевого слова
     */
    private static function parseKeyword($keyInfo, $content, $block) {

        if(empty($keyInfo['keyword'])) {                                                // Если ключевого слова нет
            return false;                                                               // то функция об этом сообщает
        }

        switch($keyInfo['keyword']) {

            case 'for':                                                                 // Ключевое слово for
                return core::parseContent(
                    '{{ begin ' . $keyInfo['elemmod'][0] . ' }}',
                    $content,
                    $block
                ) .
                '{{ end }}';

            case 'doctype':
                $keyInfo['single'] = true;
                $keyInfo['tag']    = '!doctype ' . $content;
                return core::makeTag($keyInfo, false);

            case 'lang':
                $keyInfo['single'] = true;
                $keyInfo['tag']    = 'meta';
                return core::makeTag($keyInfo, (object)array(
                    'attr' => (object)array(
                        'http-equiv' => 'Content-Language',
                        'content'    => $content
                    )
                ));

            case 'charset':
                $keyInfo['single'] = true;
                $keyInfo['tag']    = 'meta';
                return core::makeTag($keyInfo, (object)array(
                    'attr' => (object)array(
                        'http-equiv' => 'Content-Type',
                        'content'    => 'text/html; charset=' . $content
                    )
                ));

            case 'favicon':
                $keyInfo['single'] = true;
                $keyInfo['tag']    = 'link';
                return core::makeTag($keyInfo, (object)array(
                    'attr' => (object)array(
                        'type' => 'ico',
                        'rel'  => 'shortcut icon',
                        'href' => $content
                    )
                ));

            case 'css':
                $keyInfo['single'] = true;
                $keyInfo['tag']    = 'link';
                return core::makeTag($keyInfo, (object)array(
                    'attr' => (object)array(
                        'type' => 'text/css',
                        'rel'  => 'stylesheet',
                        'href' => $content
                    )
                ));

            case 'js':
                $keyInfo['tag'] = 'script';
                return core::makeTag($keyInfo, (object)array(
                    'attr' => (object)array(
                        'src' => $content
                    )
                ));

            case 'ie':
                return core::parseContent(
                    '<!--[if IE]>',
                    $content
                ) . '<![endif]-->';

            case 'ie<8':
                return core::parseContent(
                    '<!--[if lt IE 8]>',
                    $content
                ) . '<![endif]-->';

            case 'ie<9':
                return core::parseContent(
                    '<!--[if lt IE 9]>',
                    $content
                ) . '<![endif]-->';

            case 'html':
            case 'head':
            case 'title':
            case 'body':
                $keyInfo['tag'] = $keyInfo['keyword'];
                return core::makeTag($keyInfo, $content);
        }
    }

    /**
     * Генерация атрибута class
     *
     * @param  string $block Имя текущего блока
     * @param  array  $info  Массив информации по ключу
     * @return string        Строка с открытым тегом и атрибутом class
     */
    private static function genClass($block, $info) {

        $blockElems = array($block);                                                    // Массив для хранения имени блока и его элементов
        $class      = array();                                                          // Массив атрибута class

        if($info['block']) {                                                            // Если текущий узел является блоком
            array_push($class, $info['block']);                                         // то нужно добавить его в атрибут class
        }

        foreach($info['elemmod'] as $elemmod) {                                         // Цикл по элементам и модификаторам

            if(substr($elemmod, 0, 2) == '__') {                                        // Элемент
                array_push($blockElems, $elemmod);
                array_push($class, $block . $elemmod);
            }
            else if($elemmod[0] == '_') {                                               // Модификатор
                
                if($info['block']) {                                                    // Если текущий узел является блоком
                    array_push($class, $info['block'] . $elemmod);                      // то к нему надо применить модификатор
                }

                for($elem = 1; $elem < count($blockElems); $elem++) {                   // Цикл по элементам начинается со второго итема, потому что первым идёт имя текущего блока
                    array_push($class, $block . $blockElems[$elem] . $elemmod);         // Применение модификатора к элементу блока
                }
            }
        }

        foreach($info['mix'] as $mix) {
            array_push($class, $mix);
        }

        return implode(' ', $class);
    }

    /**
     * Установка переменных
     *
     * @param string $string Строка контента
     * @param string $block  Имя текущего блока
     * @return               Изменённая строка
     */
    private static function setVar($string, $block) {
        $string = str_replace(array('{this}'), array($block), $string);                 // Замена зарезервированных переменных
        $string = preg_replace('/{\s*([a-z0-9_\-]*)\s*}/i', '{{ $$1 }}', $string);      // Замена переменных
        return str_replace(array('\{', '\}'), array('{', '}'), $string);                // Замена экранированных фигурных скобок
    }

    /**
     * Получение имени переменной из строки вида "{variable}"
     *
     * @param  string $string Строка с переменной
     * @return string         Имя переменной
     */
    private static function getVarName($string) {
        return preg_replace('/{\s*([a-z0-9_\-]*)\s*}/i', '$1', $string);
    }

    /**
     * Парсинг tenhtml-массивов
     *
     * @param  array  $array Массив к парсингу
     * @param  string $block Имя текущего блока
     * @return string        Результирующая строка
     */
    private static function parseArray($array, $block) {

        $string = '';

        foreach($array as $elem) {

            switch(gettype($elem)) {

                case 'string':
                    $string .= core::setVar($elem, $block);
                    break;

                case 'object':
                    foreach($elem as $key => $content) {
                        $string .= core::parsetenhtml($key, $content, $block);
                    }
                    break;
            }
        }

        return $string;
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

// Класс работы с GET-переменными
class get {
    
    public static $arg;                                                    // Объект, который используется из приложения для обращения к GET-переменным
    
    /**
     * Функция добавления свойства для объекта $arg
     *
     * @param string $key Имя GET-переменной
     * @param string $val Значение GET-переменной
     */
    public static function set_arg($key, $val) {
        
        get::$arg->$key = $val;
    }
    
    /**
     * Функция удаления всех свойств объекта $arg
     *
     */
    public static function unset_args() {
        
        if(count(get::$arg))                                               // Если объект аргументов содержит хотя бы одно значение
            foreach(get_object_vars(get::$arg) as $key => $val)
                get::$arg->$key = '';
    }
}