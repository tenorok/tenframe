<?php

$menu = array(

	array(
		'name' => 'page1',					// Имя страницы
		'text' => 'Страница 1',				// Текст ссылки
		'href' => '/',						// Адрес (Пример: domen.com/admin/page1/)

		'tabs' => array(					// Вкладки страницы
			
			array(
				'name' => 'tab1',			// Имя вкладки
				'text' => 'Вкладка 1',		// Текст ссылки
				'href' => '/tab1/'			// Адрес (Пример: domen.com/admin/page1/tab1/)
			),
            array(
                'name' => 'tab2',           // Имя вкладки
                'text' => 'Вкладка 2',      // Текст ссылки
                'href' => '/tab2/'          // Адрес (Пример: domen.com/admin/page1/tab1/)
            )
		)
	),
	array(
		'name' => 'page2',					// Имя страницы
		'text' => 'Страница 2',				// Текст ссылки
		'href' => '/page2/',				// Адрес (Пример: domen.com/admin/page1/)
	)
);