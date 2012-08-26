<?php

// Класс работы с датой

/*	Использование

	Преобразование даты в привычный вид:
		$date = ten_date::get_date('2011-04-28 18:21:02', array(
			
			'lang'  => 'ru   | en',			// Язык
			'month' => 'full | short',		// Полная или краткая запись месяца
			'time'  =>  true | false,		// Выводить или нет время
			'now'   =>  300					// Время в секундах для написания "сейчас" (По умолчанию: 5 минут)
		));
*/

class ten_date {
	
	private static $month = array(										// Месяца в родительном падеже
		
		'ru' => array(
			
			'full' => array(
				'01' => 'января',   '02' => 'февраля', '03' => 'марта',  '04' => 'апреля',
				'05' => 'мая',      '06' => 'июня',    '07' => 'июля',   '08' => 'августа',
				'09' => 'сентября', '10' => 'октября', '11' => 'ноября', '12' => 'декабря'
			),

			'short' => array(
				'01' => 'янв', '02' => 'фев',  '03' => 'мар',  '04' => 'апр',
				'05' => 'мая', '06' => 'июня', '07' => 'июля', '08' => 'авг',
				'09' => 'сен', '10' => 'окт',  '11' => 'ноя',  '12' => 'дек'
			)
		),

		'en' => array(

			'full' => array(
				'01' => 'january',   '02' => 'february', '03' => 'march',    '04' => 'april',
				'05' => 'may',       '06' => 'june',     '07' => 'july',     '08' => 'august',
				'09' => 'september', '10' => 'october',  '11' => 'november', '12' => 'december'
			),

			'short' => array(
				'01' => 'jan', '02' => 'feb', '03' => 'mar', '04' => 'apr',
				'05' => 'may', '06' => 'jun', '07' => 'jul', '08' => 'aug',
				'09' => 'sep', '10' => 'oct', '11' => 'nov', '12' => 'dec'
			)
		)
	);

	private static $words = array(										// Слова
		
		'now' => array(
			'en' => 'now',
			'ru' => 'сейчас'
		),

		'today' => array(
			'en' => 'today',
			'ru' => 'сегодня'
		),

		'yesterday' => array(
			'en' => 'yesterday',
			'ru' => 'вчера'
		),

		'tomorrow' => array(
			'en' => 'tomorrow',
			'ru' => 'завтра'
		),

		'will' => array(
			'en' => 'will',
			'ru' => 'будет'
		),

		'at' => array(
			'en' => 'at',
			'ru' => 'в'
		)
	);

	private static $default_options = array(							// Дефолтные параметры
		'lang'  => 'ru',
		'month' => 'full',
		'time'  => false,
		'now'   => 300
	);
	
	/**
	 * Функция преобразования даты в привычный вид
	 *
	 * @param  datetime $date    Дата для преобразования
	 * @param  array    $options Параметры вывода даты
	 * @return string
	 */
	public static function get_date($date, $options = null) {
		
		foreach(ten_date::$default_options as $key => $val)																	// Установка значений по умолчанию
			if(!isset($options[$key]))																						// для незаданных опций
				$options[$key] = $val;

		$lang  = $options['lang'];
		$words = ten_date::$words;
		$ret_date = null;																									// Переменная для записи возвращаемой даты

		list($year, $month, $daytime) = explode('-', $date);																// Год, месяц и день со временем
		$day = (substr($daytime, 0, 1) == '0') ? substr($daytime, 1, 1) : substr($daytime, 0, 2);							// День без первого нуля
		
		if($year . '-' . $month == date('Y-m')) {																			// Если текущий год и месяц

			$now_day = date('d');

			if($day == $now_day)																							// Если дата сегодняшняя
				$ret_date = $words['today'][$lang];

			else if($day == $now_day - 1)																					// Иначе, если дата вчерашняя
				$ret_date = $words['yesterday'][$lang];

			else if($day == $now_day + 1)																					// Иначе, если дата завтрашняя
				$ret_date = $words['tomorrow'][$lang];
		}
		
		if(is_null($ret_date))																								// Если дата до сих пор не указана
			$ret_date = 
				$day . ' ' .
				ten_date::$month[$lang][$options['month']][$month] . ' ' . 
				(($year == date('Y')) ? '' : $year);																		// Если год текущий, то его не надо печатать
		
		if(
			$ret_date == $words['today'][$lang] && 																			// Если дата сегодняшняя
			strtotime('now') - strtotime($date) < $options['now'] && 														// и время не далее, чем $now секунд назад
			strtotime('now') - strtotime($date) > 0																			// и разница между текущим временем и переданным положительна
		)
			return $words['now'][$lang];																					// Значит это сейчас
		
		else																												// Иначе, либо дата не сегодняшняя, либо разница во времени больше, чем $now секунд
			return
				((strtotime('now') < strtotime($date)) ? $words['will'][$lang] . ' ' : '') . 								// Если надо печатать "будет"
				$ret_date . 																								// Дата
				(($options['time'] == true) ? ' ' . $words['at'][$lang] . ' ' . substr($daytime, 3, 5) : '');				// Если надо печатать время
	}
}
