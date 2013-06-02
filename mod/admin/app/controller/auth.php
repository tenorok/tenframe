<?php

// Авторизация в административной панели

class mod_admin_auth {

    /**
     * Отображение формы авторизации
     *
     */
    public static function view_auth() {

        require ROOT . '/mod/admin/conf/settings.php';

        return ten\tpl::block(array(

            'block' => 'html',

            'parse' => array(

                'title' => 'Вход в административную панель',
                'files' => ten\statical::includes('libs, developer, require', GEN),

                'body'  => ten\tpl::block(array(

                    'mod'   => 'admin',
                    'block' => 'logon',

                    'parse' => array(
                        'action' => ten\text::rgum($settings['urls']['page'], '/') . 'auth/',
                        'error'  => (isset($_SESSION['mod_admin_auth_logon']) && !$_SESSION['mod_admin_auth_logon']) ? 'Неверный логин или пароль' : ''
                    )
                ))
            )
        ));
    }

    /**
     * Выполнение авторизации
     *
     */
    public static function auth() {

        mod_admin_m_auth::auth();

        require ROOT . '/mod/admin/conf/settings.php';

        header('location: ' . $settings['urls']['page']);
    }

    /**
     * Выполнение выхода
     *
     */
    public static function quit() {

        mod_admin_m_auth::quit();

        require ROOT . '/mod/admin/conf/settings.php';

        header('location: ' . $settings['urls']['page']);
    }
}