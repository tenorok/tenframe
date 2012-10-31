// Модель работы с категориями

var mod_shop_m_categories_add = {

	fieldHtml: null,													// Переменная для хранения html-структуры добавления поля категории

	init: function() {													// Инициализация добавления категории

		mod_shop_m_categories_add.fieldHtml =
			$('.mod-shop-categories__fielditem').htmlWithParent();		// Получение html-структуры добавления поля категории
		
		$('#mod-shop-categories-catname')								// Событие ввода названия категории
			.keyup(mod_shop_m_categories_add.setAlias);
		
		$('.mod-shop-categories__fielditem')							// Событие изменения значения выпадающего списка "Новое поле" и выбора уже существующих
			.find('select[name=existfield\\[\\]]')
			.change(mod_shop_m_categories_add.changeExistList);

		$('.mod-shop-categories__textinput[name=name\\[\\]]')			// Событие ввода названия поля
			.keyup(mod_shop_m_categories_add.addField);

		$('.mod-shop-categories__checkboxinput')						// Событие изменения флажка "Выпадающий список"
			.live('change', mod_shop_m_categories_add.changeClassifier);

		$('.mod-shop-categories__textinput_right-column')				// Событие ввода значения выпадающего списка
			.live('keyup', mod_shop_m_categories_add.addClassifierValue);
	},

	setAlias: function() {												// Генерация алиаса названия категории на транслите

		$('#mod-shop-categories-catalias')
			.val(ten_text($(this).val()).translitUri());
	},

	changeExistList: function() {										// Демонстрация и скрытие полей для ввода при изменении выпадающего списка "Новое поле" и выбора уже существующих полей

		var labels =													// Получение всех полей для ввода у данного поля
			$(this)
				.parents('.mod-shop-categories__fielditem')
				.find('.mod-shop-categories__labelitem')
				.not('.mod-shop-categories__existlist');

		if($(this).val() != 'new') {									// Если выбрана уже существующая категория
			
			mod_shop_m_categories_add.addField.apply(this);				// Нужно добавить ещё одну пустую форму для ввода нового поля

			labels.css({'display': 'none'});							// Скрыть все поля для ввода информации о текущем поле
		}
		else															// Иначе выбрано Новое поле
			labels.css({'display': 'block'});							// Нужно показать все поля для ввода информации о текущем поле
	},

	addField: function() {												// Добавление чистой формы для ввода информации об ещё одном поле

		if($(this).hasClass('mod-shop-categories__field-added'))		// Если по текущему элементу уже добавлялась новая форма
			return;														// то больше этого делать не нужно

		var lastField = $('.mod-shop-categories__fielditem').last();	// Получение последней формы ввода информации о поле

		lastField														// Назначение класса, символизирующего, что данная форма уже добавила после себя новую форму
			.find('select[name=existfield\\[\\]]')
			.addClass('mod-shop-categories__field-added');
		
		lastField														// Назначение класса, символизирующего, что данная форма уже добавила после себя новую форму
			.find('.mod-shop-categories__textinput[name=name\\[\\]]')
			.addClass('mod-shop-categories__field-added');

		var fieldsCount =												// Количество добавленных полей
			$('.mod-shop-categories__fieldlist')
				.children('.mod-shop-categories__fielditem')
				.length;
		
		var newFieldHtml = 												// Изменение имён полей для ввода значений выпадающего списка
			mod_shop_m_categories_add.fieldHtml
				.replace(/options_0/g, 'options_' + fieldsCount);
		
		var newField = 													// Добавление новой формы
			$(newFieldHtml)
				.appendTo('.mod-shop-categories__fieldlist');
		
		$(newField)														// Назначение события изменения выпадающего списка "Новое поле" и выбора уже существующих полей в новой форме
			.find('select[name=existfield\\[\\]]')
			.change(mod_shop_m_categories_add.changeExistList);

		$(newField)														// Назначение события ввода названия поля в новой форме
			.find('.mod-shop-categories__textinput[name=name\\[\\]]')
			.keyup(mod_shop_m_categories_add.addField);
	},

	changeClassifier: function() {										// Обработка изменения флажка "Выпадающий список"

		var classifier =												// Получение скрытого элемента списка с полями для ввода значений выпадающего списка
			$(this)
				.parents('.mod-shop-categories__labellist')
				.children('.mod-shop-categories__hiddenitem');

		var hiddenInput = $(this).next();								// Получение скрытого поля-флага, говорящего, является ли поле выпадающим списком

		if($(this).is(':checked')) {									// Если флажок поставлен
			
			$(classifier).css({'display': 'block'});					// Нужно показать поля для ввода значений выпадающего списка

			$(classifier)												// Поставить фокус в первое поле
				.children('.mod-shop-categories__textinput')
				.focus();

			$(hiddenInput).attr('value', '1');							// Изменение флага, поле является выпадающим списком
		}
		else {															// Иначе флажок не поставлен

			$(classifier).css({'display': 'none'});						// И нужно скрыть поля для ввода значений выпадающего списка

			$(hiddenInput).attr('value', '0');							// Изменение флага, поле не является выпадающим списком
		}
	},

	addClassifierValue: function() {									// Добавление чистого поля для ввода значения выпадающего списка

		if($(this).hasClass('mod-shop-categories__classifier-added'))	// Если по текущему элементу уже добавлялось новое поле
			return;

		var parent = $(this).parent('.mod-shop-categories__labelitem');	// Получение родительского тега

		var valCount =													// Количество полей для ввода значений выпадающего списка
			parent
				.children('.mod-shop-categories__textinput')
				.length;

		var newValueHtml =												// Получение html-структуры нового поля для ввода значения выпадающего списка
			$($(this).htmlWithParent())
				.attr('placeholder', 'Значение ' + (++valCount));

		var newValue = newValueHtml.appendTo(parent);					// Добавление нового поля

		$(this).addClass('mod-shop-categories__classifier-added');		// Назначение класса, символизирующего, что данное поле уже добавляло после себя новое поле

		$(newValue)														// Назначение события ввода значения в только что добавленное поле
			.keyup(mod_shop_m_categories_add.addClassifierValue);
	}
};