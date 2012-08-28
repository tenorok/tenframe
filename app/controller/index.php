<?php

// Контроллер работы с главной страницей

class index {
	
	/**
	 * Отображение главной страницы
	 *
	 */
	public static function page() {

		echo core::block(array(
			
			'block' => 'html',

			'parse' => array(
				'title' => 'Заголовок',
				'files' => core::includes('libs, developer, require'),
				'body'  => 'Контент'
			)
		));
	}
}