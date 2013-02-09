<?php

require $_SERVER['DOCUMENT_ROOT'] . '/sys/require.php';

switch($_GET['event']) {
    
    case 'sort':                                                 // Сортировка категорий по заданному порядку

        $categories = $_GET['categories'];
        
        foreach($categories as $i => $id) {                      // Цикл по полученному массиву категорий

            orm::update('tmod_shop_categories', array(
                'serial' => $i
            ))->where($id);
        }
        
        break;
}