<?php

/**
 * Конкатенация файлов
 * @version 0.0.1
 */

/* Использование

    Конкатенация файлов:
        $result = ten\join::files(array(

            'files'       => 'ext: css, js, ... , etc',        // Обязательный. Мод расширений объединяемых файлов
            // или
            'files'       => 'reg: /\.ctrl\.js$/',             //               Мод регулярного выражения
            // или
            'files'       => array('file1', 'file2'),          //               Массив файлов к объединению

            'output_file' => '/assets/{ext}/file.{ext}',       // Обязательный. Выходящий файл
                                                                  Это может быть маска формируемых на выходе файлов при передаче нескольких расширений.
                                                                  Где {ext} - расширение (extension) файла.
                                                                  Переменная существует только при использовании мода расширений ('files' => 'ext: ')

            'priority'    => '/view/core.js',                  // Файл, который нужно подключить первым
            // или
            'priority'    => array('file1', 'file2'),          // или несколько первых файлов

            'input_path'  => '/view/',                         // Корневая директория, содержащая объединяемые файлы
            // или
            'input_path'  => array('/view1/', '/view2/'),      // Массив корневых директорий
                                                                  По умолчанию: array('/view/', mods), где mods - это пути к папками view подключенных модулей

            'before'      => "\n start: {filename} { \n",      // Строка, помещаемая перед содержанием очередного файла
            'after'       => "\n } {filename} :end \n",        // Строка, помещаемая после содержания очередного файла
                                                                  Где {filename} - путь и имя текущего файла

            'start_str'   => "start { \n",                     // Строка, помещаемая в начало файла, по умолчанию отсутствует
            'end_str'     => "\n } end",                       // Строка, помещаемая в конец файла, по умолчанию отсутствует

            'compress'    => true | false,                     // Флаг сжатия конечного файла (работает для CSS и JS), по умолчанию включено
            'recursion'   => true | false                      // Флаг рекурсивного перебора дочерних директорий корневой директории, по умолчанию включено
        ));

        Если в качестве выходящего файла явно указан CSS или JS, то собираемые файлы
        будут скомпрессованы вне зависимости от их истинного расширения,
        например: 'assets/css/style.{ext}.css'

        В случае успешной загрузки, в $result возвращается массив с подмассивом путей входящих файлов и
        подмассивом (или строкой, если было указано одно расширение) выходящих файлов, например:
            Array (
                [input] => Array (
                    [0] => view/blocks/dir_1/style.css
                    [1] => view/blocks/dir_2/style.css
                    [2] => view/blocks/dir_1/script.js
                    [3] => view/blocks/dir_2/script.js
                    ...
                ) [output] => Array (
                    [0] => assets/css/main.css
                    [1] => assets/js/main.js
                )
            )
*/

namespace ten;

class join extends file {

    private static $folders = array();                                               // Массив директорий
    private static $input_files = array();                                           // Массив путей объединённых файлов
    private static $output_file;                                                     // Строка, в которую собираются файлы

    public  static $input_path = array('/view/');                                    // Массив входящих директорий
    public  static $debugJoin  = array();                                            // Массив объединённых файлов

    private static $options = array(                                                 // Дефолтные параметры объединения файлов
        'before'    => '',
        'after'     => '',
        'start_str' => '',
        'end_str'   => '',
        'priority'  => '',
        'compress'  => true,
        'recursion' => true
    );

    /**
     * Функция объединения файлов
     *
     * @param  array $options Параметры объединения файлов
     * @return array
     */
    public static function files($options) {

        $options['output_file'] = ROOT . $options['output_file'];                    // Абсолютный путь выходящего файла

        foreach(self::$options as $key => $val)                                      // Установка значений по умолчанию
            if(!isset($options[$key]))                                               // для незаданных опций
                $options[$key] = $val;

        if(!empty($options['priority']))                                             // Если указаны приоритетные файлы
            self::concat($options['priority'], $options);                            // нужно сперва прилепить их

        if(gettype($options['files']) == 'array') {                                  // Если передан массив файлов

            $output = self::join_files('fls', $options['files'], $options);          // Нужно просто их объединить
        }
        else {                                                                       // Иначе передан мод расширений или регулярных выражений

            $files = explode(':', $options['files']);                                // Разбиение строки объединяемых файлов в массив
            $files_mod = trim($files[0]);                                            // Мод поиска файлов (ext или reg)

            if($files_mod == 'ext')                                                  // Если задан мод расширений
                $files_val    = explode(',', $files[1]);                             // то строку значения надо разбить в массив расширений
            else if($files_mod == 'reg')                                             // Если задан мод регулярного выражения
                $files_val[0] = $files[1];                                           // то достаточно просто переприсвоить строку значения

            if(!isset($options['input_path']))                                       // Если входящие директории не указаны явно
                $options['input_path'] = self::$input_path;                          // будут использоваться стандартные

            if(
                $files_mod == 'reg' ||                                               // Если задан мод регулярного выражения
                $files_mod == 'ext' && count($files_val) == 1                        // или мод расширений и указано всего одно расширение
            ) {
                $output = self::join_files($files_mod, trim($files_val[0]), $options);  // То можно просто вызвать функцию объединения один раз
            }
            else {                                                                   // Иначе задан мод расширений и указано больше одного расширения

                $output = array();                                                   // Массив для путей собранных файлов

                foreach($files_val as $extension)                                    // Цикл по полученным расширениям
                    array_push(                                                      // Добавление
                        $output,                                                     // в массив путей
                        self::join_files($files_mod, trim($extension), $options)     // результата слияния файлов
                    );
            }
        }

        $input = self::$input_files;
        self::$input_files = array();                                                // Обнуление файла путей объединённых файлов

        $result = array(
            'input'  => $input,
            'output' => $output
        );

        array_push(self::$debugJoin, $result);
        return $result;
    }

    /**
     * Функция непосредственного объединения файлов
     *
     * @param  string $mod       Мод поиска файлов (fls, ext или reg)
     * @param  string $val       Значение поиска файлов (расширение, регулярное выражение или массив файлов)
     * @param  array  $options   Параметры объединения файлов
     * @return string
     */
    private static function join_files($mod, $val, $options) {

        $output_extension = end(explode('.', $options['output_file']));              // Расширение выходящего файла

        if($mod == 'fls') {                                                          // Если переданы конкретные файлы для объединения

            self::concat($file, $options);                                           // Непосредственное прилепливание текущего файла к конечному
        }
        else {                                                                       // Иначе передано расширение или регулярное выражение

            if(gettype($options['input_path']) == 'array')                           // Если указан массив входящих директорий
                $input_path    = $options['input_path'];
            else                                                                     // Иначе указана одна входящая директория
                $input_path[0] = $options['input_path'];

            foreach($input_path as $path) {                                          // Цикл по входящим директориям

                $options['input_path'] = text::rgum(ROOT . $path, '/');              // Абсолютный путь входящей корневой директории и добавление слеша в конец пути, если его там нет

                self::get_folders($mod, $val, $options);                             // Вызов функции рекурсивного перебора директорий
            }
        }

        self::$output_file =                                                         // Добавление
            $options['start_str']  .                                                 // первой строки
                self::$output_file .                                                 // к выходящему файлу
                $options['end_str'];                                                 // и последней строки

        $extension = ($mod == 'ext') ? $val : '';                                    // Переменная расширения будет существовать только когда задан мод расширений

        if($extension == 'css' || $output_extension == 'css') {                      // Если текущее расширение или расширение выходящего файла является CSS

            if(is_null($options['compress']) || $options['compress'])                // Если сжатие конечного файла не отключено
                self::$output_file = css::minify(self::$output_file);
        }
        else if($extension == 'js' || $output_extension == 'js') {                   // Если текущее расширение или расширение выходящего файла является JS

            if(is_null($options['compress']) || $options['compress'])                // Если сжатие конечного файла не отключено
                self::$output_file = trim(jsmin::minify(self::$output_file));
        }

        $output_file = parent::resolve_path(                                         // Установление корректного пути до файла
            str_replace('{ext}', $extension, $options['output_file'])
        );

        parent::make_dir($output_file);                                              // Создание пути, если его не существует

        file::autogen($output_file, self::$output_file, '');                         // Запись итоговой строки в выходящий файл

        self::$output_file = '';                                                     // Обнуление строки собранного файла

        return $output_file;                                                         // Возвращается путь к составленному файлу
    }

    /**
     * Функция рекурсивного перебора директорий для объединения файлов
     *
     * @param  string $mod       Мод поиска файлов (ext или reg)
     * @param  string $val       Значение поиска файлов (расширение, регулярное выражение или массив файлов)
     * @param  array  $options   Параметры объединения файлов
     * @return function
     */
    private static function get_folders($mod, $val, $options) {

        if($input = opendir($options['input_path'])) {                               // Если открылась первоначальная директория

            while($object = readdir($input)) {                                       // Цикл по объектам в текущей директории

                if($object != '.' && $object != '..') {                              // Если текущий объект является файлом или директорией

                    $directory = $options['input_path'] . $object . '/';

                    if(
                        is_dir($directory) &&                                        // Если текущий объект является директорией
                        $options['recursion']                                        // и требуется рекурсивный перебор директорий
                    ) {
                        array_push(self::$folders, $directory);                      // он добавляется в массив директорий
                    }
                    else if (                                                        // Иначе текущий объект - это файл
                        $mod == 'ext' &&                                             // Если задан мод расширений
                        end(explode('.', $object)) == $val ||                        // и расширение текущего файла соответствует заданному для поиска

                        $mod == 'reg' &&                                             // Или задан мод регулярного выражения
                        preg_match($val, $object)                                    // и имя текущего файла удовлетворяет условия регулярного выражения
                    ) {
                        self::concat(                                                // Непосредственное прилепливание текущего файла
                            $options['input_path'] . $object,                        // Полный путь к файлу
                            $options
                        );
                    }
                }
            }

            if(count(self::$folders)) {                                              // Если имеются непросмотренные директории

                $options['input_path'] = self::$folders[0];                          // Задание новой директории для дальнейшего рекурсивного вызова функции
                array_shift(self::$folders);                                         // Удаление присвоенной директории из массива непросмотренных директорий
                return self::get_folders($mod, $val, $options);                      // Рекурсивный вызов функции
            }

            closedir($input);                                                        // Закрытие текущего объекта
        }
        else
            message::error('Can\'t open directory: ' . $options['input_path']);
    }

    /**
     * Непосредственное прилепливание файлов к выходящему файлу
     *
     * @param string | array $files   Полные пути конкатенируемых файлов
     * @param array          $options Параметры объединения файлов
     */
    private static function concat($files, $options) {

        if(gettype($files) == 'string')                                              // Если передан один файл
            $files = array($files);                                                  // нужно сделать из него массив

        foreach($files as $file) {                                                   // Цикл по файлам

            $file = core::resolve_path($file);                                       // Установление корректного пути до файла

            if(in_array($file, self::$input_files))                                  // Если файл уже был прилеплен
                return;                                                              // то его уже не нужно прилеплять

            array_push(self::$input_files, $file);                                   // Добавление пути текущего файла в массив путей объединённых файлов

            self::$output_file .=                                                    // Добавление
                str_replace('{filename}', $file, $options['before']) .               // предваряющей строки
                file_get_contents($file)                             .               // к содержанию текущего файла
                str_replace('{filename}', $file, $options['after']);                 // и последующей строки
        }
    }
}