<?php

/**
 * Подключение статических файлов
 * @version 0.0.1
 */

/* Использование

    Задание пути до хранения путей статических файлов:
        ten\core::$settings = array(
            'statical' => '/view/statical/'                    // Значение по умолчанию
        );

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
                'output_file' => 'include.tpl',                // Файл для сохранения строк подключений файлов
                'prefix'      => '__autogen__',                // Префикс для сохраняемого файла (по умолчанию не добавляется)

                'hash'        => true | false                  // Флаг добавления хеш-метки к файлу (по умолчанию включено и длина = 7)
                // или
                'hash'        => 7                             // Длина хеш-метки, от 0 до 32 (по умолчанию = 7)
            ));

            Если имя передаётся в виде строки и это не JS-файл, то он автоматически подключается с помощью тега link.
            Если нужно подключить файл с помощью тега script, то требуется подключение в виде массива с передачей атрибута src:
            array(
                'src' => 'filename.java'
            )

    Подключение статических файлов:
        echo ten\statical::includes(
            'libs, developer, require',                        // Обязательный. Файлы с именами 'developer' и 'dev' подключаются только при включенном режиме разработчика
            GEN                                                // Префикс перед именами файлов (по умолчанию отсутствует)
        );
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
                self::$path . $options['output_file'],
                (core::$settings['compressHTML']) ? tpl::compressHTML($included) : $included,
                (!empty($options['prefix'])) ? $options['prefix'] : ''               // Префикс для сохраняемого файла
            );

            array_push(self::$debugStatical, $output_file);
        }

        return $included;                                                            // Возвращение строки подключения всех файлов
    }

    private static $include_dev = array('developer', 'dev');                         // Массив имён файлов, которые подключаются только при включенном режиме разработчика
    public static $path;                                                             // Путь для хранения путей к статическим файлам

    /**
     * Функция подключения include-файлов
     *
     * @param  string $files  Имена include-файлов
     * @param  string $prefix Префикс перед именами include-файлов
     * @return string
     */
    public static function includes($files, $prefix = '') {

        $includes = '';                                                              // Переменная для конкатенации содержимого файлов

        foreach(explode(',', $files) as $file) {                                     // Цикл по массиву переданных имён файлов

            $file = trim($file);                                                     // Обрезание пробелов с обеих сторон имени текущего файла

            if(in_array($file, self::$include_dev) && !DEV)                          // Если текущий файл требуется для режима разработчика и режим разработчика выключен
                continue;                                                            // то его подключать не нужно и выполняется переход к следующему файлу

            $includes .= file_get_contents(                                          // Конкатенация содержимого текущего файла
                parent::resolvePath(self::$path, $prefix . $file . '.tpl')           // Корректный вид пути
            );
        }

        return $includes;                                                            // Возвращение результата конкатенации содержимого файлов
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

                $type = file::info($file)['extension'];                              // Расширение файла

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

        $attrs[$url] .= self::getHash($options, $attrs[$url]);                       // Добавление хеша в строку пути файла

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

    private static $hashlen = 7;                                                     // Дефолтная длина добавочного хеша

    /**
     * Добавление метки в виде хеша файла
     *
     * @param  array  $options Параметры формирования строки подключения
     * @param  string $file    Относительный путь до файла
     * @return string          Строка метки
     */
    private static function getHash($options, $file) {

        if(!isset($options['hash'])) {                                               // Если опция хеша не задана
            $option = self::$hashlen;                                                // то хеш добавляется по стандартной длине
        } else {                                                                     // Иначе опция хеша задана
            if(is_numeric($options['hash'])) {                                       // В виде числа
                $option = $options['hash'];                                          // и надо добавить заданную длину
            } else if(is_bool($options['hash'])) {                                   // Или в виде булева значения
                $option = ($options['hash']) ? self::$hashlen : 0;                   // и надо добавить стандартную длину или не добавлять совсем
            }
        }

        return ($option) ?                                                           // Если метку надо добавлять
            '?' . substr(md5_file(core::resolvePath($file)), 0, $option) :           // Добавление метки с заданной длиной
            '';                                                                      // Иначе метку добавлять не нужно
    }
}