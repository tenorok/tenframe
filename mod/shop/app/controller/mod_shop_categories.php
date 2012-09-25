<?php

// Работа с категориями магазина

class mod_shop_categories {
	
	/**
	 * Отображение категорий
	 * 
	 */
	public static function view_categories() {

		return core::block(array(

			'mod'   => 'shop',
			'block' => 'categories'
		));
	}

	public static function add_category($pagename) {

		require ROOT . '/mod/admin/conf/settings.php';
		require ROOT . '/mod/admin/conf/roles.php';

		$admin_info = mod_admin_m_auth::get_admin_info();				// Получение данных об администраторе

		if(
			!$admin_info || 											// Если администратор не авторизован
			!mod_admin_m_menu::get_access(								// Или если администратор не имеет доступа к текущей странице
				mod_admin_m_auth::get_role_info(),
				$pagename
			)
		)
			core::not_found();											// то страница не найдена

		echo core::block(array(											// Парсинг всей страницы
			
			'block' => 'html',

			'parse' => array(
				
				'title' => 'Административная панель &mdash; Добавление новой категории',
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
								'title'   => 'Добавление новой категории',
								'content' => ''
							)
						))
					)
				))
			)
		));
	}
}