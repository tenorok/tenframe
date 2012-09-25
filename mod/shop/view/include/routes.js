routes.push({
	url:  [
		'/admin/',
		'/admin/{page}/',
		'/admin/{page}/{tab}'
	],
	ctrl: 'mod_shop_categories',
	func: 'init'
});