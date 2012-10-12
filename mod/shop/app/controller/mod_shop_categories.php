<?php

// Работа с категориями магазина

class mod_shop_categories {
	
	/**
	 * Отображение категорий
	 * 
	 */
	public static function view_categories() {

		require ROOT . '/mod/admin/conf/settings.php';

		$page = ten_text::del($settings['urls']['page'], '/');

		return core::block(array(

			'mod'   => 'shop',
			'block' => 'categories',

			'parse' => array(

				'page'       => $page,
				'categories' => mod_shop_m_categories::get_categories_list($page)
			)
		));
	}

	private static $types = array(										// Типы полей
		
		array(
			'val' => 'varchar',
			'txt' => 'Строка'
		),
		array(
			'val' => 'text',
			'txt' => 'Текст'
		),
		array(
			'val' => 'int',
			'txt' => 'Целое число'
		),
		array(
			'val' => 'float',
			'txt' => 'Дробное число'
		),
		array(
			'val' => 'datetime',
			'txt' => 'Дата и время'
		),
		array(
			'val' => 'image',
			'txt' => 'Изображение'
		),
		array(
			'val' => 'file',
			'txt' => 'Любой файл'
		)
	);

	/**
	 * Проверка доступа к страницам работы с категориями
	 * 
	 */
	private static function get_categories_access() {

		$admin_info = mod_admin_m_auth::get_admin_info();				// Получение данных об администраторе
		
		if(
			!$admin_info || 											// Если администратор не авторизован
			!mod_admin_m_menu::get_access(								// Или если администратор не имеет доступа к текущей странице
				'categories'
			)
		)
			core::not_found();											// то страница не найдена
	}

	/**
	 * Отображение формы создания категории
	 * 
	 * @param integer $parentcat Идентификатор родительской категории
	 */
	public static function add_category_form($parentcat = null) {

		require ROOT . '/mod/admin/conf/settings.php';

		$admin_info = mod_admin_m_auth::get_admin_info();				// Получение данных об администраторе

		mod_shop_categories::get_categories_access();					// Проверка доступа к страницам работы с категориями

		$fieldslist = orm::join('tmod_shop_categories', array(			// Получение списка существующих полей
			array(
				'table' => 'tmod_shop_fields'
			)
		))
		->where('isnull(tmod_shop_fields.tmod_shop_fields_fk)');

		if(!is_null($parentcat))										// Если задана родительская категория
			$title = 'Добавление подкатегории в &laquo;' . orm::select('tmod_shop_categories')->where($parentcat)->name . '&raquo;';
		else
			$title = 'Добавление новой категории';

		echo core::block(array(											// Парсинг всей страницы
			
			'block' => 'html',

			'parse' => array(
				
				'title' => 'Административная панель &mdash; ' . $title,
				'files' => core::includes('libs, developer, require'),
				
				'body'  => core::block(array(
					
					'mod'   => 'admin',
					'block' => 'page',

					'parse' => array(
						
						'header' => core::block(array(

							'mod'   => 'admin',
							'block' => 'header',
							
							'parse' => array(
								'login'  => $admin_info['login'],
								'action' => ten_text::rgum($settings['urls']['page'], '/') . 'quit/'
							)
						)),

						'menu' => core::block(array(

							'mod'   => 'admin',
							'block' => 'menu',

							'context' => array(

								'items' => array(
									
									'array' => mod_admin_m_menu::get_menu(),
									'parse' => array(
										'title'  => 'title',
										'active' => 'active'
									),
									
									'deactive' => array(
										
										'!if'   => 'active',
										'parse' => array(
											'href' => 'href'
										)
									),

									'sub' => array(

										'if'       => 'tabs',

										'subitems' => array(

											'array' => 'tabs',
											
											'deactive' => array(
												
												'!if'   => 'active',
												'parse' => array(
													'href' => 'href'
												)
											),

											'parse' => array(
												'active' => 'active',
												'title'  => 'title'
											)
										)
									)
								)
							)
						)),

						'content' => core::block(array(
							
							'mod'   => 'admin',
							'block' => 'content',
							
							'parse' => array(
								
								'title'   => $title,
								
								'content' => core::block(array(
									'mod'   => 'shop',
									'block' => 'categories',
									'view'  => 'add',

									'parse' => array(
										'page'   => ten_text::del($settings['urls']['page'], '/'),
										'parent' => $parentcat
									),

									'context' => array(

										'existfields' => array(

											'array' => $fieldslist,
											'parse' => array(
												'id'       => 'tmod_shop_fields_id',
												'category' => 'name',
												'field'    => 'tmod_shop_fields_name'
											)
										),
										
										'types' => array(

											'array' => mod_shop_categories::$types,
											'parse' => array(
												'value' => 'val',
												'text'  => 'txt'
											)
										)
									)
								))
							)
						))
					)
				))
			)
		));
	}

	/**
	 * Добавление новой категории
	 * 
	 */
	public static function insert_category() {

		mod_shop_categories::get_categories_access();					// Проверка доступа к страницам работы с категориями
		
		array_pop($_POST['existfield']);								// Удаление последнего элемента подмассива, так как это всегда незполенное поле

		$category_id = orm::insert('tmod_shop_categories', array(		// Добавление записи о категории
			'name'  => $_POST['catname'],
			'alias' => $_POST['catalias'],
			'tmod_shop_categories_fk' => (!empty($_POST['catparent'])) ? $_POST['catparent'] : 'null'
		));

		foreach($_POST['existfield'] as $i => $existfield) {			// Цикл по полям категории

			if(
				$existfield == 'new' &&									// Если нужно создать новое поле
				!empty($_POST['name'][$i])								// у которого заполнено имя
			) {

				$field_id = orm::insert('tmod_shop_fields', array(		// Добавление записи о поле
					'name'  => $_POST['name'][$i],
					'type'  => $_POST['type'][$i],
					'count' => $_POST['count'][$i],
					'tmod_shop_categories_fk' => $category_id
				));

				$options = array_filter(								// Удаление пустых элементов в массиве значений выпадающего списка
					$_POST['options_' . $i],
					'mod_shop_categories::foo'
				);

				foreach($options as $option)							// Цикл по значениям выпадающего списка
					orm::insert('tmod_shop_values', array(				// Добавление записи значения выпадающего списка
						'val_' . $_POST['type'][$i] => $option,
						'tmod_shop_fields_fk' => $field_id
					));
			}
			else if(is_numeric($existfield)) {							// Иначе не нужно создавать новое поле, а привязать уже существующее по его номеру

				orm::insert('tmod_shop_fields', array(					// Добавление записи связанного поля
					'tmod_shop_fields_fk'     => $existfield,
					'tmod_shop_categories_fk' => $category_id
				));
			}
		}

		header('location: /admin/');
	}

	/**
	 * Удаление пустых элементов массива
	 * 
	 * @param string $e Текущий элемент массива
	 * @return boolean
	 */
	private static function foo($e) {

		return (!empty($e));
	}
}