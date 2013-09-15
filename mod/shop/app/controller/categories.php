<?php

// Работа с категориями магазина

namespace ten\mod\shop\ctr;
use ten\mod\shop\mod as mod;

class categories {

    /**
     * Отображение категорий
     *
     */
    public static function view_categories() {

        $settings = \ten\core::requireFile('/mod/admin/conf/settings.php');

        $page = \ten\text::del($settings['urls']['page'], '/');

        return \ten\tpl::block(array(

            'mod'   => 'shop',
            'block' => 'categories',

            'parse' => array(

                'page'    => $page,

                'listcat' => \ten\tpl::block(array(
                    'mod'   => 'shop',
                    'block' => 'listcat',

                    'parse' => array(
                        'list' => mod\categories::get_categories_list($page)
                    )
                ))
            )
        ));
    }

    private static $types = array(                                         // Типы полей

        array(
            'val' => 'varchar',
            'txt' => 'Строка'
        ),
        array(
            'val' => 'text',
            'txt' => 'Текст'
        ),
        array(
            'val' => 'int',
            'txt' => 'Целое число'
        ),
        array(
            'val' => 'float',
            'txt' => 'Дробное число'
        ),
        array(
            'val' => 'datetime',
            'txt' => 'Дата и время'
        ),
        array(
            'val' => 'image',
            'txt' => 'Изображение'
        ),
        array(
            'val' => 'file',
            'txt' => 'Любой файл'
        )
    );

    /**
     * Проверка доступа к страницам работы с категориями
     *
     */
    private static function get_categories_access() {

        $admin_info = \ten\mod\admin\mod\auth::get_admin_info();           // Получение данных об администраторе

        $pages = \ten\core::requireFile('/mod/shop/conf/pages.php');

        if(
            !$admin_info ||                                                // Если администратор не авторизован
            !\ten\mod\admin\mod\menu::get_access(                          // Или если администратор не имеет доступа к текущей странице
                $pages['categories']
            )
        ) {
            \ten\tpl::not_found();                                         // то страница не найдена
        }
    }

    /**
     * Отображение формы создания категории
     *
     */
    public static function add_category_form() {

        $settings = \ten\core::requireFile('/mod/admin/conf/settings.php');

        $admin_info = \ten\mod\admin\mod\auth::get_admin_info();           // Получение данных об администраторе

        self::get_categories_access();                                     // Проверка доступа к страницам работы с категориями

        \ten\orm::db('tmod_shop');

        $fieldslist = \ten\orm::join('tmod_shop_categories', array(        // Получение списка существующих полей
            array(
                'table' => 'tmod_shop_fields'
            )
        ))->where('isnull(tmod_shop_fields.tmod_shop_fields_fk)');

        $info = mod\categories::get_info();                                // Получение массива с информацией для парсинга

        if(isset(\ten\route::url()->categoryid)) {

            $edit = \ten\tpl::block(array(
                'mod'   => 'shop',
                'block' => 'categories',
                'view'  => 'edit',

                'parse' => array(

                    'hided' => \ten\tpl::block(array(
                        'mod' => 'shop',
                        'block' => 'edit',
                        'view' => 'hided',
                        'parse' => array(
                            'hided' => $info['hided']
                        )
                    )),

                    'change-parent' => \ten\tpl::block(array(
                        'mod' => 'shop',
                        'block' => 'edit',
                        'view' => 'change-parent',
                        'parse' => array(
                            'categories' => mod\categories::get_categories_list(\ten\route::url()->categoryid)
                        )
                    ))
                )
            ));

            mod\categories::get_fields(\ten\route::url()->categoryid);
        }
        else
            $edit = '';

        echo \ten\tpl::block(array(                                        // Парсинг всей страницы

            'block' => 'html',

            'parse' => array(

                'title' => 'Административная панель &mdash; ' . $info['title'],
                'files' => \ten\statical::includes('libs, require', GEN),

                'body'  => \ten\tpl::block(array(

                    'mod'   => 'admin',
                    'block' => 'page',

                    'parse' => array(

                        'header' => \ten\tpl::block(array(

                            'mod'   => 'admin',
                            'block' => 'header',

                            'parse' => array(
                                'login'  => $admin_info['login'],
                                'action' => \ten\text::rgum($settings['urls']['page'], '/') . 'quit/'
                            )
                        )),

                        'menu' => \ten\tpl::block(array(

                            'mod'   => 'admin',
                            'block' => 'menu',

                            'context' => array(

                                'items' => array(

                                    'array' => \ten\mod\admin\mod\menu::get_menu(),
                                    'parse' => array(
                                        'title'  => 'title',
                                        'active' => 'active'
                                    ),

                                    'deactive' => array(

                                        '!if'   => 'active',
                                        'parse' => array(
                                            'href' => 'href'
                                        )
                                    ),

                                    'sub' => array(

                                        'if'       => 'tabs',

                                        'subitems' => array(

                                            'array' => 'tabs',

                                            'deactive' => array(

                                                '!if'   => 'active',
                                                'parse' => array(
                                                    'href' => 'href'
                                                )
                                            ),

                                            'parse' => array(
                                                'active' => 'active',
                                                'title'  => 'title'
                                            )
                                        )
                                    )
                                )
                            )
                        )),

                        'content' => \ten\tpl::block(array(

                            'mod'   => 'admin',
                            'block' => 'content',

                            'parse' => array(

                                'title'   => $info['title'],

                                'content' => \ten\tpl::block(array(
                                    'mod'   => 'shop',
                                    'block' => 'addcat',

                                    'parse' => array(
                                        'page'   => \ten\text::del($settings['urls']['page'], '/'),
                                        'action' => $info['action'],
                                        'parent' => $info['parentid'],
                                        'name'   => $info['name'],
                                        'alias'  => $info['alias'],
                                        'edit'   => $edit,

                                        'fieldlist' => \ten\tpl::block(array(
                                            'mod'   => 'shop',
                                            'block' => 'fieldlist',

                                            'parse' => array(
                                                'fielditem' => \ten\tpl::block(array(
                                                    'mod'   => 'shop',
                                                    'block' => 'fielditem',

                                                    'context' => array(

                                                        'existfields' => array(

                                                            'array' => $fieldslist,
                                                            'parse' => array(
                                                                'id'       => 'tmod_shop_fields_id',
                                                                'category' => 'name',
                                                                'field'    => 'tmod_shop_fields_name'
                                                            )
                                                        ),

                                                        'types' => array(

                                                            'array' => self::$types,
                                                            'parse' => array(
                                                                'value' => 'val',
                                                                'text'  => 'txt'
                                                            )
                                                        )
                                                    )
                                                ))
                                            )
                                        ))
                                    )
                                ))
                            )
                        ))
                    )
                ))
            )
        ));
    }

    /**
     * Добавление новой категории
     *
     */
    public static function insert_category() {

        $settings = \ten\core::requireFile('/mod/admin/conf/settings.php');

        \ten\orm::db('tmod_shop');

        self::get_categories_access();                                     // Проверка доступа к страницам работы с категориями

        array_pop($_POST['existfield']);                                   // Удаление последнего элемента подмассива, так как это всегда незполенное поле

        if(!empty($_POST['catparent'])) {                                  // Если задана родительская категория

            $catparent = $_POST['catparent'];

            $serial =                                                      // Получение сортировочного номера для новой категории
                array_shift(
                    \ten\orm::select('tmod_shop_categories')
                        ->fields('count(*) as serial')
                        ->where('tmod_shop_categories_fk = ' . $catparent)
                )->serial;
        }
        else {                                                             // Иначе добавляется корневая категория

            $catparent = 'null';

            $serial =                                                      // Получение сортировочного номера для новой категории
                array_shift(
                    \ten\orm::select('tmod_shop_categories')
                        ->fields('count(*) as serial')
                        ->where('all')
                )->serial;
        }

        $category_id = \ten\orm::insert('tmod_shop_categories', array(     // Добавление записи о категории
            'name'   => $_POST['catname'],
            'alias'  => $_POST['catalias'],
            'serial' => $serial,
            'tmod_shop_categories_fk' => $catparent
        ));

        foreach($_POST['existfield'] as $i => $existfield) {               // Цикл по полям категории

            if(
                $existfield == 'new' &&                                    // Если нужно создать новое поле
                !empty($_POST['name'][$i])                                 // у которого заполнено имя
            ) {

                $field_id = \ten\orm::insert('tmod_shop_fields', array(    // Добавление записи о поле
                    'name'  => $_POST['name'][$i],
                    'type'  => $_POST['type'][$i],
                    'count' => $_POST['count'][$i],
                    'list'  => $_POST['list'][$i],
                    'tmod_shop_categories_fk' => $category_id
                ));

                $options = array_filter(                                   // Удаление пустых элементов в массиве значений выпадающего списка
                    $_POST['options_' . $i],
                    'mod_shop_categories::foo'
                );

                foreach($options as $option)                               // Цикл по значениям выпадающего списка
                    \ten\orm::insert('tmod_shop_values', array(            // Добавление записи значения выпадающего списка
                        'val_' . $_POST['type'][$i] => $option,
                        'tmod_shop_fields_fk' => $field_id
                    ));
            }
            else if(is_numeric($existfield)) {                             // Иначе не нужно создавать новое поле, а привязать уже существующее по его номеру

                \ten\orm::insert('tmod_shop_fields', array(                // Добавление записи связанного поля
                    'tmod_shop_fields_fk'     => $existfield,
                    'tmod_shop_categories_fk' => $category_id
                ));
            }
        }

        header('location: ' . \ten\text::gum($settings['urls']['page'], '/'));
    }

    /**
     * Удаление пустых элементов массива
     *
     * @param string $e Текущий элемент массива
     * @return boolean
     */
    private static function foo($e) {

        return (!empty($e));
    }

    /**
     * Сортировка категорий
     *
     * @param array $data Принимается $_POST
     */
    public static function sort($data) {
        \ten\orm::db('tmod_shop');
        foreach($data['categories'] as $i => $id) {
            \ten\orm::update('tmod_shop_categories', array(
                'serial' => $i
            ))->where($id);
        }
    }
}