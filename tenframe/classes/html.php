<?php

/**
 * Работа с tenhtml-файлами
 * @version 0.0.1
 */

/* Использование

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
            18) %block: {                                           // блоку можно передать свойство json
                    json: true                                      // (По умолчанию) <div class="block i-bem" onclick="return { "block":{} }"></div>
                    // или
                    json: false                                     // <div class="block"></div>
                    // или
                    json: {                                         // <div class="block i-bem" onclick="return { "block":{"a":100,"b":"string"} }"></div>
                        a: 100,
                        b: 'string'
                    }
                }
*/

namespace ten;

class html extends core {

    public static $tenhtmlFolder;                                                       // Директория для хранения шаблонов, сгенерированных из tenhtml
    public static $debugTemplates = array();                                            // Массив автоматически-сгенерированных шаблонов

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
        'body', 'json'
    );

    private static $iterateSeparator = '___';                                           // Разделитель ключа и его порядкового номера

    /**
     * Генерирования шаблона из tenhtml
     *
     * @param  string $file Путь до tenhtml-шаблона
     * @return string       Путь до сгенерированного шаблона
     */
    public static function savetenhtml($file) {

        if(!is_file($file)) return false;                                               // Если tenhtml-шаблон не существует

        $tenhtmlPath = core::resolvePath(                                               // Приведение пути к корректному виду
            self::$tenhtmlFolder,                                                       // Путь до папки хранения tenhtml
            text::ldel($file, ROOT)                                                     // Относительный путь до tenhtml-шаблона
        );

        if(in_array($tenhtmlPath, file::$debugAutogen)) {                               // Если этот шаблон уже был сгенерирован
            return $tenhtmlPath;                                                        // то повторная генерация не требуется
        }

        $tenhtml = preg_replace(                                                        // Удаление комментариев
            '/(\/\*([^*]|[\r\n]|(\*+([^*\/]|[\r\n])))*\*+\/)|(\/\/.*)/',
            '', file_get_contents($file)
        );

        $symbols = 'a-z0-9_\-\/<>';                                                     // Обычные символы, из которых состоят ключи

        $iterateSeparator = self::$iterateSeparator;

        $tenhtml = preg_replace_callback(                                               // Заключение ключей в кавычки
            '/'                                  .
                '('                              .
                    '['                          .
                        implode('', self::$spec) .
                        $symbols                 .
                    ']'                          .
                    '['                          .
                        '\t\s\n'                 .
                        implode('', self::$spec) .
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
            },
            $tenhtml
        );

        $tenhtml = json_decode(                                                         // Декодирование в JSON-дерево
            '{'                                  .                                      // Обрамление в фигурные скобки для валидного JSON
                str_replace(                                                            // Замена экранированных одинарных кавычек в двойные и удаление переносов строк
                    array("\'", "'", "\n"),
                    array("&apos;", '"', ''),
                    $tenhtml)                    .
            '}'
        );

        if(!$tenhtml) {                                                                 // Если не удалось получить JSON-дерево
            message::error('invalid tenhtml in ' . $file);
        }

        $gentpl = '';

        foreach($tenhtml as $key => $content) {                                         // Цикл по корневым элементам шаблона
            $gentpl .= self::parsetenhtml($key, $content);
        }

        $tpl = file::autogen(
            $tenhtmlPath,
            $gentpl,
            ''
        );

        array_push(self::$debugTemplates, $tpl);
        return $tpl;
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

        $keyInfo = self::parsetenhtmlKey($key);                                         // Получение массива информации по ключу

        $keywordResult = self::parseKeyword($keyInfo, $content, $block);                // Парсинг ключевого слова

        if($keywordResult) {                                                            // Если ключевое слово было найдено
            return $keywordResult;                                                      // то нужно просто вернуть его результат
        }                                                                               // Иначе получен обычный селектор

        if($keyInfo['block']) {                                                         // Если в ключе указан блок
            $block = $keyInfo['block'];                                                 // Текущий блок нужно переназначить
        }
        else if(!$block) {                                                              // Иначе если текущий блок не имеется
            message::error('Undefined block name');
        }

        return self::makeTag($keyInfo, $content, $block);                               // Формирование тега
    }

    /**
     * Получение чистого имени ключа без порядкового постфикса
     *
     * @param  string $key Имя ключа
     * @return string      Чистое имя ключа
     */
    private static function getClearKey($key) {
        return explode(self::$iterateSeparator, $key)[0];                               // Вернуть первую часть ключа по разделителю
    }

    /**
     * Проверка на необходимость инициализации блока
     *
     * @param  array $keyInfo Массив информации по ключу
     * @return bool           Нужно инициализировать или нет
     */
    private static function needJson($keyInfo) {
        return (
            $keyInfo['block'] &&                                                        // Если узел является блоком
            $keyInfo['tag'] != 'ctx' &&                                                 // не абстрактным
            (
                $keyInfo['json'] ||                                                     // и json задан в true или объект
                is_null($keyInfo['json'])                                               // или json не задан
            )
        );
    }

    /**
     * Установка атрибута onclick для передачи данных в i-bem
     *
     * @param  string       $inner   Строка, предваряющая парсингуемый контент
     * @param  array        $keyInfo Массив информации по ключу
     * @param  string|false $block   Имя контекстного блока
     * @return string                Строка, предваряющая парсингуемый контент с установленным при необходимости атрибутом onclick
     */
    private static function setJson($inner, $keyInfo, $block) {

        if(!self::needJson($keyInfo)) return $inner;                                    // Если блок не нужно инициализировать, можно вернуть полученную строку без добавления onclick

        switch(gettype($keyInfo['json'])) {
            case 'object':                                                              // Если json задан объектом
                foreach(get_object_vars($keyInfo['json']) as $key => $val) {            // Цикл по значениям объекта json
                    $keyInfo['json']->{self::getClearKey($key)} = $val;                 // Установка чистого ключа без порядкового постфикса
                    unset($keyInfo['json']->{$key});                                    // Удаление грязного ключа с постфиксом
                }
                break;

            case 'boolean':                                                             // Если json задан в true
            case 'NULL':                                                                // или если json не задан
                $keyInfo['json'] = new \stdClass();                                     // Достаточно передать пустой объект
        }

        return self::setAttrs($inner, (object)array(                                    // Установить атрибут onclick и заэкранировать кавычки
            'onclick' => htmlspecialchars('return { "' . $block . '":' . json_encode($keyInfo['json']) . ' }', ENT_QUOTES)
        ), $block);
    }

    /**
     * Рекурсивный парсинг контента
     *
     * @param  string              $inner   Строка, предваряющая парсингуемый контент
     * @param  array               $keyInfo Массив информации по ключу
     * @param  string|object|array $content Содержимое блока
     * @param  string|false        $block   Имя контекстного блока
     * @return string                       Пропарсенный контент
     */
    private static function parseContent($inner, $keyInfo, $content, $block = false) {

        $inner = self::setJson($inner, $keyInfo, $block);                               // Установить атрибут onclick

        switch(gettype($content)) {                                                     // Способ разбора зависит от типа данных

            case 'string':                                                              // Обычной строке надо только проставить переменные
                return $inner . self::setVar($content, $block);

            case 'object':                                                              // По объекту нужно пробежаться
                foreach($content as $key => $content) {

                    switch(self::getClearKey($key)) {                                   // Способ разбора зависит от ключа объекта

                        case 'attr':                                                    // Объект атрибутов
                            $inner = self::setAttrs($inner, $content, $block);
                            break;

                        case 'content':                                                 // Массив контента
                            $inner = self::parseContent($inner, $keyInfo, $content, $block);
                            break;

                        case 'json':                                                    // Наличие или отсутствие инициализации для i-bem
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

            $clearAttr = self::getClearKey($attr);                                                  // Получение чистого ключа атрибута без порядкового номера

            if(is_bool($val) && $val) {                                                             // Если атрибут = true
                $attributes .= ' ' . $clearAttr;                                                    // то это одиночный атрибут
                continue;
            }

            switch($clearAttr) {                                                                    // Обработка по имени атрибута

                case 'bool':                                                                        // Одиночные атрибуты по переменной
                    if(is_object($val)) {                                                           // Одиночные атрибуты должны быть указаны в виде объекта
                        foreach($val as $name => $variable) {
                            $attributes .= ' {{ if($' . self::getVarName($variable) . ', "' . self::getClearKey($name) . '") }}';
                        }
                        continue;
                    }

                default:                                                                            // Обычный атрибут
                    $attributes .= ' ' . $clearAttr . '="' . self::setVar($val, $block) . '"';      // Формирование строки атрибутов
            }
        }

        return substr_replace($inner, $attributes, strlen($inner) -1, 0);                           // Вставка сформированной строки перед закрывающей скобкой
    }

    /**
     * Поиск ключа на одном уровне объекта
     *
     * @param  string|object|array $content  Содержимое блока
     * @param  string              $childKey Имя искомого ключа
     * @return mixed|null                    Значение ключа или null, если ключ не найден
     */
    private static function getChildKey($content, $childKey) {

        if(is_string($content)) return null;                                                        // Если содержимое блока является строкой, то ключ считается не найденным

        foreach($content as $key => $val) {                                                         // Иначе содержимое блока является объектом или массивом
            if(self::getClearKey($key) == $childKey) {                                              // Если найден искомый ключ
                return $val;                                                                        // его нужно вернуть
            }
        }

        return null;                                                                                // Иначе ключ не найден
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

        if(!empty($keyInfo['block']) && $keyInfo['tag'] == 'ctx') {                     // Если нужно добавить только контекст блока без DOM-узла
            return self::parseContent('', $keyInfo, (($content) ? $content : ''), $block);
        }

        if($keyInfo['block']) {                                                         // Если текущий узел является блоком
            $keyInfo['json'] = self::getChildKey($content, 'json');                     // нужно определить его атрибут json
        }

        $class = self::genClass($block, $keyInfo);                                      // Генерация атрибута class

        return self::parseContent(                                                      // Рекурсивный парсинг контента
            '<' .
                $keyInfo['tag'] .
                ((!empty($class)) ? ' class="' . $class . '"' : '') .
            '>',
            $keyInfo,
            (($content) ? $content : ''),                                               // Если контент есть
            $block
        ) . (                                                                           // Если тег требуется закрыть
            (!in_array($keyInfo['tag'], self::$singleTags) && !$keyInfo['single']) ?
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

        $info = preg_split(                                                             // Разбор ключа на массив
            '/([' . implode('', self::$spec) . '])/',
            str_replace(' ', '', self::getClearKey($key)),                              // Удаление всех пробелов из ключа
            -1,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );

        $single  = false;                                                               // По умолчанию считается, что тег парный
        $keyword = false;                                                               // По умолчанию считается, что ключевого слова нет

        if(!in_array($info[0], self::$spec)) {                                          // Если первый элемент не является одним из зарезервированных спецсимволов

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

        if(in_array($tag, self::$keywords)) {                                           // Если полученный тег является ключевым словом
            $keyword = $tag;                                                            // Нужно его сохранить
            $tag = false;                                                               // и удалить тег
        }

        $block   = false;                                                               // По умолчанию считается, что блок не указан
        $elemmod = array();                                                             // Массив для элементов и модификаторов
        $mix     = array();                                                             // Массив для миксов

        if(count($info) > 0) {                                                          // Если для узла заданы элементы, модификаторы и миксы

            for($p = 0; $p < count($info); $p++) {                                      // Цикл по массиву селектора

                switch($info[$p]) {                                                     // Если текущий элемент

                    case self::$spec['block']:                                          // имя блока
                        $p++;
                        $block = $info[$p];
                        continue;

                    case self::$spec['elemmod']:                                        // имя элемента или модификатора
                        $p++;
                        array_push($elemmod, $info[$p]);
                        continue;

                    case self::$spec['mix']:                                            // микс
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
                return self::parseContent(
                    '{{ begin ' . $keyInfo['elemmod'][0] . ' }}',
                    $keyInfo,
                    $content,
                    $block
                ) .
                    '{{ end }}';

            case 'doctype':
                $keyInfo['single'] = true;
                $keyInfo['tag']    = '!doctype ' . $content;
                return self::makeTag($keyInfo, false);

            case 'lang':
                $keyInfo['single'] = true;
                $keyInfo['tag']    = 'meta';
                return self::makeTag($keyInfo, (object)array(
                    'attr' => (object)array(
                        'http-equiv' => 'Content-Language',
                        'content'    => $content
                    )
                ));

            case 'charset':
                $keyInfo['single'] = true;
                $keyInfo['tag']    = 'meta';
                return self::makeTag($keyInfo, (object)array(
                    'attr' => (object)array(
                        'http-equiv' => 'Content-Type',
                        'content'    => 'text/html; charset=' . $content
                    )
                ));

            case 'favicon':
                $keyInfo['single'] = true;
                $keyInfo['tag']    = 'link';
                return self::makeTag($keyInfo, (object)array(
                    'attr' => (object)array(
                        'type' => 'ico',
                        'rel'  => 'shortcut icon',
                        'href' => $content
                    )
                ));

            case 'css':
                $keyInfo['single'] = true;
                $keyInfo['tag']    = 'link';
                return self::makeTag($keyInfo, (object)array(
                    'attr' => (object)array(
                        'type' => 'text/css',
                        'rel'  => 'stylesheet',
                        'href' => $content
                    )
                ));

            case 'js':
                $keyInfo['tag'] = 'script';
                return self::makeTag($keyInfo, (object)array(
                    'attr' => (object)array(
                        'src' => $content
                    )
                ));

            case 'ie':
                return self::parseContent(
                    '<!--[if IE]>',
                    $keyInfo,
                    $content
                ) . '<![endif]-->';

            case 'ie<8':
                return self::parseContent(
                    '<!--[if lt IE 8]>',
                    $keyInfo,
                    $content
                ) . '<![endif]-->';

            case 'ie<9':
                return self::parseContent(
                    '<!--[if lt IE 9]>',
                    $keyInfo,
                    $content
                ) . '<![endif]-->';

            case 'html':
            case 'head':
            case 'title':
            case 'body':
                $keyInfo['tag'] = $keyInfo['keyword'];
                return self::makeTag($keyInfo, $content);
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

        if(self::needJson($info)) {                                                     // Если текущий узел является блоком и его нужно инициализировать
            array_push($class, 'i-bem');                                                // то нужно добавить ему класс i-bem
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

    private static $varRegexp = '/{\s*([a-z0-9_\-]+)\s*}/i';                            // Регулярное выражение поиска переменных

    /**
     * Установка переменных
     *
     * @param string $string Строка контента
     * @param string $block  Имя текущего блока
     * @return               Изменённая строка
     */
    private static function setVar($string, $block) {
        $string = str_replace(array('{this}'), array($block), $string);                 // Замена зарезервированных переменных
        $string = preg_replace(self::$varRegexp, '{{ $$1 }}', $string);                 // Замена переменных
        return str_replace(array('\{', '\}'), array('{', '}'), $string);                // Замена экранированных фигурных скобок
    }

    /**
     * Получение имени переменной из строки вида "{variable}"
     *
     * @param  string $string Строка с переменной
     * @return string         Имя переменной
     */
    private static function getVarName($string) {
        return preg_replace(self::$varRegexp, '$1', $string);
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
                    $string .= self::setVar($elem, $block);
                    break;

                case 'object':
                    foreach($elem as $key => $content) {
                        $string .= self::parsetenhtml($key, $content, $block);
                    }
                    break;
            }
        }

        return $string;
    }
}