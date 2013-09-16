<?php

namespace ten;

class join extends core {

    /**
     * Конструктор
     *
     * @param array $options Массив опций
     */
    function __construct($options = []) {

        $this->options = array_merge($this->defaultOptions, $options);

        $this->start = $this->options['start'];
        $this->before = $this->options['before'];
        $this->after = $this->options['after'];
        $this->end = $this->options['end'];
    }

    /**
     * Дефолтные опции
     *
     * @var array
     */
    private $defaultOptions = array(
        'start' => '',
        'before' => '',
        'after' => '',
        'end' => ''
    );

    /**
     * Объединить файлы
     *
     * @param array $options Опции объединения
     * @return string
     */
    public function combine($options) {

        $concat = $this->concat($options['files']);

        $imploded = $this->implode(
            $this->start,
            $this->before,
            $this->after,
            $this->end,
            $concat
        );

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
    private function implode($start, $before, $after, $end, $concat) {
        return $start . $before . implode($after . $before, $concat) . $after . $end;
    }

    /**
     * Конкатенация содержимого файлов
     *
     * @param array $files Массив путей файлов
     * @return array
     */
    private function concat($files) {

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
    private function save($filename, $data) {
        return file_put_contents($filename, $data);
    }
}