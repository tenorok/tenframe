<?php

namespace ten;

class join extends core {

    /**
     * Дефолтные опции
     *
     * @var array
     */
    private static $options = array(
        'start' => '',
        'before' => '',
        'after' => '',
        'end' => ''
    );

    public static function files($options) {

        $options = array_merge(self::$options, $options);

        $concat = self::concat($options['files']);

        $imploded = self::implode($options['start'], $options['before'], $options['after'], $options['end'], $concat);

        if(array_key_exists('save', $options)) {
            self::save($options['save'], $imploded);
        }

        return $imploded;
    }

    /**
     * Объединение массива содержимого файлов в строку
     *
     * @param string $start В начало результирующей строки
     * @param string $before Перед каждым файлом
     * @param string $after После каждого файла
     * @param string $end В конец результирующей строки
     * @param array $concat Массив сожержимого файлов
     * @return string
     */
    private static function implode($start, $before, $after, $end, $concat) {
        return $start . $before . implode($after . $before, $concat) . $after . $end;
    }

    /**
     * Конкатенация содержимого файлов
     *
     * @param array $files Массив путей файлов
     * @return array
     */
    private static function concat($files) {

        $joined = array();

        foreach($files as $file) {
            array_push($joined, file_get_contents($file));
        }

        return $joined;
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