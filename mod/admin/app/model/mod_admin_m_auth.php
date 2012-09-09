<?php

// Авторизация в административной панели

class mod_admin_m_auth {
	
	/**
	 * Выполнение авторизации
	 * 
	 */
	public static function auth() {

		require ROOT . '/mod/admin/conf/users.php';

		$_SESSION['mod_admin_auth_logon'] = false;					// Заранее предполагается, что авторизация не будет выполнена

		foreach($users as $user) {									// Цикл по записям из /conf/users.php
			
			if(
				$user['login']    == $_POST['login'] &&				// Если логин
				$user['password'] == $_POST['password']				// и пароль совпадают
			) {
				
				$_SESSION['mod_admin_auth_logon'] = true;			// Авторизация выполнена

				$_SESSION['mod_admin_logon_info'] = array(			// В сессию записываются данные об авторизованном администраторе

					'role'     => $user['role'],
					'login'    => $user['login'],
					'password' => $user['password']
				);

				break;
			}
		}
	}

	/**
	 * Выполнение выхода
	 * 
	 */
	public static function quit() {

		unset(
			$_SESSION['mod_admin_auth_logon'],
			$_SESSION['mod_admin_logon_info']
		);
	}

	/**
	 * Проверка на авторизацию пользователя
	 * 
	 * @return mixed
	 */
	public static function get_admin_info() {

		if(isset($_SESSION['mod_admin_logon_info']))
			return array(
				$_SESSION['mod_admin_logon_info']['role'],
				$_SESSION['mod_admin_logon_info']['login'],
				$_SESSION['mod_admin_logon_info']['password']
			);
		else
			return false;
	}

	/**
	 * Получение блока меню
	 * 
	 */
	public static function get_menu($page, $tab) {

		require ROOT . '/mod/admin/conf/settings.php';
		require ROOT . '/mod/admin/conf/menu.php';

		foreach($menu as $key => $item) {												// Цикл по элементам меню

			$main_url = ten_text::rgum($settings['urls']['page'], '/');					// Адрес главной страницы административной панели

			$menuInfo = $menu[$key];													// Заведение информационной переменной для удобства

			$menu[$key]['active'] = (													// Задание активного класса
				
				ten_text::del($page . '/' . $tab, '/') ==								// Если текущий адрес соответствует
				ten_text::del($item['href'], '/')										// адресу ссылки меню
			
			) ? ' mod-admin-menu__item_active' : '';

			if(isset($menuInfo['tabs'])) {												// Если у меню существует подменю
				
				foreach($menuInfo['tabs'] as $i => $curTab) {							// Цикл по подменю
					
					$tabInfo = $menuInfo['tabs'][$i];									// Заведение информационной переменной для удобства

					$menu[$key]['tabs'][$i]['active'] = (								// Задание активного класса

						ten_text::del($page . '/' . $tab, '/') ==						// Если текущий адрес соответствует
						ten_text::del($menuInfo['href'], '/') . '/' . 					// адресу ссылки подменю
						ten_text::del($tabInfo['href'], '/')

					) ? ' mod-admin-menu__item_active' : '';
					
					$menu[$key]['tabs'][$i]['href'] =									// Изменение адреса ссылки подменю
						$main_url .														// Прибавление адреса главной страницы в начало ссылки
						ten_text::ldel($menuInfo['href'], '/') . 						// Прибавление адреса родительского меню
						ten_text::ldel($tabInfo['href'], '/');
				}
			}

			$menu[$key]['href'] =														// Изменение адреса ссылки меню
				$main_url .																// Прибавление адреса главной страницы в начало ссылки
				ten_text::ldel($menuInfo['href'], '/');
		}

		return $menu;
	}
}