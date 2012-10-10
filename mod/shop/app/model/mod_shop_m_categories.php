<?php

// Работа с категориями магазина

class mod_shop_m_categories {
	
	/**
	 * Получение списка категорий с учётом вложенностей
	 * 
	 */
	public static function get_categories_list($page) {

		$categories = orm::select('tmod_shop_categories')->where('all');

		$items = '';

		foreach($categories as $category) {

			if(!$category->tmod_shop_categories_fk) {

				$items .= mod_shop_m_categories::parse_category_item(
					$page,
					$category->tmod_shop_categories_id,
					$category->name
				);

				$items = mod_shop_m_categories::get_category(
					$page,
					$categories,
					$category->tmod_shop_categories_id,
					$items
				);
			}
		}

		return preg_replace('/\[\[child_\d+\]\]/', '', $items);
	}

	/**
	 * Рекурсивная функция парсинга подкатегорий
	 * 
	 * @param array   $categories Массив всех категорий
	 * @param integer $current    Идентификатор текущей категории
	 */
	private static function get_category($page, $categories, $current, $template) {

		foreach($categories as $category) {

			if($category->tmod_shop_categories_fk == $current) {

				$item = mod_shop_m_categories::parse_category_item(
					$page,
					$category->tmod_shop_categories_id,
					$category->name
				);

				$child_tmp = '[[child_' . $current . ']]';

				$template = str_replace($child_tmp, $item . $child_tmp, $template);

				$template = mod_shop_m_categories::get_category(
					$page,
					$categories,
					$category->tmod_shop_categories_id,
					$template
				);
			}
		}

		return $template;
	}

	/**
	 * Парсинг шаблона элемента категории
	 * 
	 * @param string  $page Имя главной страницы административной панели
	 * @param integer $id   Идентификатор категории
	 * @param string  $name Название категории
	 * @return string
	 */
	private static function parse_category_item($page, $id, $name) {

		return core::block(array(

			'mod'   => 'shop',
			'block' => 'categories',
			'view'  => 'item',

			'parse' => array(

				'page' => $page,
				'id'   => $id,
				'name' => $name
			)
		));
	}
}