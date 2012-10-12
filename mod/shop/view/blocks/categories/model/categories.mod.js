// Модель работы с категориями

var mod_shop_m_categories = {

	init: function() {																	// Инициализация работы с категориями

		var obj = mod_shop_m_categories;
		
		$('.mod-shop-categories__cat').click(obj.showDropdown);							// Событие клика по категории

		$('body').click(obj.hideDropdown);												// Событие клика в пустое место

		obj.drawHarr();																	// Рисование необходимых выпадушек
		obj.sortable();																	// Инициализация сортировки категорий
	},

	showDropdown: function() {															// Отображение выпадушки с меню работы над категорией
		
		var position = $(this).position(),
			top      = position.top,
			left     = position.left,
			href     = $(this).attr('href'),
			dropdown = '.mod-shop-categories__dropdown__menu_';

		$('.mod-shop-categories__dropdown').css({										// Изменение положения выпадушки
			'display': 'block',
			'top'    : top  + 29,
			'left'   : left - 8
		});
		
		$(dropdown + 'add-product').attr({												// Изменение адреса ссылки добавления товара
			'href': href + 'addproduct'
		});

		$(dropdown + 'add-category').attr({												// Изменение адреса ссылки добавления подкатегории
			'href': href + 'addcategory'
		});

		$(dropdown + 'edit').attr({														// Изменение адреса ссылки изменения категории
			'href': href
		});

		return false;																	// Отмена перехода по ссылке
	},

	hideDropdown: function() {															// Скрытие выпадушки с меню работы над категорией
		
		$('.mod-shop-categories__dropdown').css({
			'display': 'none'
		});
	},

	drawHarr: function() {																// Отрисовка сворачивающих стрелочек для вложенных списков категорий

		var arrow = '<span class="mod-shop-categories__harr">&#9660;</span>';			// Шаблон стрелочки

		$('.mod-shop-categories__item:has(.mod-shop-categories__item)')					// Если у категории есть подкатегория
			.children('.mod-shop-categories__name')										// то после её имени
			.append(arrow);																// добавляется стрелочка

		$('.mod-shop-categories__harr')													// Добавление события клика по сворачивающим стрелочкам
			.click(mod_shop_m_categories.slideCategoryList);
	},

	slideCategoryList: function() {														// Скрытие / Отображение вложенных категорий

		var arrow = $(this);															// Объект, по которому был произведён клик

		arrow
			.parent()
			.next('.mod-shop-categories__list')											// Получение объекта списка, который нужно скрыть или отобразить
			.slideToggle(200, function() {												// Скрытие и отображение списка
				
				if($(this).is(':hidden'))												// Изменение стрелочки
					arrow.html('&#9650;');
				else
					arrow.html('&#9660;');
			});
	},

	sortable: function() {																// Сортировка категорий
		
		$('.mod-shop-categories__list')
			.disableSelection()
			.sortable({
				start: function(e, ui) {
					$(ui.placeholder).hide(300);										// Добавление плавной анимации
				},
				change: function(e, ui) {
					$(ui.placeholder).hide().show(300);									// Добавление плавной анимации
				},
				tolerance: 'pointer',
				update: mod_shop_m_categories.serializeList,
				handle: '.mod-shop-categories__draggable',								// Элемент для зацепки
				axis: 'y',																// Перемещение элементов допускается только по вертикали
				containment: 'parent'													// Перемещение допускается только в родительском контейнере
			});
	},

	serializeList: function(data) {														// Сохранение новой последовательности категорий

		var items = $(data.target).children('.mod-shop-categories__item');				// Все участвующие в сортировке категории

		var arr = [];																	// Массив для сохранения id категорий

		$.each(items, function() {														// Цикл по категориям
			
			var id =																	// Формирование чистого идентификатора текущей категории
				$(this)
					.attr('id')
					.split('_')[1];
			
			arr.push(id);																// Добавление чистого идентификатора в массив
		});

		console.log(arr.join(', '));
	}
};