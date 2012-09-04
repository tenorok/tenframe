# Модуль для Tenframe &mdash; Admin (Панель администратора)

## Возможности
- Создание ролей
- Создание пользователей
- Авторизация в панели администратора
- Создание страниц
- Создание вкладок


## Применение


### Создание роли

Файл: `/conf/roles.php`

	$roles = array(
		
		array(
			'name' => 'admin'
		)
	);


### Создание пользователей

Файл: `/conf/users.php`

	$users = array(
		
		array(
			'login1'    => 'admin1',
			'password1' => 'admin1'
		),
		array(
			'login2'    => 'admin2',
			'password2' => 'admin2'
		)
	);


### Авторизация

	mod_admin::auth($_POST['login'], $_POST['password']);

### Создание страниц

### Создание вкладок