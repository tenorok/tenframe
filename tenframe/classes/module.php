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
     */
    public static function init() {

        route::get([
            'url' => '/module/{mod}/',
            'call' => 'ten\module::readme',
            'dev' => true
        ]);

        foreach(parent::$settings['modules'] as $mod) {

            $path = parent::resolveRealPath(TEN_MODULES, $mod);

            list($view, $init, $routes) = array(
                parent::resolveRealPath($path, '/view/'),
                parent::resolveRealPath($path, '/init.php'),
                parent::resolveRealPath($path, '/routes.php')
            );

            array_push(join::$input_path, $view);                       // Добавление пути к представлениям модуля для объединения файлов
            parent::requireFiles($init, $routes);
        }
    }

    /**
     * Функция отображения readme модулей
     */
    public static function readme() {

        $mod = route::url()->mod;

        echo tpl::block(array(

            'block' => 'html',

            'parse' => array(

                'title' => 'Модуль — ' . $mod,
                'files' => statical::includes('markdown', GEN),
                'body'  => markdown::html(file_get_contents(parent::resolveRealPath('/mod/', $mod, '/readme.md')))
            )
        ));
    }
}
