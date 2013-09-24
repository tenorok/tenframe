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

        $this->resolve = $this->options['resolve'];
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
        'depth' => -1,

        'resolve' => true
    );

    /**
     * Объединить файлы
     *
     * @param array $files Массив путей до файлов
     * @param array [$options=[]] Опции объединения
     * @return string
     */
    public function combine($files, $options = []) {
        return $this->combineResolvedFiles($this->resolveFiles($files, true), $options);
    }

    /**
     * Объединить файлы по расширению
     *
     * @param string|array $extension Расширение или массив расширений
     * @param array [$options=[]] Опции объединения
     * @return string
     */
    public function extension($extension, $options = []) {

        $this->extension = is_string($extension) ? array($extension) : $extension;

        return $this->combineDirectoryFilesByCriterion($options, function($file) {
            return in_array($file->getExtension(), $this->extension);
        });
    }

    /**
     * Объединить файлы по регулярному выражению
     *
     * @param string $regexp Регулярное выражение
     * @param array [$options=[]] Опции объединения
     * @return string
     */
    public function regexp($regexp, $options = []) {

        $this->regexp = $regexp;

        return $this->combineDirectoryFilesByCriterion($options, function($file) {
            return preg_match($this->regexp, $file->getFilename());
        });
    }

    /**
     * Объединить файлы с приведёнными путями
     *
     * @param array $files Массив приведённых путей до файлов
     * @param array [$options=[]] Опции объединения
     * @return string
     */
    private function combineResolvedFiles($files, $options = []) {

        $concat = $this->concat($files);

        $imploded = $this->implode(
            $this->start,
            $this->before,
            $this->after,
            $this->end,
            $concat
        );

        if(array_key_exists('save', $options)) {
            $resolveSave = $this->resolve($options['save']);
            $this->save($resolveSave, $imploded) && $this->debug($files, $resolveSave);
        }

        return $imploded;
    }

    /**
     * Объединить файлы в директории по критерию
     *
     * @param array $options Опции объединения
     * @param callback $criterion Критерий искомых файлов
     * @return string
     * @throws \Exception При отсутствии опции directory в конструкторе
     */
    private function combineDirectoryFilesByCriterion($options, $criterion) {

        if(!$this->directory) throw new \Exception('Missing option: directory');

        $priorityList = $this->priorityList($options);

        $fileList = $this->fileList($this->iteratorsInit(), $priorityList, $criterion);

        return $this->combineResolvedFiles(array_merge($priorityList, $fileList), $options);
    }

    /**
     * Получение списка приоритетных файлов
     *
     * @param array $options Опции объединения
     * @return array
     */
    private function priorityList($options) {
        return array_key_exists('priority', $options) ? $this->resolveFiles($options['priority'], true) : array();
    }

    /**
     * Инициализация итераторов
     *
     * @return \RecursiveIteratorIterator[]
     */
    private function iteratorsInit() {

        $directory = is_string($this->directory) ? array($this->directory) : $this->directory;
        $iterators = array();

        foreach($directory as $dir) {
            $dirList  = new \RecursiveDirectoryIterator($this->resolve($dir, true));
            $iterator = new \RecursiveIteratorIterator($dirList);
            $iterator->setMaxDepth($this->depth);
            array_push($iterators, $iterator);
        }

        return $iterators;
    }

    /**
     * Получение массива файлов к объединению
     *
     * @param \RecursiveIteratorIterator[] $iterators Массив итераторов
     * @param array $priority Массив файлов по приоритету
     * @param callback $criterion Критерий искомых файлов
     * @return array
     */
    private function fileList($iterators, $priority, $criterion) {

        $list = array();

        foreach($iterators as $iterator) {
            foreach($iterator as $object) {
                $pathname = $object->getPathname();
                if($object->isFile() && !in_array($pathname, $priority) && $criterion($object)) {
                    array_push($list, $pathname);
                }
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

    /**
     * Приведение пути
     *
     * @param string $path Путь
     * @param boolean [$real=false] Существующий путь
     * @return string
     * @throws \Exception При отсутствии пути, который должен существовать
     */
    private function resolve($path, $real = false) {
        if(!$this->resolve) return $path;

        $resolve = is_string($this->resolve) ? $this->resolve : '';

        if($real) {
            $resolve = parent::resolveRealPath($resolve, $path);
            if(!$resolve) throw new \Exception('Path not found: ' . $path);
            return $resolve;
        }

        return parent::resolvePath($resolve, $path);
    }

    /**
     * Приведение массива путей
     *
     * @param array $files Массив путей
     * @param boolean [$real=false] Существующий путь
     * @return array
     */
    private function resolveFiles($files, $real = false) {
        return array_map(function($file) use ($real) {
            return $this->resolve($file, $real);
        }, $files);
    }

    /**
     * Массив для хранения информации по объединённым файлам
     *
     * @var array
     */
    public static $debug = array();

    /**
     * Сохранение информации по объединённым файлам
     *
     * @param array $files Массив путей до файлов
     * @param string $save Путь до результирующего файла
     */
    private function debug($files, $save) {
        if(!DEV) return;

        array_push(self::$debug, array(
            'files' => $files,
            'save' => $save
        ));
    }
}