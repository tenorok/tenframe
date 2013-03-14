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
                        'if'  => 'val1',                               // Контекст будет проитерирован, если переменная val1 истинна
                        или
                        '!if' => 'val1'                                // Контекст будет проитерирован, если переменная val1 ложна
                    ),
                    
                    'context4' => array(
                        'array' => 'subarray',                         // Если в качестве array указана строка
                                                                       // то это ключ массива контекста-родителя, которому соответствует вложенный массив (вложенность массивов не ограничена)
                        'parse' => array(                              // В таком случае
                            'tplvar1' => 'subkey'                      // в качестве ключей массива будут использоваться ключи вложенного массива
                        )
                    )
                ),
                'context5' => array(...)                               // Количество контекстов не ограничено
            )
        ));

    Синтаксис tenhtml-шаблонов:
        Типовые имена файлов: <view_name>.tenhtml

        Специальные символы:
            "%" - блок
            "." - элемент или модификатор
            "&" - микс

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
                        mytag/                                          //     <mytag class="
                            .__logo                                     //                   page__logo
                            ._size_xl                                   //                   page__logo_size_xl
                            ._color_red                                 //                   page__logo_color_red
                            .__link                                     //                   page__link
                            ._align_right                               //                   page__logo_align_right page__link_align_right
                            &mytag__class: {                            //                   mytag__class"
                            attr: {                                     //            data-num="100">
                                data-num: 100
                            }
                        }
                    },
                    'Second paragraph.'                                 //     Second paragraph.
                ]                                                       // </div>
            }                                                       // </div>

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

/* orm
    
    Правило наименования ключей в БД:
        Первичный ключ: table_id
        Внешний ключ:   table_fk

    Подключение к MySQL:
        orm::connect('host', 'login', 'password');

    Выбор базы данных:
        orm::db('dbname');

    Добавление записи:
        $last_id = orm::insert('table', array(                         // Возвращается идентификатор добавленной записи или false, если запрос не был выполнен
            'field_1' => 'value',                                      // Перечисление полей и значений
            'field_2' => 'func: now()'                                 // Для использования функций применяется ключевое слово "func:"
        ));

    Обновление записи:
        orm::update('table', array(                                    // Возвращается true или false
            'field_1' => 'value',                                      // Перечисление полей и значений
            'field_2' => 'func: now()'                                 // Для использования функций применяется ключевое слово "func:"
        ))
        ->where('table_id > 10');                                      // Обязательная опция. В качестве условия может быть строка
        ->where(10);                                                   // или число (такая запись идентична: table_id = 10)
        ->where('all');                                                // или применить для всех строк таблицы (длинная запись)
        ->where('*');                                                  //     применить для всех строк таблицы (краткая запись)

    Удаление записи:
        orm::delete('table')                                           // Возвращается true или false
            ->where(...);                                              // Обязательная опция. Условия удаления

    Выборка записей:
        $result =                                                      // Результаты выборки возвращаются в виде массива объектов
                                                                       // или в виде одного объекта, если был указан "->where(число)"
            orm::select('table')                                       // По умолчанию выбираются все поля таблицы
                ->sub(array(                                           // Подзапросы
                    'select count(*) from tab1' => 'count1'            // Идентично select count(*) from tab1 as `count1`
                ))
                ->fields('*, sum(field1)')                             // Явное          указание select
                ->addfields('sum(field1)')                             // Дополнительное указание select (Аналогично предыдущей строке)
                ->order('field1')                                      // Сортировка
                ->group('field1')                                      // Группировка
                ->limit('0, 10')                                       // Лимит
                ->prefix('prefix_')                                    // Префикс
                ->where(...);                                          // Обязательно указывать последней! Последовательность предыдущих опций свободна

        $result =                                                      // Результатом выборки будет всегда массив объектов
            orm::join('table', array(                                  // From table и массив join-таблиц
                
                array(                                                 // Описание подключаемой таблицы
                    'table'  => 'tablename_1',                         // Обязательный. Имя подключаемой таблицы
                    'join'   => 'inner',                               // Тип join: inner (по умолчанию), left outer, right outer, full outer, cross
                    'on'     => '...',                                 // Дополнительное условие для соединения таблиц
                    'left'   => 'users',                               // left | right; По умолчанию: 'left' => 'table' (Обычное направление связи к первоначальной таблице)
                    'prefix' => 'prefix_'                              // Префикс для полей данной таблицы
                ),

                array(
                    'table'  => 'tablename_2',                         // Таблица tablename_2
                    'right'  => 'tablename_1'                          // Подключается к таблице tablename_1 в обратном направлении связи
                                                                       // Иначе говоря, в данном случае tablename_1 играет роль таблицы-связки
                ),

                array(
                    'table'  => 'tablename_3'                          // Таблица tablename_3 подключится к первоначальной таблице table
                )
            ))
            ->sub, fields, addfields, order, group, limit              // те же опции, что и в select
            ->prefix('prefix_{table}')                                 // Префикс. Вместо {table} подставится имя таблицы
            ->where(...);                                              // Обязательно указывать последней! Последовательность предыдущих опций свободна

            Важно:
                Если в результате объединения таблиц появляются одинаковые поля, то они автоматически будут приведены в вид: таблица_поле.
                Данная проверка осуществляется, если для таблицы явно не указан префикс ('prefix' => 'prefix_').

    Отладка:
        orm::result($result);                                          // Печать результатов выборки в удобочитаемом виде
        orm::debug();                                                  // Статистика проведённых до этого момента запросов
*/

/*    error
    
    Отключение отображения ошибок интерпретатора:
        error_reporting(0);
    
    Указание метода, которые будет вызван по окончании выполнения всего скрипта:
        register_shutdown_function(array('error', 'get_error'));
    
     Вывод ошибки системы:
        error::print_error('Error text');
*/

/*    message
    
    Вывод сообщения системы:
        message::print_message('Message text');
*/

/*    mod
    
    Просмотр readme модуля:
        Адрес: domen.com/mod/{modname}/
        В корне модуля должен лежать readme.md

    Инициализация модулей (require.php):
        mod::init(array('mod1', 'mod2', ..., 'modN'));
        
        Инициализация модуля:
            1) добавляет его стили и скрипты в единый объединённый файл
            2) обеспечивает автоподключение вызываемых классов модуля
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
            error::print_error(                                            // Иначе метод не существует
                '[' . $type . '] Route error: Function is undefined: '
                . $call[0] . '->' . $call[1]
            );
    }

    private static $routes_default = array(                                // Умолчания для системных маршрутов
        'type'    => 'GET',
        'asserts' => array(),
        'dev'     => false                                                 // Проводить маршрут всегда
    );
    
    public static $routes = array(                                         // Системные маршруты
        
        array(
            'url'      => '/module/{mod}/',
            'callback' => 'mod->readme',
            'dev'      => true                                             // Проводить маршрут только когда включен режим разработчика
        )
    );

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

        if(gettype($val) == 'array') {                                           // Если контекст нужно проитерировать
            
            if(
                !isset($val['array']) &&                                         // Если у текущего контекста не задан массив к перебору
                !empty(core::$ctx_last_array_element)                            // и существует последний элемент предыдущего контекста
            ) {
                $array = array(core::$ctx_last_array_element);                   // то текущий контекст нужно отпарсить в соответствии с текущим элементов контекста родителя
            }
            else                                                                 // Иначе массив задан в обычном виде массива
                $array = $val['array'];                                          // и нужно его сохранить в качестве родительского массива
            
            foreach($array as $i => $element) {                                  // Цикл по массиву значений к присваиванию

                if(
                    isset($val['if'])  &&                                        // Если задано положительное условие
                    (
                        gettype($element) == 'array' &&                          // и элемент является массивом
                        (
                            !isset($element[$val['if']])  ||                     // и переменная не существует
                                  !$element[$val['if']]                          // или она отрицательна
                        ) ||
                        gettype($element) == 'object' &&                         // или элемент является объектом
                        (
                            !isset($element->$val['if'])  ||                     // и переменная не существует
                                  !$element->$val['if']                          // или она отрицательна
                        )
                    ) ||
                    
                    isset($val['!if']) &&                                        // или задано отрицательное условие
                    (
                        gettype($element) == 'array' &&                          // и элемент является массивом
                        (
                            isset($element[$val['!if']]) &&                      // и существует переменная к проверке
                                  $element[$val['!if']]                          // и она положительна
                        ) ||
                        gettype($element) == 'object' &&                         // иkb элемент является объектом
                        (
                            isset($element->$val['!if']) &&                      // и существует переменная к проверке
                                  $element->$val['!if']                          // и она положительна
                        )
                    )
                )
                    break;                                                       // то выполнять парсинг не нужно
                
                core::$ctx_last_array_element = $element;                        // Сохранение последнего обработанного элемента массива контекста
                
                $tmp = array();                                                  // Временный массив для хранения сопоставленных значений текущей итерации
                
                if(isset($val['parse']))                                         // Если переменная parse задана
                    foreach($val['parse'] as $parse_key => $parse_val) {         // Цикл по массиву ключей: переменная_шаблона => ключ_массива_значений
                            
                        $tmp_val = 
                            (gettype($element) == 'object') ?                    // Если текущий элемент массива значений является объектом
                            $element->$parse_val :                               // требуется такой способ получения его значения
                            $element [$parse_val];                               // Иначе это массив и требуется иной способ получения значения

                        $tmp[$parse_key] = $tmp_val;                             // Добавление элемента с текущим значением во временный массив
                    }

                $tpl->block($ctx, $tmp);                                         // Парсинг текущей итерации

                foreach($val as $ctx2 => $arr)                                   // Цикл по элементам контекста
                    if(
                        !in_array($ctx2, core::$ctx_reserve) ||                  // Если ключ не является служебной переменной
                        !$ctx2                                                   // или ключа не существует
                    ) {                                                          // То это вложенный контекст
                        if(!$ctx2) {                                             // Если ключа не существует
                            $arr = $ctx . '/' . $arr;                            // то этот контекст нужно просто активировать
                        }
                        else if(
                            isset($arr['array']) &&                              // Если задана опция массива
                            gettype($arr['array']) == 'string'                   // в текстовом виде
                        ) {
                            core::$ctx_parent_array = $element;                  // Задание родительской таблицы в соответствии с текущим элементом
                            
                            if(isset(core::$ctx_parent_array[$arr['array']]))    // Если существует массив предыдущего контекста
                                $arr['array'] = core::$ctx_parent_array[$arr['array']];
                            else                                                 // Иначе не существует такого массива
                                continue;                                        // и нужно перейти к следующей итерации
                        }
                        
                        core::context($tpl, $ctx . '/' . $ctx2, $arr);           // Рекурсивный вызов вложенного контекста
                    }
            }
        }
        else                                                                     // Иначе контекст нужно просто активировать
            $tpl->iterate($val);                                                 // Активация контекста
    }

    private static $tenhtmlFolder = '/assets/__autogen__tenhtml';                       // Директория для хранения шаблонов, сгенерированных из tenhtml

    private static $spec = array(                                                       // Массив зарезервированных специальных символов
        'block'   => '%',                                                               // Блок
        'elemmod' => '.',                                                               // Элемент или модификатор
        'mix'     => '&'                                                                // Миксованное значение
    );

    /**
     * Генерирования шаблона из tenhtml
     *
     * @param  string $file Путь до tenhtml-шаблона
     * @return string       Путь до сгенерированного шаблона
     */
    private static function savetenhtml($file) {

        $symbols = 'a-z0-9_\-\/<>';                                                     // Обычные символы, из которых состоят ключи

        $tenhtml = preg_replace(                                                        // Заключение ключей в кавычки
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
                    ']+'                         .
                ')'                              .
                '(?=:\s+[\{|\[|\'|\"])'          .
            '/i',
            '"$1"', file_get_contents($file));

        $tenhtml = json_decode(                                                         // Декодирование в JSON-дерево
            '{' .                                                                       // Обрамление в фигурные скобики для валидного JSON
                str_replace(array('\'', "\n"), array('"', ''), $tenhtml) .              // Замена экранированных одинарных кавычек в двойные и удаление переносов строк
            '}');

        if(!$tenhtml) {                                                                 // Если не удалось получить JSON-дерево
            error::print_error('invalid tenhtml in ' . $file);
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
            error::print_error('Undefined block name');
        }

        $class = core::genClass($block, $keyInfo);                                      // Генерация атрибута class

        return core::parseContent(
            '<' .
                $keyInfo['tag'] .
                ((!empty($class)) ? ' class="' . $class . '"' : '') .
            '>',
            $content,
            $block
        ) . (                                                               // Если тег требуется закрыть
            (!in_array($keyInfo['tag'], core::$singleTags) && !$keyInfo['single']) ?
                '</' . $keyInfo['tag'] . '>' :
                ''
        );
    }

    /**
     * Рекурсивный парсинг контента
     *
     * @param  string              $inner   Строка, предваряющая парсингуемый контент
     * @param  string|object|array $content Содержимое блока
     * @param  string              $block   Имя контекстного блока
     * @return string                       Пропарсенный контент
     */
    private static function parseContent($inner, $content, $block) {

        switch(gettype($content)) {                                                     // Способ разбора зависит от типа данных

            case 'string':                                                              // Обычной строке надо только проставить переменные
                return $inner . core::setVar($content);

            case 'object':                                                              // По объекту нужно пробежаться
                foreach($content as $key => $content) {

                    $attributes = '';

                    switch($key) {                                                      // Способ разбора зависит от ключа объекта

                        case 'attr':                                                    // Объект атрибутов
                            foreach($content as $attr => $val) {
                                $attributes .= ' ' . $attr . '="' . core::setVar($val) . '"';       // Формирование строки атрибутов
                            }
                            $inner = substr_replace($inner, $attributes, strlen($inner) -1, 0);     // Вставка сформированной строки перед закрывающей скобкой
                            break;

                        case 'content':                                                 // Массив контента
                            $inner .= core::parseArray($content, $block);
                            break;

                        default:                                                        // Произвольный ключ (селектор)
                            $inner .= core::parsetenhtml($key, $content, $block);
                    }
                }
                return $inner;

            case 'array':                                                               // Нужно разобрать массив
                return $inner . core::parseArray($content, $block);
        }
    }

    private static $keywords = array(                                                   // Массив ключевых слов tenhtml
        'for',
        'doctype', 'html', 'head',
        'title', 'lang', 'charset', 'favicon',
        'ie', 'ie<8', 'ie<9',
        'body'
    );

    /**
     * Формирование массива с информацией по ключу tenhtml-шаблона
     *
     * @param  string $key Ключ tenhtml-шаблона
     * @return array       Массив информации по ключу
     */
    private static function parsetenhtmlKey($key) {

        $info = preg_split(                                                             // Разбор ключа на массив
            '/([' . implode('', core::$spec) . '])/',
            str_replace(' ', '', $key),                                                 // Удаление всех пробелов из ключа
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

        $keyword = $keyInfo['keyword'];

        if(empty($keyword)) {
            return false;
        }

        switch($keyword) {

            case 'for':                                                                 // Ключевое слово for
                return core::parseContent(
                    '{{ begin ' . $keyInfo['elemmod'][0] . ' }}',
                    $content,
                    $block
                ) .
                '{{ end }}';

            case 'doctype':
                break;
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
     * @return               Изменённая строка
     */
    private static function setVar($string) {
        $string = preg_replace('/{\s*([a-z0-9_\-]*)\s*}/i', '{{ $$1 }}', $string);      // Замена переменных
        return str_replace(array('\{', '\}'), array('{', '}'), $string);                // Замена экранированных фигурных скобок
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
                    $string .= core::setVar($elem);
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

        if(isset(orm::$mysqli))
            orm::$mysqli->close();                                         // Разрыв соединения с базой данных

        error::get_error();                                                // Обработка ошибок интерпретатора
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

// Класс работы с базой данных
class orm {
    
    public static $mysqli;                                                 // Объект работы с MySQL
    
    private static $queries = array();                                     // Массив данных о выполняемых запросах
    private static $parameters = null;                                     // Массив параметров текущей операции
    private static $object;                                                // Текущий объект
    
    /**
     * Конструктор для сохранения текущей операции
     *
     * @param string $operation Название текущей операции
     */
    private function __construct($operation = null) {
        
        orm::$queries[count(orm::$queries)]->name = $operation;
        
        orm::$limit      = null;                                           // Обнуление дополнительных переменных перед каждым новым запросом
        orm::$order      = null;
        orm::$group      = null;
        orm::$fields     = null;
        orm::$addfields  = null;
        orm::$subqueries = null;
        orm::$prefix     = null;
        
        orm::$single     = false;                                          // Выключение флага одиночной выборки
    }
    
    /**
     * Функция подключения к MySQL
     *
     * @param string $host     Имя хоста
     * @param string $login    Логин
     * @param string $password Пароль
     */
    public static function connect($host, $login, $password) {
        
        orm::$mysqli = new mysqli($host, $login, $password);
        orm::$mysqli->set_charset('utf8');
    }
    
    /**
     * Функция выбора базы данных
     *
     * @param string $db Имя базы данных
     */
    public static function db($db) {
        
        if(!orm::$mysqli->select_db($db))
            error::print_error('Selected database <b>' . $db . '</b> not found');
    }
    
    /**
     * Функция преобразования значений для использования в SQL-запросе
     *
     * @param mixed $val Значение для преобразования
     * @return mixed
     */
     private static function get_value($val) {
        
        $quote = '';

        if(strpos($val, 'func:') !== false) {                              // Если в значении присутствует ключевое слово, указывающее на функцию
            
            $val = str_replace('func:', '', $val);                         // Удаление ключевого слова из значения
            $val = str_replace(' ', '', $val);                             // Удаление пробелов из значения
        }
        else
            $quote = (
                gettype($val) == 'string'    &&                            // Если у значения строковый тип
                !preg_match('/^\d+$/', $val) &&                            // и это не число со строковым типом
                strtolower($val) != 'null'                                 // и это не null
            ) ? '\'' : '';                                                 // то надо добавить кавычки
        
        return $quote . $val . $quote;                                     // При необходимости возвращаемое значение обрамляется в апострофы
     }
    
    /**
     * Функция добавления записи в базу данных
     *
     * @param string $table  Имя таблицы
     * @param array  $values Массив со значениями
     * @return integer || boolean
     */
    public static function insert($table, $values) {
        
        orm::$parameters = array($table, $values);
        
        new orm(__FUNCTION__);
        
        orm::set_debug(debug_backtrace());
        
        $fields    = '';
        $variables = '';

        foreach($values as $key => $val) {
            
            $fields .= $key . ', ';
            $variables .=  orm::get_value($val) . ', ';
        }
        
        if(!orm::execute_query('insert into ' . $table . '(' . substr($fields, 0, -2) . ') values (' . substr($variables, 0, -2) . ')'))
            return false;                                                  // Запрос не выполнен и возвращается отрицательный результат
        
        return orm::$mysqli->insert_id;                                    // Возвращается последний добавленный идентификатор
    }
    
    /**
     * Функция обновления записи в базе данных
     *
     * @param string $table  Имя таблицы
     * @param array  $values Массив со значениями
     * @return object
     */
    public static function update($table, $values) {
        
        orm::$parameters = array($table, $values);
        
        orm::$object = new orm(__FUNCTION__);
        orm::set_debug(debug_backtrace());
        
        return orm::$object;
    }
    
    /**
     * Функция обработки данных перед отправкой на выполнение запроса на обновление записи
     *
     * @param string $table  Имя таблицы
     * @param array  $values Массив со значениями
     * @return boolean
     */
    private static function update_query($table, $values) {
        
        $variables = '';

        foreach($values as $key => $val)
            $variables .= $key . ' = ' . orm::get_value($val) . ', ';
        
        return orm::execute_query('update ' . $table . ' set ' . substr($variables, 0, -2) . orm::$where);
    }
    
    /**
     * Функция удаления записи из базы данных
     *
     * @param string $table Имя таблицы
     * @return object
     */
    public static function delete($table) {
        
        orm::$parameters = array($table);
        orm::$object = new orm(__FUNCTION__);
        orm::set_debug(debug_backtrace());
        
        return orm::$object;
    }
    
    /**
     * Функция вызова запроса на удаление записи
     *
     * @param string $table  Имя таблицы
     * @return boolean
     */
    private static function delete_query($table) {
        
        return orm::execute_query('delete from ' . $table . orm::$where);
    }
    
    /**
     * Функция выборки записей из базы данных
     *
     * @param string $table Имя таблицы
     * @return object
     */
    public static function select($table) {
        
        orm::$parameters = array($table);
        orm::$object = new orm(__FUNCTION__);
        orm::set_debug(debug_backtrace());
        
        return orm::$object;
    }
    
    private static $int_array = array(                                     // Массив типов данных базы данных, которые необходимо перевести в integer
        'tinyint' => 1, 'smallint'  => 2, 'integer' => 3, 
        'bigint'  => 8, 'mediumint' => 9, 'year'    => 13
    );
    
    private static $float_array = array(                                   // Массив типов данных базы данных, которые необходимо перевести в float
        'float' => 4, 'double' => 5
    );
    
    /**
     * Функция вызова запроса на выборку
     *
     * @param string $table  Имя таблицы
     * @return array || boolean
     */
    private static function select_query($table) {
        
        $select = (!is_null(orm::$fields)) ? orm::$fields : '*';
        
        return orm::modernize_selection(
            
            orm::execute_query(
                'select '         .
                $select           .
                orm::$addfields   .
                orm::$subqueries  .
                ' from ' . $table .
                orm::$where .
                orm::$group .
                orm::$order .
                orm::$limit
            )
        );
    }

    /**
     * Функция соединения таблиц базы данных
     * 
     * @param string $table Имя левой таблицы
     * @param array  $join  Массив массивов с описанием правых таблиц
     * @return object
     */
    public static function join($table, $join) {

        orm::$parameters = array($table, $join);
        orm::$object = new orm(__FUNCTION__);
        orm::set_debug(debug_backtrace());
        
        return orm::$object;
    }

    /**
     * Функция вызова join-запроса
     * 
     * @param string $table Имя левой таблицы
     * @param array  $join  Массив массивов с описанием правых таблиц
     * @return array || boolean
     */
    private static function join_query($table, $join = array()) {

        $exist_fields = array();                                                                    // Массив для хранения всех выбранных полей

        $fields = orm::execute_query('show columns from ' . $table);                                // Запрос на получение списка полей левой таблицы
        
        while($field = $fields->fetch_object())                                                     // Цикл по полученному списку полей левой таблицы
            array_push($exist_fields, $field->Field);                                               // Добавление поля в массив полей

        $joins  = '';                                                                               // Строка для конкатенации подключения таблиц

        $select = (!is_null(orm::$fields)) ? orm::$fields : $table . '.*';                          // Если задано значение для select, то используется оно, иначе все поля левой таблицы

        foreach($join as $tab) {                                                                    // Цикл по массивам с описанием правых таблиц
            
            $type = (!isset($tab['join'])) ? 'inner' : $tab['join'];                                // Если не задан тип join, то по умолчанию устанавливается inner
            
            if(isset($tab['right']))                                                                // Если задано правое направление связи
                $on = $tab['table'] . '.' . $tab['table'] . '_id = ' . $tab['right'] . '.' . $tab['table'] . '_fk';
            
            else {                                                                                  // Иначе правое направление связи не задано
                
                $left = (isset($tab['left'])) ? $tab['left'] : $table;                              // Если явно задано левое направление связи, то нужно использовать указанную в направлении таблицу, иначе использовать левую таблицу
                
                $on = $tab['table'] . '.' . $left . '_fk = ' . $left . '.' . $left . '_id';
            }

            $on .= (isset($tab['on'])) ? ' and ' . $tab['on'] : '';                                 // Добавление опции on

            $joins .= ' ' . $type . ' join ' . $tab['table'] . ' on ' . $on;                        // Конкатенация полной строки подключения таблицы

            if(!is_null(orm::$fields))                                                              // Если задано значение для select
                continue;                                                                           // то нужно пропустить последующие операции и перейти к следующей правой таблице

            $fields = orm::execute_query('show columns from ' . $tab['table']);                     // Запрос на получение списка полей текущей правой таблицы

            if(isset($tab['prefix']))                                                               // Если задан префикс для текущей правой таблицы
                while($field = $fields->fetch_object())                                             // Цикл по полученному списку полей правой таблицы
                    $select .= ', ' . $tab['table'] . '.' . $field->Field . ' as ' . $tab['prefix'] . $field->Field;
            
            else                                                                                    // Иначе префикс для текущей правой таблицы не задан
                while($field = $fields->fetch_object()) {                                           // Цикл по полученному списку полей правой таблицы

                    $select .= ', ' . $tab['table'] . '.' . $field->Field;

                    if(in_array($field->Field, $exist_fields)) {                                    // Если поле, с именем текущего уже было в одной из предыдущих таблиц
                        
                        $field->Field = $tab['table'] . '_' . $field->Field;                        // Значит этому полю нужно добавить табличный префикс

                        $select .= ' as ' . $field->Field;                                          // и в запросе указать его в качестве as
                    }

                    array_push($exist_fields, $field->Field);                                       // Добавление текущего поля в массив хранения всех полей
                }
        }

        return orm::modernize_selection(
            
            orm::execute_query(
                'select '         .
                $select           .
                orm::$addfields   .
                orm::$subqueries  .
                ' from ' . $table .
                $joins            .
                orm::$where .
                orm::$group .
                orm::$order .
                orm::$limit
            )
        );
    }

    /**
     * Функция обработки результатов выборки
     * 
     * @param array $result Результат выборки
     * @return array || boolean
     */
    private static function modernize_selection($result) {

        if(!$result)                                                                                // Если запрос не был выполнен
            return false;
        
        else {                                                                                      // Иначе запрос был успешно выполнен
            
            $result_array = array();                                                                // Результирующий массив
            
            while($current_row = $result->fetch_object()) {                                         // Цикл по строкам результатов выборки
                
                foreach($result->fetch_fields() as $val) {                                          // Цикл по полям текущей строки
                    
                    $name = $val->name;                                                             // Имя текущего поля
                    
                    if(!is_null(orm::$prefix)) {                                                    // Если требуется добавить префикс
                        
                        $prefix = str_replace('{table}', $val->table, orm::$prefix);

                        $prefix_name = $prefix . $name;                                             // Формирование нового имени для поля
                        $current_row->$prefix_name = $current_row->$name;                           // Присваивание значения из старого свойства объекта свойству с новым именем
                        unset($current_row->$name);                                                 // Удаление свойства со старым именем
                        $name = $prefix_name;                                                       // Замена основного имени на новое с префиксом
                    }
                    
                    if(in_array($val->type, orm::$int_array))                                       // Если тип данных текущего поля является числовым и целым
                        $current_row->$name = intval($current_row->$name);                          // то это поле надо перевести в целое число
                    
                    else if(in_array($val->type, orm::$float_array))                                // Если тип данных текущего поля является числовым и дробным
                        $current_row->$name = floatval($current_row->$name);                        // то это поле надо перевести в дробное число
                }
                
                array_push($result_array, $current_row);                                            // Запись строки в результирующий массив
            }
            
            if(orm::$single)                                                                        // Если нужно выбрать одну строку
                return $result_array[0];                                                            // то нужно вернуть именно её
            else
                return $result_array;                                                               // иначе массив записей
        }
    }
    
    private static $where;                                                                          // Переменная, хранящая переданные условия
    private static $single = false;                                                                 // Флаг выборки одной строки
    
    /**
     * Функция условия
     *
     * @param string || integer $where Текст условия
     * @return mixed
     */
    public function where($where) {
        
        $query_name = orm::$queries[count(orm::$queries) - 1]->name;                                // Имя текущей операции

        if(!$where)                                                                                 // Если аргумент отсутствует
            error::print_error('Missing argument for <b>where</b> in <b>' . $query_name . '</b> query');
        
        else if(gettype($where) == 'integer' || preg_match('/^\d+$/', $where)) {                    // иначе если аргумент имеется и это целое число или это строка, являющаяся числом
            
            if($query_name == 'select')                                                             // Если выполняется select
                orm::$single = true;                                                                // нужно отметить, что к выборке требуется одна строка
            
            orm::$where = ' where ' . orm::$parameters[0] . '_id = ' . $where;
        }
        
        else if(gettype($where) == 'string')                                                        // иначе если аргумент имеется и это строка
            orm::$where = ($where == 'all' || $where == '*') ? '' : ' where ' . $where;             // Если запрос выполняется для всех записей, то условие не нужно
        
        else                                                                                        // Иначе аргумент имеется, но у него неверный тип данных
            error::print_error('Wrong argument for <b>where</b> in <b>' . $query_name . '</b> query');
        
        return call_user_func_array(
            array('orm', $query_name . '_query'),
            orm::$parameters
        );
    }
    
    /**
     * Функция присоединения таблиц с использованием простых sql-запросов и их объединение в php
     *
     * @param array  $table Массив массивов выборок
     * @return array
     */
    /*
    public static function inner($tables) {
        
        $index = 0;                                                // Итератор для главного цикла
        
        foreach($tables as $prefix => $tab) {                    // Цикл по выборкам
            
            if(gettype($tab) == 'object')                        // Если таблица является объектом
                $table[0] = $tab;                                // то этот объект надо сделать первым элементом массива
            else if(gettype($tab) == 'array')                    // Иначе если это массив
                $table = $tab;                                    // и таблицу надо просто переприсовить
            
            if($index > 0) {                                    // Если сейчас не первая таблица
                
                if(gettype($tab) == 'string')                            // Если текущая таблица - строка
                    $table = orm::select($tab)->limit(1)->where('all');    // надо сделать выборку одной строки, чтобы затем выявить поля-ключи
                
                $right_table_fk = array();                                // Массив внешних ключей правой таблицы
                $right_table_id = array();                                // Массив первичных ключей правой таблицы
                
                if(isset($table[0]))                                    // Если у текущей таблицы есть хотя бы одна строка выборки
                    foreach($table[0] as $field => $value)                // Цикл по первой строке текущей таблицы
                        if(substr($field, -3) == '_fk') {                // Если текущее поле является внешним ключём
                            $fk_name = explode('_fk', $field);
                            array_push($right_table_fk, $fk_name[0]);    // то его надо добавить в массив внешних ключей
                        }
                        else if(substr($field, -3) == '_id') {            // Иначе если текущее поле является первичным ключём
                            $id_name = explode('_id', $field);
                            array_push($right_table_id, $id_name[0]);    // то его надо добавить в массив первичных ключей
                        }
                
                $tables_key = null;
                
                foreach($right_table_fk as $fk)                        // Цикл по внешним ключам правой таблицы
                    foreach($left_table_id as $id)                    // Цикл по первичным ключам левой таблицы
                        if($fk == $id) {                            // Если ключи совпадают
                            
                            $relation = 'left';                        // Связь направлена влево
                            $tables_key = $fk;                        // то по этому ключу будут объединяться строки
                        }
                
                if(is_null($tables_key))                            // Если соответствие ключей не было найдено
                    foreach($right_table_id as $id)                    // Цикл по первичным ключам правой таблицы
                        foreach($left_table_fk as $fk)                // Цикл по внешним ключам левой таблицы
                            if($fk == $id) {                        // Если ключи совпадают
                                
                                $relation = 'right';                // Связь направлена вправо
                                $tables_key = $fk;                    // то по этому ключу будут объединяться строки
                            }
                
                if(!is_null($tables_key)) {                            // Если соответствие ключей найдено
                    
                    $tables_key_id = $tables_key . '_id';                            // Имя поля первичного ключа
                    $tables_key_fk = $tables_key . '_fk';                            // Имя поля внешнего ключа
                    $result = array();                                                // Результирующий массив
                    
                    if(gettype($tab) == 'string') {                                    // Если текущая таблица - строка
                        
                        $table = array();                                            // Массив формируемой таблицы
                        $exist = array();                                            // Массив первичных ключей, которые уже добавлены
                        
                        foreach($left_table as $left_row) {                            // Цикл по строкам левой таблицы
                            
                            if($relation == 'left')
                                $where = $tables_key_fk . ' = ' . $left_row->$tables_key_id;
                            else if($relation == 'right')
                                $where = $tables_key_id . ' = ' . $left_row->$tables_key_fk;
                            
                            $right_rows = orm::select($tab)->where($where);            // Запрос к текущей таблице в соответствии с найденными ключами
                            
                            foreach($right_rows as $row) {                            // Цикл по полученным в результате запроса строкам
                                
                                if($relation == 'right') {                            // Если связь направлена вправо
                                    
                                    if(!in_array($row->$tables_key_id, $exist)) {    // Если в массиве ещё нет текущего первичного ключа
                                        array_push($exist, $row->$tables_key_id);    // Добавление нового ключа в массив первичных ключей
                                        array_push($table, $row);                    // Добавление полученных строк в текущую таблицу
                                    }
                                }
                                else if($relation == 'left') {                        // Иначе если связь направлена влево
                                    
                                    $right_table_key = $right_table_id[0] . '_id';    // У правой таблицы, переданной в виде строки есть только один первичный ключ
                                    
                                    if(!in_array($row->$right_table_key, $exist)) {    // Если в массиве ещё нет текущего первичного ключа
                                        array_push($exist, $row->$right_table_key);    // Добавление нового ключа в массив первичных ключей
                                        array_push($table, $row);                    // Добавление полученных строк в текущую таблицу
                                    }
                                }
                            }
                        }
                    }
                    
                    if($relation == 'right')                                        // Если связь направлена вправо
                        list($left_table, $table) = array($table, $left_table);        // то нужно поменять местами таблицы для их дальнейшего объединения
                    
                    foreach($left_table as $left_row => $left_row_values) {            // Цикл по строкам левой таблицы
                        
                        foreach($table as $row => $row_values) {                    // Цикл по строкам правой таблицы
                            
                            if($left_row_values->$tables_key_id == $row_values->$tables_key_fk) {    // Если значения ключей совпадают
                                
                                // array_push($result, (object) array_merge((array) $left_row_values, (array) $row_values)); // Слияние объектов
                                
                                if(gettype($prefix) == 'string' && $relation == 'right') {            // Если передан префикс и связь направлена вправо
                                    
                                    $tmp = new stdClass;
                                    
                                    foreach($left_row_values as $tmp_field => $tmp_value) {            // Цикл по полям текущей строки левой таблицы
                                        
                                        if(substr($tmp_field, -3) != '_fk' && substr($tmp_field, -3) != '_id') {    // Если текущее поле не является ключом
                                            
                                            $tmp_new_field = $prefix . $tmp_field;                    // Формирование нового имени для поля с учётом префикса
                                            $tmp->$tmp_new_field = $tmp_value;                        // Добавление нового свойства с прежним значением
                                        }
                                        else                                                        // Иначе текущее поле является ключом
                                            $tmp->$tmp_field = $tmp_value;                            // и его нужно просто переприсвоить
                                    }
                                }
                                else                                                                // Иначе префикс не передан или связь направлена влево
                                    $tmp = clone $left_row_values;
                                
                                foreach($row_values as $field => $value) {                    // Цикл по полям правой таблицы
                                    
                                    if(substr($field, -3) != '_fk' && substr($field, -3) != '_id') {
                                    
                                        if(gettype($prefix) == 'string' && $relation == 'left')    // Если указан префикс и связь направлена влево
                                            $field = $prefix . $field;
                                        else if(property_exists($tmp, $field)) {                // Иначе префикс не указан и если поле с таким названием уже есть
                                            
                                            // if($relation == 'right') {                        // Если связь направлена вправо
                                                
                                                $left_field = $tables_key . '_' . $field;    // Новое название для поля
                                                $tmp->$left_field = $tmp->$field;            // Присваивание значения полю с новым названием
                                            // }
                                            // else if($relation == 'left')                    // Иначе если связь направлена влево
                                                // $field = $tables_key . '_' . $field;        // Нужно просто задать новое название для поля
                                        }
                                    }
                                    
                                    $tmp->$field = $value;                                    // Присваивание объекту левой таблицы значений свойств правой таблицы
                                }
                                
                                array_push($result, $tmp);
                                
                                // Надо не очищать $result, а добавлять в него строки, тогда не придётся
                                // передавать его значение в $left_table
                                // array_splice($result, $left_row, 0, $tmp);
                            }
                        }
                    }
                }
                else if(count($left_table) > 0 && count($table) > 0) {                        // Иначе связи не обнаружены и если у обоих таблиц есть хотя бы одна запись
                    
                    $debug_info = debug_backtrace();
                    error::print_error('<b>inner</b> can\'t found conformity keys in <b>' . $debug_info[0]['file'] . '</b> on line <b>' . $debug_info[0]['line'] . '</b>');
                }
                
                $left_table = $result;                                // Массив левой таблицы - это результат слияния таблиц
            }
            else {                                                    // Иначе сейчас первая таблица
                
                if(gettype($tab) == 'string')                        // Если переданная таблица - строка
                    $table = orm::select($tab)->where('all');        // надо сделать выборку в ручную
                
                if(gettype($prefix) == 'string') {                    // Если требуется добавить префикс
                    
                    $left_table = array();
                    
                    foreach($table as $row => $row_values) {            // Цикл по строкам первой таблицы
                        
                        $left_table_row = new stdClass;
                        
                        foreach($row_values as $field => $value) {        // Цикл по полям текущей строки первой таблицы
                        
                            if(substr($field, -3) != '_fk' && substr($field, -3) != '_id') {    // Если текущее поле не является ключом
                                
                                $new_field = $prefix . $field;            // Формирование нового имени для поля с учётом префикса
                                $left_table_row->$new_field = $value;    // Добавление нового свойства с прежним значением
                            }
                            else                                        // Иначе текущее поле является ключом
                                $left_table_row->$field = $value;        // и его нужно просто переприсвоить
                        }
                        
                        array_push($left_table, $left_table_row);        // Добавление сформированной строки в новый массив первой таблицы
                    }
                }
                else
                    $left_table = $table;                            // Иначе, если префикс не требуентся, массив левой таблицы - это первая таблица (слияний пока не было)
            }
            
            $left_table_id = array();                                // Массив первичных ключей левой таблицы
            $left_table_fk = array();                                // Массив внешних ключей левой таблицы
            
            if(isset($left_table[0]))                                // Если у текущей таблицы есть хотя бы одна строка выборки
                foreach($left_table[0] as $field => $value) {        // Цикл по первой строке текущей таблицы

                    if(substr($field, -3) == '_id') {                // Если текущее поле является первичным ключём
                        
                        $id_name = explode('_id', $field);
                        array_push($left_table_id, $id_name[0]);    // то его надо добавить в массив первичных ключей
                    }
                    else if(substr($field, -3) == '_fk') {            // Иначе если текущее поле является внешним ключём
                        
                        $fk_name = explode('_fk', $field);
                        array_push($left_table_fk, $fk_name[0]);    // то его надо добавить в массив внешних ключей
                    }
                }
            
            $index++;
        }
        
        return $result;
    }
    */
    
    private static $limit;                                             // Переменная, хранящая значение для оператора limit
    
    /**
     * Функция добавления значения для оператора limit к запросу
     *
     * @param string $limit Значение оператора
     */
    public function limit($limit) {
        
        orm::$limit = ' limit ' . $limit;
        
        return orm::$object;
    }

    private static $fields;                                            // Переменная, хранящая значение для оператора select

    /**
     * Функция изменения полей в select запроса
     *
     * @param string $fields Перечисление полей
     */
    public function fields($fields) {
        
        orm::$fields = $fields;
        
        return orm::$object;
    }

    private static $addfields;                                         // Переменная, хранящая дополнительное значение для оператора select

    /**
     * Функция добавления полей в select запроса
     *
     * @param string $addfields Перечисление полей
     */
    public function addfields($addfields) {
        
        orm::$addfields = ', ' . $addfields;
        
        return orm::$object;
    }
    
    private static $order;                                             // Переменная, хранящая значение для оператора order
    
    /**
     * Функция добавления значения для оператора order к запросу
     *
     * @param string $order Значение оператора
     */
    public function order($order) {
        
        orm::$order = ' order by ' . $order;
        
        return orm::$object;
    }
    
    private static $group;                                             // Переменная, хранящая значение для оператора group
    
    /**
     * Функция добавления значения для оператора group к запросу
     *
     * @param string $group Значение оператора
     */
    public function group($group) {
        
        orm::$group = ' group by ' . $group;
        
        return orm::$object;
    }
    
    private static $subqueries;                                        // Переменная, хранящая подзапросы
    
    /**
     * Функция добавления подзапросов
     *
     * @param array $subqueries Массив с текстами подзапросов
     */
    public function sub($subqueries) {
        
        foreach($subqueries as $val => $key)
            orm::$subqueries .= ', (' . $val . ') as ' . $key;
        
        return orm::$object;
    }
    
    private static $prefix;                                            // Переменная, хранящая значение префикса
    
    /**
     * Функция добавления значения префикса для полей таблицы
     *
     * @param string $prefix Значение для префикса
     */
    public function prefix($prefix) {
        
        orm::$prefix = $prefix;
        
        return orm::$object;
    }

    private static $selection_operation = array('select', 'join');                                          // Операции, выполняющие выборку данных
    
    /**
     * Функция непосредственного выполнения запроса
     *
     * @param string $query SQL-запрос
     * @return boolean
     */
    private static function execute_query($query) {
        
        orm::$queries[count(orm::$queries) - 1]->query = $query;                                            // Запись в массив данных текста текущего запроса
        
        $start = microtime(true);                                                                           // Время начала выполнения запроса
        $result = orm::$mysqli->query($query);                                                              // Выполнение самого запроса
        orm::$queries[count(orm::$queries) - 1]                                                             // Запись в массив данных
            ->duration = microtime(true) - $start;                                                          // длительности выполнения запроса
        
        if(!$result) {                                                                                      // Если запрос не был выполнен
            
            orm::$queries[count(orm::$queries) - 1]->result = '<b>error:</b> ' . orm::$mysqli->error;       // Запись ошибки в результат выполнения запроса
            return false;                                                                                   // и возвращение отрицательного результата
        }
        else if(in_array(orm::$queries[count(orm::$queries) - 1]->name, orm::$selection_operation)) {       // Иначе запрос был выполнен и если текущая операция относится к операциям, выполняющим выборку данных
            
            orm::$queries[count(orm::$queries) - 1]->result = 'complete: ' . $result->num_rows . ' rows';   // Запись количества выбранных строк в результат выполнения запроса
            return $result;                                                                                 // и возвращение результата выборки
        }
        else {                                                                                              // Иначе запрос успешно выполнен, но текущая операция не относится к выполняющим выборку
            
            orm::$queries[count(orm::$queries) - 1]->result = 'complete';                                   // Запись сообщения об успешном выполнении в качестве результата
            return true;                                                                                    // и возвращение положительного результата
        }
    }
    
    /**
     * Функция добавления информации по выполняемым запросам
     *
     */
    private static function set_debug($backtrace) {
        
        orm::$queries[count(orm::$queries) - 1]->file = $backtrace[0]['file'];                              // Запись в массив данных пути к файлу
        orm::$queries[count(orm::$queries) - 1]->line = $backtrace[0]['line'];                              // и строки, откуда был вызван запрос
    }
    
    /**
     * Функция вывода информации по отработанным запросам
     *
     */
    public static function debug() {
        
        echo "<pre><b>Queries debuger:</b>\n\n";
        
        $duration_sum = 0;
        
        foreach(orm::$queries as $key => $val) {
            
            echo $key + 1 . " -> " . $val->name . " [\n"
                . "\t"   . "file     -> " . $val->file
                . "\n\t" . "line     -> " . $val->line
                . "\n\t" . "query    -> " . $val->query
                . "\n\t" . "duration -> " . $val->duration
                . "\n\t" . "result   -> " . $val->result
                . "\n]\n\n";
            
            $duration_sum += $val->duration;
        }
        
        echo "total [\n"
            . "\t" .   "count    -> " . count(orm::$queries)
            . "\n\t" . "duration -> " . $duration_sum
            . "\n]</pre>";
    }
    
    /**
     * Функция вывода массива выборки в удобочитаемом виде
     *
     * @param array || object $query
     */
    public static function result($query) {
        
        if(gettype($query) == 'object')                                // Если параметр является объектом (одна строка в результате выборки)
            $table[0] = $query;                                        // то надо добавить его в массив
        else                                                           // Иначе это массив
            $table = $query;                                           // и его надо просто переприсвоить
        
        echo "<pre><b>Query result: </b>";
        
        if(count($table) == 0)                                         // Если выборка пуста
            echo "empty\n\n";
        else {                                                         // Если есть результаты выборки
            
            echo "\n\n";
            
            foreach($table as $num => $row) {                          // Цикл по строкам результата выборки
                
                echo $num + 1 . " -> [\n";
                
                foreach($row as $key => $val)                          // Цикл по полям текущей строки
                    echo "\t" . $key . " => " . $val . "\n";
                
                echo "]\n\n";
            }
        }
        
        echo "</pre>";
    }
}

// Класс обработки ошибок
class error {
    
    protected static $sys_classes = array(                             // Определение классов системы, имена которых нельзя использовать в приложении
        'core', 'get', 'orm', 'error', 'message', 'mods'
    );
    
    /**
     * Функция обработки ошибок интерпретатора
     * 
     */
    public static function get_error() {
        
        $info = error_get_last();                                      // Получение массива с информацией о последней ошибке в таком формате: Array([type] => 1 [message] => Message text [file] => Path to file [line] => 1 ) 
        
        switch($info['type']) {
            
            case 1:                                                    // Если ошибка является фатальной
                
                if(stripos($info['message'], 
                    'Call to undefined method') === 0) {               // Если это ошибка вызова неизвестного метода
                    
                    if(preg_match('|Call to undefined method (.*)::|', $info['message'], $match)) {
                        
                        foreach(error::$sys_classes as $class)
                            if($class == $match[1]) {                  // Если имя вызываемого класса совпадает хотя бы с одним из системных классов
                                
                                echo error::print_error('Called class-name (<b>' . $match[1] . '</b>) is used in system. Other reserved system class-name: ');
                                
                                foreach(error::$sys_classes as $class)
                                    echo '<b>' . $class . '</b>; ';
                                
                                break;
                            }
                    }
                }
                
                break;
        }
    }
    
    /**
     * Функция печати ошибок системы
     *
     * @param string $text Текст ошибки
     */
    public static function print_error($text) {
        
        die('<br><b>Framework error</b>: ' . $text);
    }
}

// Класс вывода сообщений
class message {
    
    /**
     * Функция печати сообщений системы
     *
     * @param string $text Текст сообщения
     */
    public static function print_message($text) {
        
        echo '<br><b>Framework message</b>: ' . $text;
    }
}

// Класс работы с модулями фреймворка
class mod {

    /**
     * Функция инициализации модулей
     * 
     * @param array $mods Массив имён модулей
     */
    public static function init($mods) {

        foreach($mods as $mod) {                                       // Цикл по перечисленным именам модулей
            
            $path = '/mod/' . $mod;                                    // Относительный путь к модулю

            array_push(ten_file::$input_path, $path . '/view/');       // Добавление пути к представлениям модуля для объединения файлов
            
            array_push(                                                // Добавление путей для автоподключения файлов модуля
                core::$paths,
                ROOT . $path . '/app/controller/',
                ROOT . $path . '/app/model/'
            );

            require ROOT . $path . '/init.php';                        // Подключение файла инициализации модуля
        }
    }

    /**
     * Функция отображения readme модулей
     *
     * @param string $mod Имя модуля
     */
    public static function readme($mod) {

        require ROOT . '/assets/php/markdown.php';

        echo core::block(array(
                
            'block' => 'html',

            'parse' => array(
                
                'title' => 'Модуль — ' . $mod,
                'files' => core::includes('markdown', '__autogen__'),
                'body'  => Markdown(file_get_contents(ROOT . '/mod/' . $mod . '/readme.md'))
            )
        ));
    }
}