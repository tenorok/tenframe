<?php

/**
 * Подключение статических файлов
 * @version 0.0.1
 */

/* Использование

    Подключение статических файлов в HTML-шаблон:
        Простое подключение одного файла:
            echo ten\statical::involve('/assets/css/style.css');

        Подключение нескольких файлов:
            echo ten\statical::involve(array(
                '/assets/css/style.css',
                '/assets/css/manager.css',
                '/assets/js/main.js'
            ));

        Подключение файлов с заданием дополнительных опций:
            echo ten\statical::involve(array(                  // Обязательный. Массив файлов
                array(                                         // Массив атрибутов тега подключения файла
                    'href'    => 'print.css',
                    'media'   => 'print',
                    'data-my' => 'param'
                ),
                'style.css',                                   // Просто строка с именем файла
                'manager.css',
                'main.js',
                'info.xml',
                'icon.ico'
            ), array(
                'path' => array(                               // Массив путей к файлам с конкретными расширениями
                    'css' => '/assets/css/',
                    'js'  => '/assets/js/',
                    'xml' => '/assets/xml/',
                    'ico' => '/assets/'
                ),
                'output_file' => '/view/include.tpl',          // Файл для сохранения строк подключений файлов
                'prefix'      => '__autogen__',                // Префикс для сохраняемого файла (по умолчанию не добавляется)
                'hash'        => true | false                  // Флаг добавления хеш-метки к файлу (по умолчанию включено)
            ));

            Если имя передаётся в виде строки и это не JS-файл, то он автоматически подключается с помощью тега link.
            Если нужно подключить файл с помощью тега script, то требуется подключение в виде массива с передачей атрибута src:
            array(
                'src' => 'filename.java'
            )
*/

namespace ten;

class statical extends file {

    public static $debugStatical = array();                                          // Массив автоматически-сгенерированных подключений

    /**
     * Функция формирования строки подключения CSS- и JS-файлов к HTML
     *
     * @param  string | array $files   Имя файла для подкючения или массив имён
     * @param  array          $options Параметры формирования строки подключения
     * @return string
     */
    public static function involve($files, $options = null) {

        if(gettype($files) == 'string')                                              // Если нужно подключить один файл
            $included = self::involve_file($files, $options);                        // Возвращается строка подключения файла

        else if(gettype($files) == 'array') {                                        // Если нужно подключить массив файлов

            $included = '';                                                          // Объявление результирующей строки

            foreach($files as $file)                                                 // Цикл по массиву файлов
                $included .= self::involve_file($file, $options) . "\n";             // Добавление строки подключения файла в результирующую строку
        }

        if(!empty($options['output_file'])) {                                        // Если указан файл для сохранения результата

            $output_file = parent::autogen(                                          // Сохранение файла
                $options['output_file'],
                (core::$settings['compressHTML']) ? tpl::compressHTML($included) : $included,
                (!empty($options['prefix'])) ? $options['prefix'] : ''               // Префикс для сохраняемого файла
            );

            array_push(self::$debugStatical, $output_file);
        }

        return $included;                                                            // Возвращение строки подключения всех файлов
    }

    /**
     * Функция непосредственного подключения файла
     *
     * @param  string | array $files   Имя файла для подкючения
     * @param  array          $options Параметры формирования строки подключения
     * @return string
     */
    private static function involve_file($file, $options) {

        $def_attrs = array(                                                          // Массив дефолтных значений атрибутов

            'link' => array(                                                         // для тега link
                'href'    => '',
                'rel'     => 'stylesheet',
                'type'    => 'text/css',
                'media'   => '',
                'charset' => '',
                'sizes'   => ''
            ),

            'script' => array(                                                       // для тега script
                'src'      => '',
                'type'     => '',
                'language' => '',
                'defer'    => ''
            )
        );

        switch(gettype($file)) {

            case 'array':                                                            // Если текущий файл представлен в виде массива

                if(!empty($file['src'])) {                                           // Если указан атрибут src

                    $url = 'src';
                    $tag = 'script';
                }
                else if(!empty($file['href'])) {                                     // иначе если указан атрибут href

                    $url = 'href';
                    $tag = 'link';
                }
                else                                                                 // иначе ни href ни src не указаны и надо вывести ошибку
                    message::error('Can\'t read attribute "href" or "src" of include file');

                $type = end(explode('.', $file[$url]));                              // Расширение файла
                $attrs = array_merge($def_attrs[$tag], $file);                       // Слияние массива дефолтных атрибутов с переданным массивом атрибутов

                break;

            case 'string':                                                           // Если текущий файл представлен строкой

                $type = end(explode('.', $file));                                    // Расширение файла

                switch($type) {

                    case 'js':                                                       // Если js-файл
                        $url = 'src';
                        $tag = 'script';
                        break;

                    default:                                                         // Если иной файл (css, ico, xml, etc)
                        $url = 'href';
                        $tag = 'link';
                }

                $attrs = $def_attrs[$tag];                                           // Указание массива с дефолтными значениями
                $attrs[$url] = $file;                                                // Задание значения для атрибута пути/имени файла
        }

        if(isset($options['path']))
            $attrs[$url] = $options['path'][$type] . $attrs[$url];                   // Добавление пути к файлу

        if(!isset($options['hash']) || $options['hash'])                             // Если нужно добавить хеш файла
            $attrs[$url] .= '?' . md5_file(ROOT . $attrs[$url]);                     // добавление хеша в строку пути файла

        $attrs_str = '';

        foreach($attrs as $attr => $val)                                             // Цикл по атрибутам
            if(!empty($val))                                                         // Если значение атрибута не пустое
                $attrs_str .= ' ' . $attr . '="' . $val . '"';                       // его надо добавить в тег

        switch($tag) {

            case 'script':                                                           // Если тег script
                return '<script' . $attrs_str . '></script>';

            case 'link':                                                             // Если тег link
                return '<link' . $attrs_str . '>';
        }
    }
}