<?php

/**
 * Отладка
 * @version 0.0.1
 */

/* Использование

    Вывод отладочной информации:
        ten\debug::show(array(
            'h1' => 'Header 1',                                         // Заголовок первого уровня
            'h2' => 'Header 2',                                         // Заголовок второго уровня
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
*/

namespace ten;

class debug extends core {

    /**
     * Вывести отладочную информацию
     *
     * @param array $info Массив с информацией к выводу
     */
    public static function show($info) {

        $text = '';

        foreach($info as $key => $val) {                                // Цикл по информационному массиву
            if(is_string($val)) {                                       // Если значение является строкой
                $text .= self::print_keys($key, $val);                  // Можно просто вывести этот ключ
            } else if(is_array($val)) {                                 // Иначе если значением элемента является массив
                foreach($val as $subKey => $subVal) {                   // Цикл по элементам этого подмассива
                    $text .= self::print_keys($subKey, $subVal);        // Вывод информации по каждому ключу
                }
            }
        }

        echo '<pre style="padding: 10px; background: #F5F5EA;">' . $text . '</pre>';
    }

    /**
     * Вывод отладочной информации из настроек
     *
     * @param bool | array $options Настройка вывода
     */
    public static function init($options = true) {

        self::show(array(
            'h1' => 'Tenframe debuger',
            array(
                'h2' => 'Templates:',
                'list' => tpl::$debugTemplates,
            ),
            array(
                'h2' => 'Autogen:',
                'list' => file::$debugAutogen
            ),
            array(
                'h2' => 'Join:',
                'list' => join::$debugJoin
            )
        ));
    }

    /**
     * Вывод значений ключей массива отладочной информации
     *
     * @param  string $key Ключ массива
     * @param  mixed  $val Значение массива
     * @return string      Сформированная строка
     */
    private static function print_keys($key, $val) {
        switch($key) {
            case 'h1':
                return '<b style="font-size: 20px;">' . $val . '</b>' . "\n";
            case 'h2':
                return '<b style="font-size: 16px;">' . $val . '</b>' . "\n";
            case 'list':
                return self::print_array($val, 0);
        }
    }

    private static $separator = ' -> ';                                 // Разделитель ключей и значений

    /**
     * Формирование строки к выводу из массива
     *
     * @param  array  $array Массив к выводу
     * @param  number $level Уровень вложенности
     * @return string        Сформированная строка
     */
    private static function print_array($array, $level) {

        if(!count($array)) {                                            // Если массив пустой
            return 'array()' . "\n";
        }

        $longest = max(array_map('strlen', array_keys($array)));        // Количество разрядов последнего элемента массива
        $text = '';
        foreach($array as $key => $val) {                               // Перебор всех элементов переданного в ключе "list" массива

            $text .=
                self::indent_list($level) .                             // Выравнивание по уровню вложенности
                self::align_list($key, $longest);                       // Выравнивание по длине ключа

            if(is_array($val)) {                                        // Если элемент массива является вложенными массивом
                $text .= "\n" . self::print_array($val, $level + 1);    // то надо рекурсивно вызвать текущую функцию
            } else {                                                    // Иначе элемент массива является простым типом
                $text .= $val . "\n";                                   // и можно просто вывести его
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