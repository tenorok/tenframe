<?php

/**
 * Установка окружения для тестов
 */

namespace ten\test;

interface ienv {
    public static function define($name, $value);
}

class env implements ienv {

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
}