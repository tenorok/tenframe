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

        foreach(parent::$settings['modules'] as $mod) {                // Цикл по перечисленным именам модулей
            $path = TEN_MODULES . $mod;                                // Относительный путь к модулю
            array_push(join::$input_path, $path . '/view/');           // Добавление пути к представлениям модуля для объединения файлов
            parent::requireFile($path . '/init.php');                  // Подключение файла инициализации модуля
        }
    }

    /**
     * Функция отображения readme модулей
     */
    public static function readme() {

        // TODO: Подключать в Composer
        require ROOT . '/assets/php/markdown.php';

        $mod = route::url()->mod;

        echo tpl::block(array(

            'block' => 'html',

            'parse' => array(

                'title' => 'Модуль — ' . $mod,
                'files' => statical::includes('markdown', GEN),
                'body'  => Markdown(file_get_contents(parent::resolveRealPath('/mod/', $mod, '/readme.md')))
            )
        ));
    }
}
