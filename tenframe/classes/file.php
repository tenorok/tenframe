<?php

/**
 * Работа с файлами
 * @version 0.0.1
 */

/* Использование
    
    Сохранение массива в файл:
        ten\file::save_arr('/conf/settings.conf', array(
            'key_0' => 'val_0',
            'key_1' => 'val_1'
        ));
        
    Чтение массива из файла:
        $content = ten\file::read_arr('/conf/settings.conf');

    Создание директории:
        ten\file::make_dir('/new/path/');

    Сохранение автогенерированного файла:
        ten\file::autogen(
            '/my/path/file.name',                              // Обязательный. Путь к файлу (можно относительный, можно абсолютный)
            'content',                                         // Обязательный. Контент файла
            '__prefix__'                                       // По умолчанию: "__autogen__" - префикс, который будет добавлен к имени файла
        );
        Возвращается полный путь к сохранённому файлу, например: /Users/name/my/path/__prefix__file.name

    Получение информации о файле:
        $info = ten\file::info('/path/to/file.ext');
        Возвращает массив:
        array(
            'path' => '/path/to/',
            'file' => 'file.ext',
            'name' => 'file',
            'extension' => 'ext'
        )
*/

namespace ten;

class file extends core {
    
    /**
     * Функция сохранения массива в файл
     *
     * @param  string $filename Имя файла
     * @param  array $array Массив для записи в файл
     * @return array
     */
    public static function save_arr($filename, $array) {
        
        $filename = ROOT . $filename;
        file_put_contents($filename, serialize($array));
        chmod($filename, 0644);
    }
    
    /**
     * Функция чтения массива из файла
     *
     * @param  string $filename Имя файла
     * @return array
     */
    public static function read_arr($filename) {
        
        return unserialize(file_get_contents(ROOT . $filename));
    }
    
    /**
     * Функция создания директории, если её не существует
     * 
     * @param string $path Путь к папке
     * @return die || true
     */
    public static function make_dir($path) {

        if(!file_exists($path))                                      // Если указанного пути не существует
            if(!mkdir($path, 0755, true))                            // Если не удалось создать каталоги, указанные в пути
                message::error('can\'t find and make directory: <b>' . $path . '</b>');

        return true;                                                 // Если скрипт не был убит, значит операция прошла успешно
    }

    public static $autoprefix = '__autogen__';                       // Префикс автоматически сгенерированных файлов
    public static $debugAutogen = array();                           // Массив автоматически-сгенерированных файлов

    /**
     * Функция сохранения автоматически сгенерированных файлов
     *
     * @param  string $path    Путь к файлу
     * @param  string $content Контент сохраняемого файла
     * @param  string $prefix  Префикс сохраняемого файла
     * @param  number $chmod   Права на создаваемый файл
     * @return string          Сформированный путь к сгенерированному файлу
     */
    public static function autogen($path, $content, $prefix = null, $chmod = false) {

        $clear_path = parent::resolvePath($path);                    // Приведение пути к корректному виду

        $path_arr = explode('/', $clear_path);                       // Разбивка пути на массив
        $prefix = (is_null($prefix)) ? GEN : $prefix;                // Установить стандартный префикс, если он не задан
        $last = $prefix . array_pop($path_arr);                      // Получение последнего элемента и добавление ему префикса
        array_push($path_arr, $last);                                // Возвращение последнего элемента с префиксом

        $final_path = implode('/', $path_arr);                       // Вновь объединение массива в строку

        self::make_dir(dirname($final_path));                        // Создание директории
        file_put_contents($final_path, $content);                    // Сохранение файла
        array_push(self::$debugAutogen, $final_path);                // Добавление файла в массив автоматически-сгенерированных

        if($chmod && !chmod($final_path, $chmod)) {                  // Установление прав на файл, если это требуется
            message::error('can\'t set chmod to file: ' . $final_path);
        }

        return $final_path;                                          // Путь до созданного файла
    }

    /**
     * Получение информации о файле
     *
     * @param  string $file Путь до файла
     * @return array        Информация о файле
     */
    public static function info($file) {

        $pathParts = explode('/', $file);
        $filename = array_pop($pathParts);

        $filenameParts = explode('.', $filename);
        $extension = array_pop($filenameParts);

        return array(
            'path' => implode('/', $pathParts) . '/',
            'file' => $filename,
            'name' => implode('.', $filenameParts),
            'extension' => $extension
        );
    }
}