<?php

// Авторизация в административной панели

namespace ten\mod\admin\ctr;
use ten\mod\admin\mod as mod;

class auth {

    /**
     * Отображение формы авторизации
     *
     */
    public static function view_auth() {

        $settings = \ten\core::requireFile('/mod/admin/conf/settings.php');

        return \ten\tpl::block(array(

            'block' => 'html',

            'parse' => array(

                'title' => 'Вход в административную панель',
                'files' => \ten\statical::includes('libs, require', GEN),

                'body'  => \ten\tpl::block(array(

                    'mod'   => 'admin',
                    'block' => 'logon',

                    'parse' => array(
                        'action' => \ten\text::rgum($settings['urls']['page'], '/') . 'auth/',
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

        mod\auth::auth();

        $settings = \ten\core::requireFile('/mod/admin/conf/settings.php');

        header('location: ' . $settings['urls']['page']);
    }

    /**
     * Выполнение выхода
     *
     */
    public static function quit() {

        mod\auth::quit();

        $settings = \ten\core::requireFile('/mod/admin/conf/settings.php');

        header('location: ' . $settings['urls']['page']);
    }
}