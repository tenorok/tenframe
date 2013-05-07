<?php

/**
 * Работа с изображениями
 * @version 0.0.1
 */

/* Использование

    Загрузка изображения:
        Простая загрузка одного изображения:
            <input type="file" name="image">
            $result = ten\image::upload($_FILES['image']);
            В случае успешной загрузки, в $result возвращается абсолютный путь и имя файла,
            например: assets/images/directory_1/directory_2/name.jpg

        Простая загрузка массива изображений:
            <input type="file" name="images[]">
            $result = ten\image::upload($_FILES['images']);
            В случае успешной загрузки, в $result возвращается массив абсолютных путей и имена файлов,
            например: Array (
                [0] => assets/images/directory_1/directory_2/name_1.jpg
                [1] => assets/images/directory_1/directory_2/name_2.jpg
                ...
            )

        Загрузка с миниатюрами:
            $result = Array (
                [originals] => Array (
                    [0] => assets/images/directory_1/directory_2/name_1.jpg
                    ...
                ) [miniatures] => Array (
                    [0] => assets/images/directory_1/directory_2/mini_name_1.jpg
                    ...
                )
            )

        Возможные ошибки:
            if($result)
                // Загрузка прошла успешно
            else if($result == -1)
                // Ошибка: Неверный тип изображения
            else if($result == -2)
                // Ошибка: Слишком большой вес изображения
            else if($result == -3)
                // Ошибка: Невозможно прочесть информацию об изображении (скорее всего это не изображение)
            else
                // Неизвестная ошибка

        Дополнительные параметры:
            ten\image::$debug = true;                       // Отладка загрузки
            ten\image::$image_size = 10;                    // Максимально возможный вес загружаемых изображений в мегабайтах
            ten\image::$image_type = array('png');          // Массив допустимых типов изображений (по умолчанию gif, png, jpeg). Важно: поддержка других типов изображений не гарантируется

        Возможные опции:
            $result = ten\image::upload($_FILES['image'], array(
                'path'        => '/assets/',                // Путь для загрузки изображения (слеш на конце не обязателен)
                'name'        => 'image_name',              // Новое имя для загружаемого изображения (без расширения)
                'convert'     => 'jpeg',                    // Формат, в который нужно конвертировать изображение (гарантированно поддерживаются: gif, png, jpeg). Важно: jpeg, а не jpg
                'width'       => 100,                       // Максимальная ширина
                'height'      => 200,                       // Максимальная высота
                'rotate'      => 30,                        // Угол поворота в градусах
                'background-rotate' => '167, 255, 147',     // Цвет фона после поворота изображения в формате RGB
                'quality'     => 80,                        // Качество изображения (только для jpeg). От 0 до 100
                'mini'        => true,                      // Необходимость добавление миниатюры
                'mini-path'   => '/assets/mini/',           // Путь для загрузки миниатюры
                'mini-name'   => 'mini_{name}',             // Имя для миниатюры, где {name} - это имя оригинального изображения или значение опции 'name'
                'mini-width'  => 50,                        // Максимальная ширина миниатюры (по умолчанию 50% от оригинала)
                'mini-height' => 50                         // Максимальная высота миниатюры (по умолчанию 50% от оригинала)
            ));

            В опциях 'name' и 'mini-name' можно использовать служебную переменную {i}, которая в именах загружаемых изображений заменится на их порядковый номер.
            Особенно актуально при загрузхе массива изображений.
            Например:
                'name'      => 'prefix_{i}_postfix'
                'mini-name' => 'prefix_{i}_{name}_postfix'

            В опциях 'width', 'height', 'mini-width' и 'mini-height' можно использовать как абсолютные значения, так и доли.
            Изменение размеров изображения всегда совершается пропорционально.
            Например:
                'width' => 100                              // Максимальная ширина изображения ограничивается 100px
                'width' => 0.7                              // Изображение будет уменьшено до 70%
                'width' => 1.2                              // Изображение будет увеличено до 120%
                'mini-width' => 50                          // Максимальная ширина миниатюры ограничивается 50px
                'mini-width' => 0.3                         // Миниатюра будет уменьшена до 30% от исходной ширины оригинала
                'mini-width' => 1.5                         // Миниатюра будет увеличина до 150% от исходной ширины оригинала
*/

namespace ten;

class image extends file {

    private static $image_path = '/assets/images/';                              // Директория по умолчанию для загрузки изображений

    private static $path_array = array();                                        // Массив для вывода путей загруженных изображений

    public  static $image_size = 4;                                              // Вес загружаемого изображения в мегабайтах
    public  static $image_type = array('gif', 'png', 'jpeg');                    // Допустимые типы файлов для изображений

    public  static $debug = false;                                               // Флаг отладки скрипта для вывода ошибок

    /**
     * Конструктор
     *
     */
    private function __construct() {

        self::$image_size *= 1024 * 1024;                                        // Перевод мегабайтов в байты
    }

    /**
     * Функция загрузки изображения
     *
     * @param array $files   Массив $_FILES с необходимым файлом
     * @param array $options Массив дополнительных параметров (path, width, height)
     * @return mixed
     */
    public static function upload($files, $options = array()) {

        new image;                                                               // Создание объекта для запуска конструктора, чтобы перевести допустимые размеры загружаемого изображения в байты

        $files_array = array();                                                  // Массив загружаемых изображений

        foreach($files as $key => $val_arr)                                      // Цикл по массиву $_FILES
            if(gettype($val_arr) == 'array') {                                   // Если текущий элемент массива $_FILES является массивом
                foreach($val_arr as $val_num => $val_val)                        // то по нему нужно пройтись
                    if($files['size'][$val_num] > 0)                             // Если размер текущего элемента больше нуля, то есть файл существует
                        $files_array[$val_num][$key] = $val_val;                 // его нужно добавить в новый массив изображений для загрузки
            }
            else {                                                               // Иначе текущий элемент не массив, то есть загружается одно изображение

                $files_array[0] = $files;                                        // Массив изображений для загрузки будет состоять из одного элемента
                break;                                                           // Выход из цикла по массиву $_FILES
            }

        if($options['mini']) {                                                   // Если требуется загрузка миниатюр

            $mini_options = $options;                                            // Переприсваивание массива опций

            unset($mini_options['mini']);                                        // Удаление элемента, сообщающего о необходимости добавления миниатюр
            $mini_options['mini-upload'] = true;                                 // Добавление элемента, символизирующего о загрузке миниатюр

            if(isset($options['mini-path']))                                     // Если задан путь для загрузки миниатюры
                $mini_options['path']   = $options['mini-path'];                 // его нужно переприсвоить

            if(isset($options['mini-name'])) {                                   // Если задано имя для миниатюры

                if(isset($options['name']))                                      // Если задано имя для оригинала
                    $name = str_replace('{name}', $options['name'], $options['mini-name']);     // Замена служебной переменной на имя оригинала
                else                                                             // Иначе для оригинала имя не задано
                    $name = $options['mini-name'];                               // тогда имя для миниатюры нужно просто переприсвоить

                $mini_options['name']   = $name;                                 // Присваивание полученного имени для имени миниатюры
            }
            else                                                                 // Иначе имя для миниатюры не задано
                $mini_options['name']   = 'mini_' . $options['name'];            // Тогда формируется стандартное имя для миниатюры

            if(isset($options['mini-width']))                                    // Если задана ширина для миниатюры
                $mini_options['width']  = $options['mini-width'];                // то её надо переприсвоить
            else                                                                 // Иначе ширина для миниатюры не задана
                $mini_options['width']  = 0.5;                                   // тогда ширина для миниатюры по умолчанию должна быть равна половине от оригинала

            if(isset($options['mini-height']))                                   // Если задана высота для миниатюры
                $mini_options['height'] = $options['mini-height'];               // то её надо переприсвоить
            else                                                                 // Иначе высота для миниатюры не задана
                $mini_options['height'] = 0.5;                                   // тогда высота для миниатюры по умолчанию должна быть равна половине от оригинала

            self::$path_array['originals']  = array();                           // Создание массива в массиве для путей    к оригинальным изображениям
            self::$path_array['miniatures'] = array();                           // Создание массива в массиве для путей    к миниатюрам изображений
        }

        foreach($files_array as $num => $file) {

            if(empty($options['path']))                                          // Если путь не указан
                $options['path'] = self::$image_path;                            // то нужно использовать умолчания
            else if(substr($options['path'], -1) != '/')                         // Иначе если в конце забыт слеш
                $options['path'] .= '/';                                         // его нужно добавить

            if(empty($options['name'])) {                                        // Если новое имя для файла не указано
                $name = substr($file['name'], 0, strripos($file['name'], '.'));  // то используется первоначальное, но без расширения
                if($options['mini'])                                             // Если требуется загрузка миниатюр
                    $mini_options['original-name'][$num] = $name;                // то нужно сохранить имя оригинала
            }
            else                                                                 // Иначе имя указано
                $name = $options['name'];                                        // и его надо просто переприсвоить

            $name = str_replace('%', '', $name);                                 // Удаление символов процента из имени файла
            $name = str_replace('{i}', $num, $name);                             // Замена служебной переменной итерации на порядковый номер изображения
            $name = str_replace('{name}', $options['original-name'][$num], $name);   // Замена служебной переменной имени оригинала на соответствующее имя

            preg_match('/^image\/(.*)$/', $file['type'], $type);                 // Регулярное выражение на определение типа контента

            if(!$type[1] || !in_array($type[1], self::$image_type)) {            // Если текущий файл не изображение или если изображение, но имеет не поддерживаемый тип

                if(self::$debug)                                                 // Если включена отладка
                    message::error('<b>Upload error:</b> Bad image type!');

                return -1;                                                       // Ошибка -1: Неверный тип файла
            }

            if($file['size'] > self::$image_size) {                              // Если размер загружаемого файла не соответствует ограничению

                if(self::$debug)                                                 // Если включена отладка
                    message::error('<b>Upload error:</b> Big image size, maximum = ' . (self::$image_size / 1024 / 1024) . ' megabites (' . self::$image_size . ' bytes). And your file size = ' . $file['size'] . ' bytes');

                return -2;                                                       // Ошибка -2: Слишком большой размер загружаемого файла
            }

            $image_info = getimagesize($file['tmp_name']);                       // Массив информации о загружаемом изображении

            if(empty($image_info)) {

                if(self::$debug)                                                 // Если включена отладка
                    message::error('<b>Upload error:</b> can\'t read information about file');

                return -3;                                                       // Ошибка -3: Отсутствует информация по изображению (скорее всего это не изображение)
            }

            $types = array(                                                      // Массив типов изображений в соответствии с возвращаемыми флагами функции getimagesize()
                1 => 'gif', 2 => 'jpg', 3 => 'png', 4 => 'swf',
                5 => 'psd', 6 => 'bmp', 7 => 'tiff', 8 => 'tiff',
                9 => 'jpc', 10 => 'jp2', 11 => 'jpx'
            );

            self::make_dir($options['path']);                                    // Создание пути, если его не существует

            $func_imagecreatefrom = 'imagecreatefrom' . $type[1];                // Создание имени функции в соответствии с типом изображения
            $base_image = $func_imagecreatefrom($file['tmp_name']);              // Создание нового изображения из добавленного файла

            if(!empty($options['rotate'])) {                                     // Если указана опция поворота изображения

                if(empty($options['background-rotate']))                         // Если не указан цвет для фона после поворота изображения
                    $bckg_color = -1;                                            // то устанавливается прозрачный цвет
                else {                                                           // Иначе цвет указан

                    $bckg_colors = explode(',', $options['background-rotate']);

                    $background_color['red']   = intval($bckg_colors[0]);
                    $background_color['green'] = intval($bckg_colors[1]);
                    $background_color['blue']  = intval($bckg_colors[2]);

                    $bckg_color = imagecolorallocate(                            // Задание цвета для фона после поворота изображения
                        $base_image,
                        $background_color['red'],
                        $background_color['green'],
                        $background_color['blue']
                    );
                }

                $base_image = imagerotate($base_image, $options['rotate'], $bckg_color);
            }

            $base_width  = imagesx($base_image);                                 // Определение ширины базового изображения
            $base_height = imagesy($base_image);                                 // Определение высоты базового изображения

            if(!empty($options['width']) || !empty($options['height'])) {        // Если задан какой-либо параметр размеров

                if(empty($options['width']))                                     // Если опция ширины не задана
                    $width = $base_width;                                        // то ширина остаётся базовой
                else if(gettype($options['width']) == 'double')                  // Иначе ширина задана и является дробным числом
                    $width = round($base_width * $options['width']);             // тогда ширина высчитывается в соответствии указанной доле
                else                                                             // Иначе ширина задана конкретно
                    $width = $options['width'];                                  // тогда нужно её просто переприсвоить

                if(empty($options['height']))                                    // Если опция высоты не задана
                    $height = $base_height;                                      // то высота остаётся базовой
                else if(gettype($options['height']) == 'double')                 // Иначе высота задана и является дробным числом
                    $height = round($base_height * $options['height']);          // тогда высота высчитывается в соответствии указанной доле
                else                                                             // Иначе высота задана конкретно
                    $height = $options['height'];                                // тогда нужно её просто переприсвоить

                if($base_width != $width) {                                      // Если ширина изображения не соответствует новой ширине

                    $ratio  = $base_width / $width;                              // Высчитывается соотношение сторон
                    $width  = round($base_width  / $ratio);                      // Задаётся новая ширина
                    $height = round($base_height / $ratio);                      // Задаётся новая высота
                }
                else if($base_height != $height) {                               // Если высота изображения не соответствует новой высоте

                    $ratio  = $base_height / $height;                            // Высчитывается соотношение сторон
                    $width  = round($base_width  / $ratio);                      // Задаётся новая ширина
                    $height = round($base_height / $ratio);                      // Задаётся новая высота
                }

                $new_image = imagecreatetruecolor(                               // Создание нового пустого изображения
                    $width, $height
                );

                imagealphablending($new_image, false);                           // Устанавливается режим смешивания для изображения
                imagesavealpha($new_image, true);                                // Устанавливается сохранение альфа-канала

                imagecopyresampled(                                              // Копирование старого изображения в новое с изменением параметров
                    $new_image, $base_image,
                    0, 0, 0, 0,
                    $width, $height, $base_width, $base_height
                );
            }
            else {

                $new_image = imagecreatetruecolor(                               // Создание нового пустого изображения
                    $base_width, $base_height
                );

                imagealphablending($new_image, false);                           // Устанавливается режим смешивания для изображения
                imagesavealpha($new_image, true);                                // Устанавливается сохранение альфа-канала

                imagecopy(                                                       // Иначе масштабировать изображение не требуется и нужно просто его скопировать
                    $new_image, $base_image,
                    0, 0, 0, 0,
                    $base_width, $base_height
                );
            }

            if(empty($options['convert'])) {                                     // Если не указан тип конечного изображения

                $extension = $types[$image_info[2]];
                $image_type = $type[1];                                          // то используется первоначальный тип
            }
            else {                                                               // Иначе конечный тип указан

                $extension  = $options['convert'];                               // и нужно использовать его
                $image_type = $options['convert'];
            }

            $path = ROOT . $options['path'] . $name . '.' . $extension;          // Полный путь к загружаемому изображению

            $func_image = 'image' . $image_type;                                 // Создание имени функции в соответствии с типом изображения

            if($image_type == 'jpeg') {                                          // Если тип изображения jpeg

                if(empty($options['quality']))                                   // Если опция качества не указана
                    $options['quality'] = 100;                                   // то качество должно быть наилучшим

                $result = $func_image($new_image, $path, $options['quality']);   // то нужно применить параметр качества
            }
            else                                                                 // Иначе тип изображения любой другой
                $result = $func_image($new_image, $path);                        // и параметр качества применить невозможно

            imagedestroy($base_image);                                           // Удаление из памяти базового изображения
            imagedestroy($new_image);                                            // Удаление из памяти добавленного изображения

            if($result) {                                                        // Если файл загружен

                if($options['mini'])                                             // Если требуется загрузка миниатюр
                    array_push(self::$path_array['originals'], $path);           // то сейчас загружаются оригиналы и нужно пополнить в массив путей
                else if($options['mini-upload'])                                 // Иначе если сейчас загружаются миниатюры
                    array_push(self::$path_array['miniatures'], $path);          // нужно добавить путь в подмассив миниатюр
                else                                                             // Иначе загрузка миниатюр не требуется
                    array_push(self::$path_array, $path);                        // и нужно просто добавить путь в массив путей

                if(count($files_array) == 1) {                                   // Если требуется загрузить всего один файл

                    if($options['mini'])                                         // Если требуется загрузка миниатюр
                        return self::upload($files, $mini_options);              // Рекурсивный вызов функции для загрузки миниатюр

                    if(count(self::$path_array) > 1) {                           // Если было загружено больше одного изображения

                        if(self::$debug)                                         // Если включена отладка
                            message::log('Upload images array is complete');

                        return self::$path_array;                                // Возвращение массива путей
                    }
                    else {

                        if(self::$debug)                                         // Если включена отладка
                            message::log('Upload complete to <b>' . $path . '</b>');

                        return $path;                                            // Функция возвращает путь к загруженному файлу
                    }
                }
            }
            else {                                                               // Иначе файл не загрузился

                if(self::$debug)                                                 // Если включена отладка
                    message::error('<b>Upload error:</b> ' . $path);

                return false;                                                    // Неизвестная ошибка
            }
        }
        // Если программа попадает сюда, то был успешно
        // загружен массив файлов
        if($options['mini'])                                                     // Если требуется загрузка миниатюр
            return self::upload($files, $mini_options);                          // Рекурсивный вызов функции для загрузки миниатюр

        if(self::$debug)                                                         // Если включена отладка
            message::log('Upload images array is complete');

        return self::$path_array;                                                // Функция возвращает массив путей к загруженным файлам
    }
}