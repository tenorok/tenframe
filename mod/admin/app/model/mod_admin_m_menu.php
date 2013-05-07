<?php

// Отображение меню административной панели

class mod_admin_m_menu {

    private static $menu_conf = null;                                                    // Переменная для хранения файла настроек меню

    /**
     * Получение файла настроек меню
     *
     * @return array
     */
    public static function get_menu_conf() {

        if(is_null(mod_admin_m_menu::$menu_conf)) {                                      // Если конфигурационный файл ещё не был получен

            require ROOT . '/mod/admin/conf/menu.php';                                   // Подключение файла
            mod_admin_m_menu::$menu_conf = $menu;                                        // Присваивание его содержания
        }

        return mod_admin_m_menu::$menu_conf;                                             // Возвращение содержания конфигурационного файла
    }

    /**
     * Получение блока меню
     *
     * @param  string $page Адрес страницы
     * @param  string $tab  Адрес подстраницы
     * @return array
     */
    public static function get_menu($page = null, $tab = null) {

        require ROOT . '/mod/admin/conf/settings.php';

        $menu = mod_admin_m_menu::get_menu_conf();

        foreach($menu as $key => $item) {                                                // Цикл по элементам меню

            $main_url = ten\text::rgum($settings['urls']['page'], '/');                  // Адрес главной страницы административной панели

            $menuInfo = $menu[$key];                                                     // Заведение информационной переменной для удобства

            if(!mod_admin_m_menu::get_access($menuInfo['name'])) {                       // Если администратор не имеет доступ к текущей странице

                unset($menu[$key]);                                                      // то страница удаляется
                continue;                                                                // и нужно перейти к следующей странице
            }

            $menu[$key]['active'] = (                                                    // Задание активного класса
                ten\text::del($page . '/' . $tab, '/') == $item['name']                  // Если текущий адрес соответствует адресу ссылки меню
            ) ? ' mod-admin-menu__item_active' : '';

            if(isset($menuInfo['tabs'])) {                                               // Если у меню существует подменю

                foreach($menuInfo['tabs'] as $i => $curTab) {                            // Цикл по подменю

                    $tabInfo = $menuInfo['tabs'][$i];                                    // Заведение информационной переменной для удобства

                    if(!mod_admin_m_menu::get_access(                                    // Если администратор не имеет доступ к текущей подстранице
                        $menuInfo['name'], $tabInfo['name']
                    )) {

                        unset($menu[$key]['tabs'][$i]);                                  // то подстраница удаляется
                        continue;                                                        // и нужно перейти к следующей подстранице
                    }

                    $pageAndTab = $menuInfo['name'] . '/' . $tabInfo['name'];

                    $menu[$key]['tabs'][$i]['active'] = (                                // Задание активного класса
                        ten\text::del($page . '/' . $tab, '/') == $pageAndTab            // Если текущий адрес соответствует адресу ссылки подменю
                    ) ? ' mod-admin-menu__item_active' : '';

                    $menu[$key]['tabs'][$i]['href'] = $main_url . $pageAndTab;           // Изменение адреса ссылки подменю
                }
            }

            $menu[$key]['href'] =                                                        // Изменение адреса ссылки меню
                $main_url .                                                              // Прибавление адреса главной страницы в начало ссылки
                $menuInfo['name'];
        }

        return $menu;
    }

    /**
     * Проверка доступа к странице или подстранице
     *
     * @param  array         $roleInfo Массив параметров роли авторизованного администратора
     * @param  string        $page     Имя страницы для проверки
     * @param  string | null $tab      Имя подстраницы для проверки
     * @return boolean
     */
    public static function get_access($page, $tab = null) {

        $roleInfo = mod_admin_m_auth::get_role_info();                                   // Получение информации о роли текущего администратора

        if(!$roleInfo)                                                                   // Если информация о роли не была получена
            return false;                                                                // значит такой роли нет

        if(!isset($roleInfo['pages']))                                                   // Если в информации администратора не указаны доступные страницы
            return true;                                                                 // значит ему доступны все страницы

        foreach($roleInfo['pages'] as $curPage => $curTabs) {                            // Цикл по страницам, доступным для роли

            if(
                gettype($curTabs) == 'string' &&                                         // Если страница указана без табов
                $curTabs == $page                                                        // и она совпадает с переданной для проверки
            )
                return true;                                                             // Значит администратор имеет к ней доступ

            else if(
                gettype($curTabs) == 'array'  &&                                         // Иначе если страница указана с табами
                $curPage == $page                                                        // и она совпадает с переданной для проверки
            ) {

                if(!$tab)                                                                // Если не нужно проверять подстраницу
                    return true;                                                         // администратор имеет доступ к странице

                else {                                                                   // Иначе нужно проверить подстраницу

                    foreach($curTabs as $curTab)                                         // Цикл по подстраница текущей страницы, доступным для роли
                        if($curTab == $tab)                                              // Если подстраница совпадает с переданной для проверки
                            return true;                                                 // значит администратор имеет к ней доступ
                }
            }
        }

        return false;                                                                    // Не найдено соответствий, значит нет доступа
    }
}