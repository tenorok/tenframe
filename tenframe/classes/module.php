<?php

/**
 * Работа с модулями фреймворка
 * @version 0.0.1
 */

/* Использование

    Просмотр readme модуля:
        Адрес: domen.com/mod/{modname}/
        В корне модуля должен лежать readme.md

    Инициализация модулей (require.php):
        ten\module::init(array('mod1', 'mod2', ..., 'modN'));

        Инициализация модуля:
            1) добавляет его стили и скрипты в единый объединённый файл
            2) обеспечивает автоподключение вызываемых классов модуля
*/

namespace ten;

class module extends core {

    /**
     * Функция инициализации модулей
     *
     */
    public static function init() {

        array_push(                                                    // Добавление маршрута отображения документации по модулю
            route::$routes,
            array(
                'url'      => '/module/{mod}/',
                'callback' => 'ten\module->readme',
                'dev'      => true                                     // Проводить маршрут только когда включен режим разработчика
            )
        );

        foreach(parent::$settings['modules'] as $mod) {                // Цикл по перечисленным именам модулей

            $path = '/mod/' . $mod;                                    // Относительный путь к модулю

            array_push(join::$input_path, $path . '/view/');           // Добавление пути к представлениям модуля для объединения файлов

            array_push(                                                // Добавление путей для автоподключения файлов модуля
                parent::$paths,
                ROOT . $path . '/app/controller/',
                ROOT . $path . '/app/model/'
            );

            require ROOT . $path . '/init.php';                        // Подключение файла инициализации модуля
        }
    }

    /**
     * Функция отображения readme модулей
     *
     * @param string $mod Имя модуля
     */
    public static function readme($mod) {

        require ROOT . '/assets/php/markdown.php';

        echo tpl::block(array(

            'block' => 'html',

            'parse' => array(

                'title' => 'Модуль — ' . $mod,
                'files' => parent::includes('markdown', file::$autoprefix),
                'body'  => Markdown(file_get_contents(ROOT . '/mod/' . $mod . '/readme.md'))
            )
        ));
    }
}
