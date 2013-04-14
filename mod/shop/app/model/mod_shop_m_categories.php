<?php

// Работа с категориями магазина

class mod_shop_m_categories {

    /**
     * Получение списка категорий с учётом вложенностей
     * 
     * @param string $page Адрес главной страницы административной панели
     * @return string
     */
    public static function get_categories_list($page) {

        $categories = mod_shop_m_categories::get_categories_query($page);                // Получение списка категорий

        $items = '';                                                                     // Переменная для вывода категорий

        if(!empty($categories)) {                                                        // Если категории существуют

            foreach($categories as $category) {                                          // Цикл по списку категорий

                if(!$category->tmod_shop_categories_fk) {                                // Если категория не имеет родителя

                    $items .= mod_shop_m_categories::parse_category_item(                // Парсинг блока категории
                        $page,
                        $category->tmod_shop_categories_id,
                        $category->name,
                        (isset($category->parent)) ? $category->parent : '',
                        (bool) $category->hide
                    );

                    $items = mod_shop_m_categories::get_category(                        // Получение дочерних категорий
                        $page,
                        $categories,
                        $category->tmod_shop_categories_id,
                        $items
                    );
                }
            }
        }

        return preg_replace('/\[\[child_\d+\]\]/', '', $items);                          // Возвращение результата с удалением переменных шаблона
    }

    /**
     * Рекурсивная функция парсинга подкатегорий
     * 
     * @param string  $page       Адрес главной страницы административной панели
     * @param array   $categories Массив всех категорий
     * @param integer $current    Идентификатор текущей категории
     * @param string  $template   Шаблон для парсинга
     * @return string
     */
    private static function get_category($page, $categories, $current, $template) {

        foreach($categories as $category) {                                              // Цикл по списку категорий

            if($category->tmod_shop_categories_fk == $current) {                         // Если категория является дочерней для текущей

                $item = mod_shop_m_categories::parse_category_item(                      // Парсинг блока категории
                    $page,
                    $category->tmod_shop_categories_id,
                    $category->name,
                    (isset($category->parent)) ? $category->parent : '',
                    (bool) $category->hide
                );

                $child_tmp = '[[child_' . $current . ']]';                               // Генерация переменной шаблона

                $template = str_replace($child_tmp, $item . $child_tmp, $template);      // Замена переменной шаблона на дочерний блок категории

                $template = mod_shop_m_categories::get_category(                         // Рекурсивный вызов функции для получения дочерних категорий
                    $page,
                    $categories,
                    $category->tmod_shop_categories_id,
                    $template
                );
            }
        }

        return $template;                                                                // Возвращение готового шаблона
    }

    /**
     * Парсинг шаблона элемента категории
     * 
     * @param string   $page    Имя главной страницы административной панели
     * @param integer  $id      Идентификатор категории
     * @param string   $name    Название категории
     * @param boolean  $parent  Имя класса для выделения родительской категории
     * @param boolean  $hidden  Скрытая/видимая категория
     * @return string
     */
    private static function parse_category_item($page, $id, $name, $parent, $hidden) {

        if((int) $page > 0)                                                              // Если вместо адреса страницы админки передан идентификатор категории
            return core::block(array(                                                    // Значит нужно парсить список категорий для изменения родительской категории

                'mod'   => 'shop',
                'block' => 'categories',
                'view'  => 'parent',

                'context' => array(

                    'item' => array(
                        '!if' => $parent,
                        'parse' => array(
                            'id'   => $id,
                            'name' => $name
                        )
                    ),

                    'parent' => array(
                        'if' => $parent,
                        'parse' => array(
                            'id'   => $id,
                            'name' => $name
                        )
                    )
                ),

                'parse' => array(
                    'id'       => $id
                )
            ));
        else                                                                             // Иначе нужно парсить список категорий для главной страницы категорий
            return core::block(array(

                'mod'   => 'shop',
                'block' => 'categories',
                'view'  => 'item',

                'context' => array(

                    'visible' => array(
                        '!if' => $hidden,
                        'parse' => array(
                            'page' => $page,
                            'id'   => $id,
                            'name' => $name
                        )
                    ),

                    'hidden' => array(
                        'if' => $hidden,
                        'parse' => array(
                            'page' => $page,
                            'id'   => $id,
                            'name' => $name
                        )
                    )
                ),

                'parse' => array(
                    'id' => $id
                )
            ));
    }

    /**
     * Формирование массива данных для парсинга формы работы с категорией
     * 
     */
    public static function get_info() {

        $categoryid = (isset(get::$arg->categoryid)) ? get::$arg->categoryid : null;     // Если в адресной строке есть идентификатор категории
        $parentid   = (isset(get::$arg->parentid))   ? get::$arg->parentid   : null;     // Если в адресной строке есть идентификатор родительской категории

        $info = array(                                                                   // Массив возможных полей с дефолтными значениями
            'title'    => '',
            'parentid' => '',
            'action'   => '',
            'name'     => '',
            'alias'    => '',
            'hided'    => ''
        );

        if(!is_null($parentid)) {                                                        // Если задана родительская категория
            
            $info['title']    = 'Добавление подкатегории в &laquo;' . orm::select('tmod_shop_categories')->where($parentid)->name . '&raquo;';
            $info['parentid'] = $parentid;
            $info['action']   = 'insert';
        }
        else if(!is_null($categoryid)) {                                                 // Если задана конкретная категория на изменение
            
            $catinfo = orm::select('tmod_shop_categories')->where($categoryid);

            $info['title']  = 'Изменение категории &laquo;' . $catinfo->name . '&raquo;';
            $info['action'] = 'edit/' . $categoryid;
            $info['name']   = $catinfo->name;
            $info['alias']  = $catinfo->alias;
            $info['hided']  = ($catinfo->hide == 1) ? 'checked' : '';
        }
        else {                                                                           // Иначе просто добавление категории в корень
            
            $info['title']  = 'Добавление новой категории';
            $info['action'] = 'insert';
        }

        return $info;                                                                    // Возврат сформированного массива
    }

    /**
     * Получение списка категорий
     * 
     * @param string $category_id Номер текущей категории
     * @return array
     */
    private static function get_categories_query($category_id) {

        if((int) $category_id > 0) {                                                     // Если вместо адреса страницы админки передан идентификатор категории

            $categories =
                orm::select('tmod_shop_categories')                                      // Запрос на получение всех категорий с подзапросом на определение родителя текущей категории
                    ->sub(array(
                        'select tmod_shop_categories_fk from tmod_shop_categories where tmod_shop_categories_id = ' . $category_id => 'parent'
                    ))
                    ->order('serial')
                    ->where('tmod_shop_categories_id <> ' . $category_id);

            foreach($categories as $category) {                                          // Цикл по полученным категориям

                $category->parent =
                    ($category->tmod_shop_categories_id != $category->parent) ?          // Если категория не является родительской для текущей
                    false :
                    true;                                                                // Иначе категория является родительской для текущей
            }
            
            return $categories;                                                          // Возврат обработанных результатов выборки
        }
        else                                                                             // Иначе передан адрес страницы админки (значит сейчас не страница редактирования категории)
            return 
                $categories =
                    orm::select('tmod_shop_categories')                                  // Получение списка существующих категорий
                        ->order('serial')
                        ->where('all');
    }

    /**
     * Формирование массива полей категории
     * 
     * @param string $category_id Номер текущей категории
     * @return array
     */
    public static function get_fields($category_id) {

        $fields = 
            orm::join('tmod_shop_fields', array(
                array(
                    'table' => 'tmod_shop_values',
                    'join'  => 'left',
                    'on'    => 'tmod_shop_fields.list = 1'
                )
            ))
            ->where('tmod_shop_fields.tmod_shop_categories_fk = ' . $category_id);
        
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
        print_r($fields);

        orm::debug();
    }
}