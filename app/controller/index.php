<?php

// Контроллер работы с главной страницей

class index {

    /**
     * Отображение главной страницы
     *
     */
    public static function page() {

        echo ten\tpl::block(array(

            'block' => 'html',

            'parse' => array(
                'title' => 'Готов?',
                'files' => ten\statical::includes('libs, developer, require', GEN),
                'body'  => 'Поехали!'
            )
        ));
    }
}