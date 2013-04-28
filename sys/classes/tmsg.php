<?php
/**
 * Вывод сообщений фреймворка
 * @version 0.0.1
 */

/** Использование

    Вывод простого сообщения фреймворка:
        tmsg::log('Message text');
*/
class tmsg extends core {

    /**
     * Функция печати сообщений системы
     *
     * @param string $text Текст сообщения
     */
    public static function log($text) {

        echo '<br><b>Framework message</b>: ' . $text;
    }
}