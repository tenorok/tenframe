<?php

// Формирование элемента для добавления в роутинг

require ROOT . '/mod/admin/conf/settings.php';

file_put_contents(ROOT . '/mod/shop/view/include/routes.js', "routes.push({
	url:  [
		'" . $settings['urls']['page'] . "',
		'" . $settings['urls']['page'] . "{page}/',
		'" . $settings['urls']['page'] . "{page}/{tab}'
	],
	ctrl: 'mod_shop_categories',
	func: 'init'
});");

array_push(core::$routes, array(
	
	'url'      => '/admin/modshop/{categories}/add/',
	'callback' => 'mod_shop_categories->add_category'
));