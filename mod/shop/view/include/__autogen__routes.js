core.addRoute({
	url:  [
		'/admin/',
		'/admin/{page}/',
		'/admin/{page}/{tab}'
	],
	ctrl: 'core.mod.shop.categories.controller',
	func: 'list'
}, {
	url: [
		'/admin/modshop/categories/add/',
		'/admin/modshop/categories/{parentid}/addcategory/'
	],
	ctrl: 'core.mod.shop.categories.controller',
	func: 'add'
}, {
	url:  '/admin/modshop/categories/{categoryid}/',
	ctrl: 'core.mod.shop.categories.controller',
	func: 'edit'
});