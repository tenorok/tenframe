# Модуль &mdash; Панель администратора

## Возможности
- [Авторизация](#auth)
- [Создание ролей](#roles)
- [Создание пользователей](#users)
- [Создание страниц и вкладок](#pages)


## Применение

### <a id="auth"></a>Авторизация

Файл `/conf/settings.php`

	'urls' => array(
		'auth' => '/admin/'							// Адрес страницы авторизации
	)

Получение информации

	$info = mod_admin_m_auth::get_admin_info();		// Получить данные авторизованного администратора

	$info:
		
		array(										// Если администратор авторизован
			'role'     => 'admin'
			'login'    => 'login',
			'password' => 'pass'
		
		) || false									// Иначе не авторизован

### <a id="roles"></a>Создание ролей

Файл `/conf/roles.php`

	$roles = array(
		
		array(
			'name'  => 'admin',						// Обязательный. Имя роли
			
			'pages' => array(						// Ограниченный доступ к страницам панели
				'page1',							// Доступ ко всей странице
				'page2' => array('tab1', 'tab2')	// Доступ к определённым вкладкам на странице
			)
		)
	);


### <a id="users"></a>Создание пользователей

Файл `/conf/users.php`

	$users = array(
		
		array(
			'role'     => 'admin'					// Роль
			'login'    => 'login',					// Логин
			'password' => 'pass'					// Пароль
		)
	);


### <a id="pages"></a>Создание меню и подменю

Файл `/conf/menu.php`

	$menu = array(

		array(
			'name' => 'page1',						// Имя страницы (нужно ли???)
			'text' => 'Страница 1',					// Текст ссылки
			'href' => '/page1/',					// Адрес (Пример: domen.com/admin/page1/)
			
			'tabs' => array(						// Вкладки страницы
				
				array(
					'name' => 'tab1',				// Имя вкладки
					'text' => 'Вкладка 1',			// Текст ссылки
					'href' => '/tab1/'				// Адрес (Пример: domen.com/admin/page1/tab1/)
				)
			)
		)
	);