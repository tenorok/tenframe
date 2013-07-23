<?php

/**
 * Установка окружения для тестов
 */

namespace ten\test;

interface ienv {
    public static function define($name, $value);
    public static function setTestUrl($url);
}

class env extends \ten\core implements ienv {

    /**
     * Определить константу
     *
     * @param string $name  Имя
     * @param mixed  $value Значение
     */
    public static function define($name, $value) {
        !defined($name)?
            runkit_constant_add($name, $value) :
            runkit_constant_redefine($name, $value);
    }

    /**
     * Установить массив информации об URL для тестового окружения
     *
     * @param  string $url Строка запроса
     * @return array       Массив информации
     */
    public static function setTestUrl($url) {
        return parent::setUrlInfo($url);
    }
}