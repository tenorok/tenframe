<?php

/**
 * Обеспечение безопасности
 * @version 0.0.1
 */

/* Использование

    Проверка первичного ключа:
        if(ten\secure::id($_GET['id']))
            // Переданный идентификатор является целым положительным числом

        if(ten\secure::id($_GET['id'], 'table'))
            // Кроме того, в таблице table имеется запись с этим идентификатором
*/

namespace ten;

class secure extends core {

    /**
     * Функция проверки первичных ключей
     *
     * @param  mixed  $id    Идентификатор для проверки
     * @param  string $table Имя таблицы, в которой следует искать наличие идентификатора
     * @return boolean
     */
    public static function id($id, $table = null) {

        if(is_int($id) && $id > 0) {

            if(!is_null($table))
                if(count(ten\orm::select($table)->where($id)) > 0)
                    return true;
                else
                    return false;

            return true;
        }
        else
            return false;
    }
}
