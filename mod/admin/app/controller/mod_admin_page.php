<?php

// Отображение административной панели

class mod_admin_page {
    
    /**
     * Формирование страницы авторизации
     *
     */
    public static function page($page = null, $tab = '') {

        require ROOT . '/mod/admin/conf/settings.php';

        $admin_info = mod_admin_m_auth::get_admin_info();                // Получение данных об администраторе
        
        if($admin_info) {                                                // Если администратор авторизован
            
            if(!$page) {                                                 // Если страница не указана
                
                $index = explode('/', ten_text::del(                     // Нужно открыть главную страницу
                    $settings['urls']['index'], '/'                      // указанную в настройках
                ));

                $page = $index[0];
                $tab = (isset($index[1])) ? $index[1] : '';              // Если в настройках задана подстраница
            }

            mod_admin_page::view_page(                                   // Отображение главной страницы административной панели
                $admin_info['login'], $page, $tab
            );
        }
        else {                                                           // Иначе не авторизован

            echo mod_admin_auth::view_auth();                            // Отображение формы авторизации
            unset($_SESSION['mod_admin_auth_logon']);                    // Удаление переменной с результатом авторизации
        }
    }

    /**
     * Отображение главной страницы административной панели
     * 
     */
    private static function view_page($login, $page, $tab) {

        require ROOT . '/mod/admin/conf/settings.php';

        $content = mod_admin_m_content::get_content($page, $tab);        // Получение массива наполнения текущей страницы

        echo core::block(array(                                          // Парсинг всей страницы
            
            'block' => 'html',

            'parse' => array(
                
                'title' => 'Административная панель &mdash; ' . $content['title'],
                'files' => core::includes('libs, developer, require', '__autogen__'),
                
                'body'  => core::block(array(
                    
                    'mod'   => 'admin',
                    'block' => 'page',

                    'parse' => array(
                        
                        'header' => core::block(array(

                            'mod'   => 'admin',
                            'block' => 'header',
                            
                            'parse' => array(
                                'login'  => $login,
                                'action' => ten_text::rgum($settings['urls']['page'], '/') . 'quit/'
                            )
                        )),

                        'menu' => core::block(array(

                            'mod'   => 'admin',
                            'block' => 'menu',

                            'context' => array(

                                'items' => array(
                                    
                                    'array' => mod_admin_m_menu::get_menu($page, $tab),
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

                        'content' => core::block(array(
                            
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