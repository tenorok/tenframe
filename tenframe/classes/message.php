<?php

/**
 * Вывод сообщений фреймворка
 * @version 0.0.1
 */

/* Использование

    Вывод простого сообщения:
        ten\message::log('Message text');

    Вывод ошибки с полным прекращением работы всего скрипта:
        ten\message::error('Error text');

    Кроме простых строк, методы могут принимать в качестве параметра массив,
    его элементы будут склеены в результирующую строку через пробел:
        ten\message::method(array(
            'Just text.',                                               // Обычный текст без стилей
            'Bold italic and underline text in red color.' => array(    // Жирный текст с курсивом и подчёркиванием, красного цвета
                'style'=> 'biu',                                        // Bold, Italic, Underline
                'color' => '#f00'                                       // Цвет можно передавать в CSS-формате
            ),
            'Italic text in blue color.' => array(                      // Курсивный текст синего цвета
                'style'=> 'i',                                          // Italic
                'color' => 'blue'
            )
        ), 'Prepend text');                                             // Предваряющий основное сообщение текст
*/

namespace ten;

class message extends core {

    /**
     * Простое сообщение
     *
     * @param string $message Сообщение
     * @param string $prepend Предваряющее основное сообщение текст
     */
    public static function log($message, $prepend = 'Message') {
        echo '<br><b>' . implode(' ', array(self::tenMark(), $prepend)) . '</b>: ' . self::getText($message);
    }

    /**
     * Сообщение об ошибке
     *
     * @param string $message Сообщение
     * @param string $prepend Предваряющее основное сообщение текст
     */
    public static function error($message, $prepend = 'Error') {
        die('<br><b>' . implode(' ', array(self::tenMark(), $prepend)) . '</b>: ' . self::getText($message));
    }

    /**
     * Определение необходимости установить маркировку фреймворка в начало сообщения
     *
     * @return string Маркировка фреймворка или её отсутствие
     */
    private static function tenMark() {
        $backtrace = debug_backtrace();
        return (isset($backtrace[2]['class']) && preg_match('/^ten\\\/', $backtrace[2]['class']))? 'Tenframe' : '';
    }

    private static $css = array(                            // Стили для сообщения
        'style' => array(
            'b' => 'font-weight:bold',
            'i' => 'font-style:italic',
            'u' => 'text-decoration:underline'
        ),
        'color' => 'color:'
    );

    private static $settedStyles = array();                 // Массив установленных для сообщения стилей

    /**
     * Преобразование сообщения в текст
     *
     * @param  string | array $message Сообщение
     * @return string                  Сообщение в виде строки
     */
    private static function getText($message) {
        if(is_string($message)) return $message;            // Если сообщение уже в виде строки

        foreach($message as $text => $style) {              // Иначе сообщение в виде массива сообщений
            if(is_string($style)) continue;                 // Если значение является строкой
                                                            // Иначе в значении передан массив стилей
            self::$settedStyles = array();                  // Для каждого сообщения нужно обнулить массив установленных стилей

            foreach($style as $key => $val) {               // Цикл по стилям
                $css = self::$css[$key];
                switch($key) {
                    case 'style':
                        foreach(str_split($val) as $s) {    // Цикл по буквам, означающим стили текста
                            array_push(self::$settedStyles, $css[$s]);  // Добавление стиля в массив установленных стилей
                        }
                        break;
                    case 'color':
                        array_push(self::$settedStyles, $css . $val);   // Добавление стиля в массив установленных стилей
                        break;
                }
            }

            $message[$text] = '<span style="' . implode(';', self::$settedStyles) . '">' . $text . '</span>';
        }

        return implode(' ', $message);                      // Части сообщения разделяются пробелом
    }
}