<?php

// Контроллер работы с главной страницей

class index {
	
	/**
	 * Отображение главной страницы
	 *
	 */
	public static function page() {

		$html = new Blitz(ROOT . '/view/blocks/html/view/html.tpl');

		echo $html->parse(array(
			'title' => 'Заголовок',
			'body'  => 'Контент'
		));
	}
}