<?php

namespace ten;

class join extends core {

    public static function files($options) {

        $joined = self::concat($options['files']);

        if(array_key_exists('save', $options)) {
            self::save($options['save'], $joined);
        }

        return $joined;
    }

    /**
     * Конкатенация содержимого файлов
     *
     * @param array $files Массив путей файлов
     * @return string
     */
    private static function concat($files) {

        $joined = array();

        foreach($files as $file) {
            array_push($joined, file_get_contents($file));
        }

        return implode('', $joined);
    }

    /**
     * Сохранение содержимого в файл
     *
     * @param string $filename Путь до файла
     * @param string $data Содержимое
     * @return int
     */
    private static function save($filename, $data) {
        return file_put_contents($filename, $data);
    }
}