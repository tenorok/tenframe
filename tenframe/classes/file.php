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

    Сохранение автогенерированного файла
        ten\file::autogen(
            '/my/path/file.name',                              // Обязательный. Путь к файлу (можно относительный, можно абсолютный)
            'content',                                         // Обязательный. Контент файла
            '__prefix__'                                       // По умолчанию: "__autogen__" - префикс, который будет добавлен к имени файла
        );
        Возвращается полный путь к сохранённому файлу, например: /Users/name/my/path/__prefix__file.name
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
     * @param string $path Путь к папке или файлу
     * @return die || true
     */
    protected static function make_dir($path) {

        if(substr($path, 0, -1) != '/')                              // Если в конце переданной строки нет слеша
            $path = implode('/', array_slice(                        // то это путь к файлу и нужно получить чистый путь без имени файла
                explode('/', $path), 0, -1
            ));

        if(!file_exists($path))                                      // Если указанного пути не существует
            if(!mkdir($path, 0777, true))                            // Если не удалось создать каталоги, указанные в пути
                message::error('can\'t find and make directory: <b>' . $path . '</b>');

        return true;                                                 // Если скрипт не был убит, значит операция прошла успешно
    }

    /**
     * Функция сохранения автоматически сгенерированных файлов
     *
     * @param  string $path    Путь к файлу
     * @param  string $content Контент сохраняемого файла
     * @param  string $prefix  Префикс сохраняемого файла
     * @param  number $chmod   Права на создаваемый файл
     * @return string          Сформированный путь к сгенерированному файлу
     */
    public static function autogen($path, $content, $prefix = '__autogen__', $chmod = false) {
        
        if(!preg_match('/^' . str_replace('/', '\/', ROOT) . '/', $path))            // Если в пути не указана корневая директория
            $path = ROOT . $path;                                                    // то её надо добавить

        $path_arr = explode('/', $path);
        $last     = count($path_arr) - 1;
        $path_arr[$last] = $prefix . $path_arr[$last];

        $final_path = implode('/', $path_arr);

        self::make_dir($final_path);
        file_put_contents($final_path, $content);

        if($chmod && !chmod($final_path, $chmod)) {
            message::error('can\'t set chmod in file: ' . $final_path);
        }

        return $final_path;
    }
}