<?php

namespace ten;

class join extends core {

    /**
     * Конструктор
     *
     * @param array [$options=[]] Массив опций
     */
    function __construct($options = []) {

        $this->options = array_merge($this->defaultOptions, $options);

        $this->start = $this->options['start'];
        $this->before = $this->options['before'];
        $this->after = $this->options['after'];
        $this->end = $this->options['end'];

        $this->directory = $this->options['directory'];
        $this->depth = $this->options['depth'];
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
        'end' => '',

        'directory' => false,
        'depth' => -1
    );

    /**
     * Объединить файлы
     *
     * @param array $files Массив путей до файлов
     * @param array [$options=[]] Опции объединения
     * @return string
     */
    public function combine($files, $options = []) {

        $concat = $this->concat($files);

        $imploded = $this->implode(
            $this->start,
            $this->before,
            $this->after,
            $this->end,
            $concat
        );

        if(array_key_exists('save', $options)) {
            $this->save($options['save'], $imploded);
        }

        return $imploded;
    }

    /**
     * Объединить файлы по расширению
     *
     * @throws \Exception При отсутствии опции directory в конструкторе
     * @param string|array $extension Расширение или массив расширений
     * @param array [$options=[]] Опции объединения
     * @return string
     */
    public function extension($extension, $options = []) {

        if(!$this->directory) throw new \Exception('Missing option: directory');

        $priorityList = array_key_exists('priority', $options) ? $options['priority'] : array();

        $this->extension = is_string($extension) ? array($extension) : $extension;

        $fileList = $this->fileList($this->iteratorInit(), $priorityList, function($file) {
            return in_array($file->getExtension(), $this->extension);
        });

        return $this->combine(array_merge($priorityList, $fileList), $options);
    }

    /**
     * Инициализация итератора
     *
     * @return \RecursiveIteratorIterator
     */
    private function iteratorInit() {
        $dirList  = new \RecursiveDirectoryIterator($this->directory);
        $iterator = new \RecursiveIteratorIterator($dirList);
        $iterator->setMaxDepth($this->depth);
        return $iterator;
    }

    /**
     * Получение массива файлов к объединению
     *
     * @param \RecursiveIteratorIterator $iterator Итератор
     * @param array $priority Массив файлов по приоритету
     * @param callback $criterion Критерий искомого файла
     * @return array
     */
    private function fileList($iterator, $priority, $criterion) {

        $list = array();

        foreach($iterator as $object) {
            $pathname = $object->getPathname();
            if($object->isFile() && !in_array($pathname, $priority) && $criterion($object)) {
                array_push($list, $pathname);
            }
        }

        return $list;
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