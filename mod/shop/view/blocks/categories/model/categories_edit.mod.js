// Изменение категории

var mod_shop_m_categories_edit = {

	init: function() {																	// Инициализация изменения категории
		
		$('.mod-shop-categories__link_change-parent')									// Событие клика по заголовку "Изменить родительскую категорию"
			.click(mod_shop_m_categories_edit.toggleParentForm);

		$('.mod-shop-categories__cat_edit')												// Событие выбора родительской категории
			.click(mod_shop_m_categories_edit.changeParent);
	},

	toggleParentForm: function() {														// Скрытие/показ блока со списком для выбора родительской категории
		
		var arrow = $(this).children('.mod-shop-categories__harr');						// Элемент-стрелочка рядом с заголовком
		
		$('.mod-shop-categories__list_edit')											// Список категорий
			.slideToggle(200, function() {												// Скрытие и отображение списка
				
				if($(this).is(':hidden'))												// Изменение стрелочки
					arrow.html('&#9650;');
				else
					arrow.html('&#9660;');
			});

		return false;																	// Отмена перехода по ссылке
	},

	changeParent: function() {															// Изменение родительской категории

		if($(this).hasClass('mod-shop-categories__cat_selected')) {						// Если клик по выбранной категории
			
			$(this)																		// То у неё нужно
				.removeClass('mod-shop-categories__cat_selected');						// удалить класс выбранности

			$('input[name="catparent"]')												// И удалить значение у скрытого поля
				.attr('value', '');
		}
		else {																			// Иначе клик по категории, не являвшейся родителем

			$('.mod-shop-categories__cat_edit')											// У всех категорий
				.removeClass('mod-shop-categories__cat_selected');						// удаляется класс выбранности
			
			$(this)																		// Выбранной категории
				.addClass('mod-shop-categories__cat_selected');							// добавляется класс выбранности

			$('input[name="catparent"]')												// Изменение значения скрытого поля
				.attr('value', $(this).attr('href'));
		}
		
		return false;																	// Отмена перехода по ссылке
	}
};