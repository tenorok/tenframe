<?php

// Отображение административной панели

namespace ten\mod\admin\ctr;
use ten\mod\admin\mod as mod;

class page {

    /**
     * Формирование страницы авторизации
     *
     */
    public static function page() {

        $urlParams = \ten\route::url();
        $page = isset($urlParams->page) ? $urlParams->page : null;
        $tab = isset($urlParams->tab) ? $urlParams->tab : '';

        $settings = \ten\core::requireFile('/mod/admin/conf/settings.php');

        $admin_info = mod\auth::get_admin_info();                        // Получение данных об администраторе

        if($admin_info) {                                                // Если администратор авторизован

            if(!$page) {                                                 // Если страница не указана

                $index = explode('/', \ten\text::del(                    // Нужно открыть главную страницу
                    $settings['urls']['index'], '/'                      // указанную в настройках
                ));

                $page = $index[0];
                $tab = (isset($index[1])) ? $index[1] : '';              // Если в настройках задана подстраница
            }

            self::view_page(                                             // Отображение главной страницы административной панели
                $admin_info['login'], $page, $tab
            );
        }
        else {                                                           // Иначе не авторизован

            echo auth::view_auth();                                      // Отображение формы авторизации
            unset($_SESSION['mod_admin_auth_logon']);                    // Удаление переменной с результатом авторизации
        }
    }

    /**
     * Отображение главной страницы административной панели
     *
     */
    private static function view_page($login, $page, $tab) {

        $settings = \ten\core::requireFile('/mod/admin/conf/settings.php');

        $content = mod\content::get_content($page, $tab);                // Получение массива наполнения текущей страницы

        echo \ten\tpl::block(array(                                      // Парсинг всей страницы

            'block' => 'html',

            'parse' => array(

                'title' => 'Административная панель &mdash; ' . $content['title'],
                'files' => \ten\statical::includes('libs, require', GEN),

                'body'  => \ten\tpl::block(array(

                    'mod'   => 'admin',
                    'block' => 'page',

                    'parse' => array(

                        'header' => \ten\tpl::block(array(

                            'mod'   => 'admin',
                            'block' => 'header',

                            'parse' => array(
                                'login'  => $login,
                                'action' => \ten\text::rgum($settings['urls']['page'], '/') . 'quit/'
                            )
                        )),

                        'menu' => \ten\tpl::block(array(

                            'mod'   => 'admin',
                            'block' => 'menu',

                            'context' => array(

                                'items' => array(

                                    'array' => mod\menu::get_menu($page, $tab),

                                    'deactive' => array(

                                        '!if'   => 'active',
                                        'parse' => array(
                                            'href'   => 'href',
                                            'title'  => 'title'
                                        )
                                    ),

                                    'active' => array(

                                        'if' => 'active',
                                        'parse' => array(
                                            'title' => 'title'
                                        )
                                    ),

                                    'sub' => array(

                                        'if' => 'tabs',

                                        'subitems' => array(

                                            'array' => 'tabs',

                                            'deactive' => array(

                                                '!if'   => 'active',
                                                'parse' => array(
                                                    'href'   => 'href',
                                                    'title'  => 'title'
                                                )
                                            ),

                                            'active' => array(

                                                'if'   => 'active',
                                                'parse' => array(
                                                    'title' => 'title'
                                                )
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
                                'title'   => $content['title'],
                                'content' => $content['content']
                            )
                        ))
                    )
                ))
            )
        ));
    }
}