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
		'/admin/modshop/categories/{categoryid}/addcategory/'
	],
	ctrl: 'mod_shop_categories',
	func: 'add'
});