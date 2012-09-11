<?php

$menu = array(

	array(
		'name'    => 'page1',					// Имя страницы
		'title'   => 'Страница 1',				// Текст ссылки и заголовок страницы
		'href'    => '/page1/',					// Адрес (Пример: domen.com/admin/page1/)
		'content' => 'Контент',					// Контент страницы
		
		'tabs' => array(						// Вкладки страницы
			
			array(
				'name'    => 'tab1',			// Имя вкладки
				'title'   => 'Вкладка 1',		// Текст ссылки
				'href'    => '/tab1/',			// Адрес (Пример: domen.com/admin/page1/tab1/)
				'content' => 'Контент'			// Контент страницы
			)
		)
	)
);