<?php

/**
 * Вывод сообщений фреймворка
 * @version 0.0.1
 */

/* Использование

    Вывод простого сообщения фреймворка:
        ten\message::log('Message text');

    Вывод ошибки фреймворка:
        ten\message::error('Error text');
*/

namespace ten;

class message extends core {

    /**
     * Простое сообщение
     *
     * @param string $text Текст сообщения
     */
    public static function log($text) {

        echo '<br><b>Tenframe message</b>: ' . $text;
    }

    /**
     * Сообщение об ошибке
     *
     * @param string $text Текст ошибки
     */
    public static function error($text) {

        die('<br><b>Tenframe error</b>: ' . $text);
    }
}