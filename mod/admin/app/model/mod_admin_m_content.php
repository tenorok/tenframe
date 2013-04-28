<?php

// Отображение меню административной панели

class mod_admin_m_content {

    /**
     * Получение массива контента
     *
     * @param  string $page Адрес страницы
     * @param  string $tab  Адрес подстраницы
     * @return array
     */
    public static function get_content($page, $tab) {

        require ROOT . '/mod/admin/conf/settings.php';

        $menu = mod_admin_m_menu::get_menu_conf();

        $content = array();                                                              // Итоговый массив

        foreach($menu as $key => $item) {                                                // Цикл по элементам меню

            $main_url = txt::rgum($settings['urls']['page'], '/');                       // Адрес главной страницы административной панели

            $menuInfo = $menu[$key];                                                     // Заведение информационной переменной для удобства

            if($menuInfo['name'] == $page) {                                             // Если найдена искомая страница

                if(!mod_admin_m_menu::get_access($menuInfo['name']))                     // Если администратор не имеет доступ к текущей странице
                    break;

                $content['title'] = $menuInfo['title'];                                  // Присваивание заголовка страницы

                if(!empty($tab)) {                                                       // Если требуется отобразить подстраницу

                    foreach($menuInfo['tabs'] as $i => $curTab) {                        // Цикл по подстраницам

                        $tabInfo = $menuInfo['tabs'][$i];                                // Заведение информационной переменной для удобства

                        if($tabInfo['name'] == $tab) {                                   // Если найдена искомая подстраница

                            if(!mod_admin_m_menu::get_access(                            // Если администратор не имеет доступа к текущей подстранице
                                $menuInfo['name'], $tabInfo['name']
                            ))
                                break 2;

                            $content['title']   .= ' &mdash; ' . $tabInfo['title'];      // Добавление заголовка подстраницы
                            $content['content']  =               $tabInfo['content'];    // Присваивание контента подстраницы

                            return $content;
                        }
                    }

                    core::not_found();                                                   // Если не найдена подстраница
                }

                $content['content'] = $menuInfo['content'];                              // Присваивание контента страницы

                return $content;
            }
        }

        core::not_found();                                                               // Если не найдено соответствие или нет прав
    }
}