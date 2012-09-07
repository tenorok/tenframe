<?php

// Авторизация в административной панели

class mod_admin_auth {
	
	/**
	 * Формирование страницы авторизации
	 *
	 */
	public static function page($page = '/', $tab = '') {

		if(mod_admin_m_auth::get_admin_info())							// Если администратор авторизован
			mod_admin_auth::view_page($page, $tab);						// Отображение главной страницы административной панели
		
		else {															// Иначе не авторизован

			echo mod_admin_auth::view_auth();							// Отображение формы авторизации
			unset($_SESSION['mod_admin_auth_logon']);					// Удаление переменной с результатом авторизации
		}
	}

	/**
	 * Отображение формы авторизации
	 * 
	 */
	private static function view_auth() {

		require ROOT . '/mod/admin/conf/settings.php';

		return core::block(array(
			
			'block' => 'html',

			'parse' => array(
				
				'title' => 'Заголовок',
				'files' => core::includes('libs, developer, require'),
				
				'body'  => core::block(array(
					
					'mod'   => 'admin',
					'block' => 'logon',

					'parse' => array(
						'action' => ten_text::rgum($settings['urls']['page'], '/') . 'auth/',
						'error'  => (isset($_SESSION['mod_admin_auth_logon']) && !$_SESSION['mod_admin_auth_logon']) ? 'Неверный логин или пароль' : ''
					)
				))
			)
		));
	}

	/**
	 * Отображение главной страницы административной панели
	 * 
	 */
	private static function view_page($page, $tab) {

		require ROOT . '/mod/admin/conf/settings.php';

		echo core::block(array(
			
			'block' => 'html',

			'parse' => array(
				
				'title' => 'Заголовок',
				'files' => core::includes('libs, developer, require'),
				
				'body'  => core::block(array(
					
					'mod'   => 'admin',
					'block' => 'page',

					'parse' => array(
						
						'header' => core::block(array(

							'mod'   => 'admin',
							'block' => 'header',
							
							'parse' => array(

								'action' => ten_text::rgum($settings['urls']['page'], '/') . 'quit/'
							)
						)),

						'menu' => core::block(array(

							'mod'   => 'admin',
							'block' => 'menu',

							'context' => array(

								'items' => array(
									
									'array' => mod_admin_m_auth::get_menu($page, $tab),
									'parse' => array(
										'text'   => 'text',
										'active' => 'active'
									),
									
									'deactive' => array(
										
										'!if'   => 'active',
										'parse' => array(
											'href' => 'href'
										)
									),

									'subitems' => array(

										'array' => 'tabs',
										'parse' => array(
											'href' => 'href',
											'text' => 'text'
										)
									)
								)
							)
						))
					)
				))
			)
		));
	}

	/**
	 * Выполнение авторизации
	 * 
	 */
	public static function auth() {

		mod_admin_m_auth::auth();

		require ROOT . '/mod/admin/conf/settings.php';

		header('location: ' . $settings['urls']['page']);
	}

	/**
	 * Выполнение выхода
	 * 
	 */
	public static function quit() {

		mod_admin_m_auth::quit();

		require ROOT . '/mod/admin/conf/settings.php';

		header('location: ' . $settings['urls']['page']);
	}
}