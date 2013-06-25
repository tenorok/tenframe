<?php

/**
 * Выкачивание файлов
 * @version 0.0.1
 */

/* Использование

    Выкачивание файлов:
        ten\get::files(array(
            'files' => array(                                                           // Обязательный. Файлы к выкачиванию
                'http://code.jquery.com/jquery-1.10.1.min.js' => 'jquery.js',           // Можно указать иное имя конечного файла
                'https://raw.github.com/aFarkas/html5shiv/master/src/html5shiv.js',     // Иначе при сохранении будет указано имя оригинального файла
                'https://raw.github.com/necolas/normalize.css/master/normalize.css'
            ),
            'path' => '/assets/vendor/'                                                 // Обязательный. Путь для сохранения файлов
            // или
            'path' => array(                                                            // Можно указать в виде массива для каждого типа файлов
                'css' => '/assets/css/vendor/',
                'js'  => '/assets/js/vendor/'
            )
        ));
*/

namespace ten;

class get extends core {

    public static $debugGet = array();                                      // Массив выкачанных файлов

    /**
     * Выкачивание файлов
     *
     * @param  array $options Параметры выкачивания файлов
     * @return array          Массив путей сохранённых файлов
     */
    public static function files($options) {

        $gottenFiles = array();

        foreach($options['files'] as $getFile => $setFile) {                // Цикл по файлам

            $fileinfo = file::getInfo($setFile);                            // Информация о файле

            // Может быть указана одна общая директория или директории для каждого расширения файлов
            $path = (is_string($options['path'])) ? $options['path'] : $options['path'][$fileinfo['extension']];

            array_push($gottenFiles, file::autogen(                         // Сохранение файла и добавление в массив выкачанных файлов
                parent::resolve_path($path, $fileinfo['file']),
                file_get_contents((is_string($getFile)) ? $getFile : $setFile),
                ''
            ));
        }

        self::$debugGet = array_merge(self::$debugGet, $gottenFiles);       // Влияние путей до выкачанных файлов в массив для дебага

        return $gottenFiles;                                                // Массив путей сохранённых файлов
    }
}