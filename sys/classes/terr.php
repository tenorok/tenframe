<?php

/**
 * Обработка ошибок
 * @version 0.0.1
 */

/* Использование

    Отключение отображения ошибок интерпретатора:
        error_reporting(0);

    Указание метода, который будет вызван по окончании выполнения всего скрипта:
        register_shutdown_function(array('error', 'get_error'));
*/

class terr {

    private static $sys_classes = array(                               // Определение классов системы, имена которых нельзя использовать в приложении
        'core', 'get', 'torm', 'terr', 'tmsg', 'tmod'
    );

    /**
     * Функция обработки ошибок интерпретатора
     *
     */
    public static function get_error() {

        $info = error_get_last();                                      // Получение массива с информацией о последней ошибке в таком формате: Array([type] => 1 [message] => Message text [file] => Path to file [line] => 1 )

        switch($info['type']) {

            case 1:                                                    // Если ошибка является фатальной

                if(stripos($info['message'],
                    'Call to undefined method') === 0) {               // Если это ошибка вызова неизвестного метода

                    if(preg_match('|Call to undefined method (.*)::|', $info['message'], $match)) {

                        foreach(self::$sys_classes as $class)
                            if($class == $match[1]) {                  // Если имя вызываемого класса совпадает хотя бы с одним из системных классов

                                echo tmsg::error('Called class-name (<b>' . $match[1] . '</b>) is used in Tenframe. Other reserved Tenframe classname: ');

                                foreach(self::$sys_classes as $class)
                                    echo '<b>' . $class . '</b>; ';

                                break;
                            }
                    }
                }

                break;
        }
    }
}