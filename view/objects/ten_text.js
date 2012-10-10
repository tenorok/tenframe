// Класс работы с текстом

// Version 1.0.1
// From 26.09.2012

/*	Использование

	Преобразование URI в транслит:
		var translit = new ten_text('Текст для перевода').translitUri();
*/

function ten_text(text) {

	if(!(this instanceof ten_text)) return new ten_text(text);

	this.text = text;
}

/**
 * Функция преобразования URI в транслит
 *
 * @return string
 */
ten_text.prototype.translitUri = function() {
	
	var exchangeLetters = {															// Массив с латинским обозначением кириллических символов
		'А':'A',  'Б':'B',   'В':'V', 'Г':'G',  'Д':'D', 
		'Е':'E',  'Ё':'E',   'Ж':'J', 'З':'Z',  'И':'I',
		'Й':'Y',  'К':'K',   'Л':'L', 'М':'M',  'Н':'N',
		'О':'O',  'П':'P',   'Р':'R', 'С':'S',  'Т':'T',
		'У':'U',  'Ф':'F',   'Х':'H', 'Ц':'TS', 'Ч':'CH',
		'Ш':'SH', 'Щ':'SCH', 'Ъ':'',  'Ы':'YI', 'Ь':'',
		'Э':'E',  'Ю':'YU',  'Я':'YA', 
		
		'а':'a',  'б':'b',   'в':'v', 'г':'g',  'д':'d', 
		'е':'e',  'ё':'e',   'ж':'j', 'з':'z',  'и':'i',
		'й':'y',  'к':'k',   'л':'l', 'м':'m',  'н':'n',
		'о':'o',  'п':'p',   'р':'r', 'с':'s',  'т':'t',
		'у':'u',  'ф':'f',   'х':'h', 'ц':'ts', 'ч':'ch',
		'ш':'sh', 'щ':'sch', 'ъ':'y', 'ы':'yi', 'ь':'',
		'э':'e',  'ю':'yu',  'я':'ya',

		' ':'_'
	},
		regexp = '';

	for(key in exchangeLetters)														// Создание строки для использования в регулярном выражении
		regexp += key;
	
	return this.text
		.replace(new RegExp('[' + regexp + ']', 'g'), function(str) {				// Использование регулярного выражения для переданного текста
			return str in exchangeLetters ? exchangeLetters[str] : '';
		})
		.toLowerCase();
}