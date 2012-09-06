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

		foreach($menu as $key => $item) {

			$menu[$key]['active'] = (ten_text::del($page, '/') == ten_text::del($item['href'], '/')) ? ' mod-admin-menu__item_active' : '';
			
			$menu[$key]['href']   =  ten_text::rgum($settings['urls']['page'], '/') . ten_text::ldel($menu[$key]['href'], '/');
		}

		return $menu;
	}
}