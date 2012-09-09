<?php

$roles = array(

    array(
        'name'  => 'admin'                      // Обязательный. Имя роли
    ),
    array(
        'name'  => 'manager',                   // Обязательный. Имя роли

        'pages' => array(                       // Ограниченный доступ к страницам панели
            'page1',                            // Доступ ко всей странице
            'page2' => array('tab1', 'tab2')    // Доступ к определённым вкладкам на странице
        )
    ),
    array(
        'name'  => 'developer',

        'pages' => array(
            'page2' => array('tab2', 'tab3'),
            'page3'
        )
    )
);