<?php

/**
 * Работа с blitz-шаблонами
 * @version 0.0.1
 */

/* Использование

    Парсинг blitz-шаблонов:
        echo ten\tpl::block(array(                                     // Функция всегда принимает в качестве параметра массив

            'mod'   => 'modulename',                                   // Имя модуля. Если шаблон находится в модуле
            'block' => 'blockname',                                    // Обязательный. Имя блока
            'view'  => 'viewname',                                     // Имя шаблона. (По умолчанию: имя блока)

            'parse' => array(                                          // Массив парсинга
                'tplvar1' => 'val',                                    // Имя_переменной_в_шаблоне => значение
                'tplvar2' => ten\tpl::block(array(...))                // В качестве значения может быть другой блок. Вложенность не ограничена
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

    Вывод страницы 404:
        ten\tpl::not_found(array(
            'title'   => 'title',                                      // По умолчанию: "Страница не найдена"
            'header'  => 'header',                                     // По умолчанию: "Страница не найдена"
            'content' => 'content'                                     // По умолчанию: ""
        ));
*/

namespace ten;

class tpl extends core {

    public static $compressTplFolder;                                                   // Директория для хранения сжатых шаблонов

    /**
     * Установка директории для сжатых шаблонов
     *
     * @param $folder Директория для хранения сгенерированных сжатых шаблонов
     */
    public static function setCompressTplFolder($folder) {

        if(is_string($folder)) {
            self::$compressTplFolder = $folder;
        }
        else {
            self::$compressTplFolder = '/assets/' . GEN . 'compressed/';
        }
    }

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

            if($ext == 'tenhtml' && self::$settings['tenhtml']) {                       // Если рассматриваемое расширение tenhtml и включена его настройка

                $file = (DEV) ?                                                         // Если включен режим разработчика
                    html::savetenhtml(core::resolve_path($file)) :                      // то его нужно преобразовать в простой шаблон
                    ROOT . html::$tenhtmlFolder . text::ldel($file, ROOT);              // иначе просто взять уже сгенерированный шаблон

                if(file_exists($file)) {                                                // Если этот уже сгенерированный простой шаблон существует
                    break;                                                              // то рассматривать менее приоритетные расширения не нужно
                }
            }
        }

        if(self::$settings['compressHTML'] && $ext != 'tenhtml') {                      // Если HTML нужно сжимать

            if(DEV) {                                                                   // Если включен режим разработчика
                file::autogen(                                                          // Сохранение сжатого шаблона
                    self::$compressTplFolder . text::ldel($file, ROOT),
                    self::compressHTML(file_get_contents($file)),
                    false
                );
            }

            $blocks = (isset($mod)) ?
                ROOT . self::$compressTplFolder . '/mod/' . $mod . '/view/blocks/' :
                ROOT . self::$compressTplFolder . '/view/blocks/';

            $compressedFile = $blocks . $block . '/view/' . $view . '.tpl';             // Полный путь к сжатому шаблону

            if(file_exists($compressedFile)) {
                $file = $compressedFile;
            }
        }

        $tpl = new \Blitz($file);                                                       // Получение шаблона

        if(isset($context))                                                             // Если требуется контекст begin-end
            foreach($context as $ctx => $val)                                           // Цикл по контекстам
                self::context($tpl, $ctx, $val);

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
                !empty(self::$ctx_last_array_element)                                   // и существует последний элемент предыдущего контекста
            ) {
                self::iterateContextArray(
                    $tpl, $ctx, $val,
                    array(self::$ctx_last_array_element)                                // то текущий контекст нужно отпарсить в соответствии с текущим элементов контекста родителя
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

            self::$ctx_last_array_element = $element;                                   // Сохранение последнего обработанного элемента массива контекста

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

    public static $default_404_options = array(                                         // Дефолтные параметры для ненайденной страницы
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
            route::$called             &&                                               // Если маршрут был проведён
            isset($options['sysauto']) &&                                               // и функция вызывается автоматически с главной страницы после всех роутов
            $options['sysauto']
        )
            return false;                                                               // то страница найдена и ошибка 404 не нужна

        header('HTTP/1.1 404 Not Found');

        foreach(self::$default_404_options as $key => $val)                             // Установка значений по умолчанию
            if(!isset($options[$key]))                                                  // для незаданных опций
                $options[$key] = $val;

        die(self::block(array(
            'block' => 'html',
            'view'  => '404',
            'parse' => array(
                'title'   => $options['title'],
                'header'  => $options['header'],
                'content' => $options['content']
            )
        )));
    }
}