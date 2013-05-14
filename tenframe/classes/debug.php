<?php

/**
 * Отладка
 * @version 0.0.1
 */

/* Использование

    Вывод заданной информации:
        ten\debug::show(array(
            'h1' => 'Header 1',                                         // Заголовок первого уровня
            'h2' => 'Header 2',                                         // Заголовок второго уровня
            'p'  => 'Paragraph',                                        // Обычный текст
            'list' => array(),                                          // Вывод массива списком (допускаются многоуровневые массивы)
            array(                                                      // В массив можно обернуть другие данные
                'h2' => 'Header 2.1',                                   // чтобы ключи не переприсваивались
                'list' => array()
            ),
            array(
                'h2' => 'Header 2.2',
                'list' => array()
            )
        ));

    Вывод отладочной информации:
        ten\debug::init();                                              // Вывести всю отладочную информацию

        ten\debug::init(                                                // Вывести конкретную отладочную информацию
            'autogen',                                                  // Все автоматически сгенерированные файлы
            'statical',                                                 // Сгенерированные подключения статических файлов
            'join',                                                     // Объединённые файлы
            'tenhtml',                                                  // Шаблоны, сгенерированные из tenhtml
            'tpl',                                                      // Шаблоны, использованные для формирования страницы
            'orm'                                                       // Выполненные SQL-запросы
        );
*/

namespace ten;

class debug extends core {

    /**
     * Вывести отладочную информацию
     *
     * @param array $info Массив с информацией к выводу
     */
    public static function show($info) {

        $viewer = new viewer(
            'tenframe-debug',                                           // Имя блока отладочной информации
            array(                                                      // Стили блока
                'padding' => '10px',
                'background' => '#F5F5EA'
            ),
            array(                                                      // Стили элементов блока
                'h1' => array(
                    'font-size' => '20px',
                    'font-weight' => 'bold',
                    'margin' => '6px 0'
                ),
                'h2' => array(
                    'font-size' => '16px',
                    'font-weight' => 'bold',
                    'margin' => '6px 0 4px'
                ),
                'p' => array(
                    'font-size' => '12px',
                    'margin' => '2px 0'
                ),
                'list' => array(
                    'font-size' => '12px',
                    'margin' => '0'
                )
            )
        );

        $text = '';

        foreach($info as $key => $val) {                                // Цикл по информационному массиву
            if(is_string($val)) {                                       // Если значение является строкой
                $text .= self::print_keys($viewer, $key, $val);         // Можно просто вывести этот ключ
            } else if(is_array($val)) {                                 // Иначе если значением элемента является массив
                foreach($val as $subKey => $subVal) {                   // Цикл по элементам этого подмассива
                    $text .= self::print_keys(                          // Вывод информации по каждому ключу
                        $viewer, $subKey, $subVal
                    );
                }
            }
        }

        echo
            $viewer->style() .                                          // Печать стилей
            $viewer->tag('pre', $text);                                 // Печать блока
    }

    /**
     * Вывод отладочной информации из настроек
     *
     * @param bool | array $options Настройка вывода
     */
    public static function init($options = true) {

        $info = array(
            'autogen' => array(
                'h2' => 'Autogen:',
                'p' => 'Все автоматически сгенерированные файлы.',
                'list' => file::$debugAutogen
            ),
            'statical' => array(
                'h2' => 'Statical:',
                'p' => 'Сгенерированные подключения статических файлов.',
                'list' => statical::$debugStatical
            ),
            'join' => array(
                'h2' => 'Join:',
                'p' => 'Объединённые файлы.',
                'list' => join::$debugJoin
            ),
            'tenhtml' => array(
                'h2' => 'Tenhtml:',
                'p' => 'Шаблоны, сгенерированные из tenhtml.',
                'list' => html::$debugTemplates,
            ),
            'tpl' => array(
                'h2' => 'Templates:',
                'p' => 'Шаблоны, использованные для формирования страницы.',
                'list' => tpl::$debugTemplates,
            ),
            'orm' => array(
                'h2' => 'ORM:',
                'p' => 'Выполненные SQL-запросы.',
                'p' => orm::debug(),
            )

        );

        $toShow = array(
            'h1' => 'Tenframe debugger',
        );

        if(is_array($options)) {
            foreach($options as $item) {
                array_push($toShow, $info[$item]);
            }
        } else {
            $toShow = array_merge($toShow, $info);
        }

        self::show($toShow);
    }

    /**
     * Вывод значений ключей массива отладочной информации
     *
     * @param  viewer $viewer Объект viewer
     * @param  string $key    Ключ массива
     * @param  mixed  $val    Значение массива
     * @return string         Сформированная строка
     */
    private static function print_keys($viewer, $key, $val) {
        switch($key) {
            case 'h1':
            case 'h2':
            case 'p':
                return $viewer->tag('p', $val, $key);
            case 'list':
                return $viewer->tag('p', self::print_array($val, 0), $key);
        }
    }

    /**
     * Формирование строки к выводу из массива
     *
     * @param  array  $array Массив к выводу
     * @param  number $level Уровень вложенности
     * @return string        Сформированная строка
     */
    private static function print_array($array, $level) {

        if(!count($array)) {                                            // Если массив пустой
            return 'NULL' . "\n";
        }

        $longest = max(array_map('strlen', array_keys($array)));        // Количество разрядов последнего элемента массива
        $text = '';                                                     // Строка для вывода
        $prev = null;                                                   // Предыдущий элемент
        $repet = 0;                                                     // Количество повторов, идущих подряд
        foreach($array as $key => $val) {                               // Перебор всех элементов переданного в ключе "list" массива
            $text .=
                self::indent_list($level) .                             // Выравнивание по уровню вложенности
                self::align_list($key, $longest);                       // Выравнивание по длине ключа

            if($val == $prev) {                                         // Если текущее значение такое же, как и предыдущее
                $repet += 1;                                            // Количество повторов возрастает
                $repetStr = ' [' . $repet . ']';                        // Строка вывода количества повторов
            } else {                                                    // Иначе текущее значение отлично от предыдущего
                $prev = $val;                                           // Текущее значение нужно сохранить в предыдущее
                $repet = 0;                                             // Количество повторов сбросить в ноль
                $repetStr = '';                                         // Очистить строку вывода количества повторов
            }

            if(is_array($val)) {                                        // Если элемент массива является вложенными массивом
                $text .= "\n" . self::print_array($val, $level + 1);    // то надо рекурсивно вызвать текущую функцию
            } else {                                                    // Иначе элемент массива является простым типом
                $text .= $val . $repetStr . "\n";                       // и можно просто вывести его
            }
        }
        return $text;                                                   // Возвращение сформированной строки
    }

    private static $indent = 4;                                         // Количество пробелов в отступе уровня вложенности

    /**
     * Выравнивание строк по уровню вложенности
     *
     * @param  number $level Уровень вложенности
     * @return string        Набор пробелов для отступа
     */
    private static function indent_list($level) {
        $indent = '';
        for($l = 0; $l < $level; $l++) {                                // Цикл по заданному уровню вложенности
            for($s = 0; $s < self::$indent; $s++) {                     // Цикл по количеству пробелов в отступе уровня вложенности
                $indent .= ' ';
            }
        }
        return $indent;
    }

    private static $separator = ' -> ';                                 // Разделитель ключей и значений

    /**
     * Выравнивание по длине ключа
     *
     * @param  number | string  $key     Текущий индекс
     * @param  number           $longest Общее количество элементов
     * @return string                    Набор пробелов для выравнивания
     */
    private static function align_list($key, $longest) {
        $spaces = '';
        for($s = 0; $s < $longest - strlen($key); $s++) {               // Цикл до разницы между самым длинным ключом массива и текущим
            $spaces .= ' ';
        }
        return $spaces . $key . self::$separator;
    }
}