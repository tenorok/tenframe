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
        return $start . $this->addBeforeAfter($before, $after, $concat) . $end;
    }

    /**
     * Добавление значений опций before и after
     *
     * @param string $before Перед каждым файлом
     * @param string $after После каждого файла
     * @param array $concat Массив сожержимого файлов
     * @return string
     */
    private function addBeforeAfter($before, $after, $concat) {

        // Оптимизация для скорости при отсутствии опций
        if(empty($before) && empty($after)) return implode('', $concat);

        $added = array();

        foreach($concat as $filename => $data) {
            array_push(
                $added,
                $this->variable('{filename}', $filename, $before) .
                $data .
                $this->variable('{filename}', $filename, $after)
            );
        }

        return implode('', $added);
    }

    /**
     * Замена переменной на значение
     *
     * @param string $name Имя переменной
     * @param string $value Значение
     * @param string $string Строка содержащая переменную
     * @return mixed
     */
    private function variable($name, $value, $string) {
        return str_replace($name, $value, $string);
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
            $joined[$file] = file_get_contents($file);
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