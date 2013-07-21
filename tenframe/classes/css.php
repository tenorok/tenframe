<?php

/**
 * Работа с CSS
 * @version 0.0.1
 */

/* Использование

    Сохранение LESS в CSS:
        Указывать расширения less и css не обязательно.
        Можно использовать разные формы записи.

        Длинная запись:
            ten\css::less(array(
                '/assets/css/main' => '/assets/css/newmain',
                '/assets/css/print.less' => '/assets/css/newprint.css'
            ));

        Сокращённая запись:
            ten\css::less(array(
                'main' => 'newmain',
                'print.less' => 'newprint.css'
            ), array(
                'path' => '/assets/css/'                    // LESS- и CSS-файлы лежат в одной директории
            ));

        Короткая запись:
            ten\css::less(array(
                'main', 'print.less'                        // CSS-файлы будут одноимёнными с LESS-файлами
            ), array(
                'path' => array(
                    'less' => '/lesspath/',                 // Отдельная директория для LESS-файлов
                    'css' => '/csspath/'                    // и отдельная для CSS-файлов
                )
                'compress' => false                         // Сжатие конечного CSS можно выключить
            ));
*/

namespace ten;

class css extends core {

    private static $options = array(                                                // Дефолтные параметры
        'path'      => '',
        'compress'  => true
    );

    /**
     * Сохранение LESS в CSS
     *
     * @param array $files   Массив файлов
     * @param array $options Массив опций
     */
    public static function less($files, $options = array()) {

        $options = array_merge(self::$options, $options);                           // Установка заданных опций

        if(is_string($options['path'])) {                                           // Если указана общая директория для LESS- и CSS-файлов
            $lessPath = $options['path'];
            $cssPath  = $options['path'];
        } else {                                                                    // Иначе для LESS- и CSS-файлов указаны разные директории
            $lessPath = $options['path']['less'];
            $cssPath  = $options['path']['css'];
        }

        foreach($files as $lessFile => $cssFile) {                                  // Цикл по переданным файлам

            $lessFilePath = parent::resolvePath(                                    // Путь до LESS-файла
                $lessPath,
                text::rgum(is_string($lessFile) ? $lessFile : $cssFile, '.less')    // Если ключ является строкой, то в нём хранится LESS-файл, иначе он хранится в значении
            );

            if(is_string($lessFile)) {                                              // Если ключ является строкой (если указан CSS-файл)
                $cssFilePath = parent::resolvePath(                                 // Путь до CSS-файла
                    $cssPath,
                    text::rgum($cssFile, '.css')
                );
            } else {                                                                // Иначе ключ является числом (указан только LESS-файл)
                $lessFileInfo = file::info($lessFilePath);
                $cssFilePath = $lessFileInfo['path'] . $lessFileInfo['name'] . '.css';  // CSS-файл должен быть одноимённым с LESS-файлом
            }

            $lessc = new \lessc;
            try {
                $css = $lessc->compileFile($lessFilePath);                          // Компиляция LESS в CSS
            } catch(\Exception $e) {
                message::error(array(
                    'LESS compiler:' => array(
                        'style'=> 'b',
                    ),
                    $e->getMessage()
                ));
            }

            if($options['compress']) {                                              // Если CSS требуется сжать
                $css = \CssMin::minify($css);
            }

            file::autogen($cssFilePath, $css, false);                               // Сохранение CSS-файла
        }
    }
}