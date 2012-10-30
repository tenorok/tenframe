routes.push({
	url:  [
		'/admin/',
		'/admin/{page}/',
		'/admin/{page}/{tab}'
	],
	ctrl: 'mod_shop_categories',
	func: 'list'
}, {
	url: [
		'/admin/modshop/categories/add/',
		'/admin/modshop/categories/{parentid}/addcategory/'
	],
	ctrl: 'mod_shop_categories',
	func: 'add'
}, {
	url:  '/admin/modshop/categories/{categoryid}/',
	ctrl: 'mod_shop_categories',
	func: 'edit'
});