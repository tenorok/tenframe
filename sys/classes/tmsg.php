<?php

/**
 * Вывод сообщений фреймворка
 * @version 0.0.1
 */

/* Использование

    Вывод простого сообщения фреймворка:
        tmsg::log('Message text');

    Вывод ошибки фреймворка:
        tmsg::error('Error text');
*/

class tmsg extends core {

    /**
     * Простое сообщение
     *
     * @param string $text Текст сообщения
     */
    public static function log($text) {

        echo '<br><b>Framework message</b>: ' . $text;
    }

    /**
     * Сообщение об ошибке
     *
     * @param string $text Текст ошибки
     */
    public static function error($text) {

        die('<br><b>Framework error</b>: ' . $text);
    }
}