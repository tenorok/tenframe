<?php

// Контроллер работы с главной страницей

class index {

    /**
     * Отображение главной страницы
     *
     */
    public static function page() {

        echo tpl::block(array(

            'block' => 'html',

            'parse' => array(
                'title' => 'Готов?',
                'files' => core::includes('libs, developer, require', '__autogen__'),
                'body'  => 'Поехали!'
            )
        ));
    }
}