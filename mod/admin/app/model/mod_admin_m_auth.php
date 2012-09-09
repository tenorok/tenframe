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
				'role'     => $_SESSION['mod_admin_logon_info']['role'],
				'login'    => $_SESSION['mod_admin_logon_info']['login'],
				'password' => $_SESSION['mod_admin_logon_info']['password']
			);
		else
			return false;
	}

	/**
	 * Получение параметров роли
	 * 
	 * @return array | false
	 */
	public static function get_role_info() {

		require ROOT . '/mod/admin/conf/roles.php';

		$admin_info = mod_admin_m_auth::get_admin_info();								// Получение информации об авторизованном администраторе

		foreach($roles as $role)														// Цикл по ролям
			if($role['name'] == $admin_info['role'])									// Если имя роли совпадает с текущим именем роли администратора
				return $role;															// нужно её вернуть
		
		return false;																	// Иначе такой роли нет
	}
}