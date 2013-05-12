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

    private static $blockname = 'tenframe-debug';                       // Имя блока отладочной информации

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

        echo
            self::print_style() .
            self::print_tag('pre', $text);
    }

    private static $style = array(                                      // Стили для отображения отладочной информации
        'block' => array(
            'padding' => '10px',
            'background' => '#F5F5EA'
        ),
        'elems' => array(
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

    /**
     * Печать стилей
     *
     * @return string Стили
     */
    private static function print_style() {
        return
            '<style type="text/css">' .
                self::print_style_rule(self::$style['block'], self::$blockname) .
                self::print_style_rule(self::$style['elems']) .
            '</style>';
    }

    /**
     * Печать стилевых правил
     *
     * @param  array         $rules     Массив стилей блока или элементов
     * @param  string | bool $blockname Имя блока
     * @return string                   Список стилей
     */
    private static function print_style_rule($rules, $blockname = false) {

        $ruleslist = '';

        if($blockname) {
            $ruleslist = self::print_style_block(
                $blockname,
                self::print_style_rules($rules)
            );
        } else {
            foreach($rules as $elem => $props) {
                $ruleslist .= self::print_style_block(
                    self::$blockname . '__' . $elem,
                    self::print_style_rules($props)
                );
            }
        }

        return $ruleslist;
    }

    /**
     * Печать селектора
     *
     * @param  string $selector Селектор
     * @param  string $rules    Правила
     * @return string           Блок селектора
     */
    private static function print_style_block($selector, $rules) {
        return
            '.' . $selector . '{' .
                $rules .
            '}';
    }

    /**
     * Печать правил для блока селектора
     *
     * @param  array $rules Массив стилей
     * @return string       Список правил
     */
    private static function print_style_rules($rules) {
        $style = '';
        foreach($rules as $prop => $val) {
            $style .= $prop . ':' . $val . ';';
        }
        return $style;
    }

    /**
     * Вывод отладочной информации из настроек
     *
     * @param bool | array $options Настройка вывода
     */
    public static function init($options = true) {

        self::show(array(
            'h1' => 'Tenframe debugger',
            array(
                'h2' => 'Templates:',
                'p' => 'Шаблоны, использованные для формирования страницы.',
                'list' => tpl::$debugTemplates,
            ),
            array(
                'h2' => 'Autogen:',
                'p' => 'Все автоматически сгенерированные файлы.',
                'list' => file::$debugAutogen
            ),
            array(
                'h2' => 'Join:',
                'p' => 'Объединённые файлы.',
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
            case 'h2':
            case 'p':
                return self::print_tag('p', $val, $key);
            case 'list':
                return self::print_tag('p', self::print_array($val, 0), $key);
        }
    }

    /**
     * Печать тега
     *
     * @param  string        $tag  Имя тега
     * @param  string        $val  Контент тега
     * @param  string | bool $elem Имя элемента
     * @return string              Готовый тег
     */
    private static function print_tag($tag, $val, $elem = false) {
        return
            '<' . $tag . ' class="' . self::$blockname .
            (($elem) ? '__' . $elem : '') .                             // Если элемент указан, то его надо напечатать
            '">' .
                $val .
            '</' . $tag . '>';
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
            return 'NULL' . "\n";
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