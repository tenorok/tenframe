# Модуль &mdash; Панель администратора

Версия 0.3.3

## Возможности
- [Авторизация](#auth)
- [Создание ролей](#roles)
- [Создание пользователей](#users)
- [Создание страниц и вкладок](#pages)


## Применение

### <a id="auth"></a>Авторизация

Файл `/conf/settings.php`

	'urls' => array(
		'page'  => '/admin/',						// Адрес страницы авторизации
		'index' => '/page/tab/'						// Адрес первой страницы
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
				'page1',							// Доступ ко всей странице со всеми её вкладками
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


### <a id="pages"></a>Создание страниц и вкладок

Файл `/conf/menu.php`

	$menu = array(

		array(
			'name'    => 'page1',					// Имя и адрес страницы (Пример: domen.com/admin/page1/)
			'title'   => 'Страница 1',				// Текст ссылки и заголовок страницы
			'content' => ctrl::method('param'),		// Контент страницы
			
			'tabs' => array(						// Вкладки страницы
				
				array(
					'name'    => 'tab1',			// Имя и адрес вкладки (Пример: domen.com/admin/page1/tab1/)
					'title'   => 'Вкладка 1',		// Текст ссылки
					'content' => 'Контент'			// Контент страницы
				)
			)
		)
	);