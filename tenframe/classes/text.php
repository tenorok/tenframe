<?php

/**
 * Работа с текстом
 * @version 1.1.1
 */

/* Использование

    Перевод строки в нижний регистр:
        $string = ten\text::lower_case($string);

    Преобразование текста в транслит:
        $text = ten\text::translit($text);

    Преобразование URI в транслит:
        $uri = ten\text::translit_uri($uri);

    Типограф:
        $text = ten\text::typograf('"Вы всё ещё кое-как верстаете в "Ворде"? - Тогда мы идем к вам!"');

    Отбивка:
        Если второй параметр не указан, то используется отбивка для шрифта Arial:
            $text = ten\text::wean('&laquo;подсказок&raquo;');

        Можно указывать шрифт, если он есть в массиве $wean_fonts:
            $text = ten\text::wean('&laquo;подсказок&raquo;', 'verdana');

        Можно передавать массив специфичных отступов:
            $text = ten\text::wean('&laquo;подсказок&raquo;', array('b'=>'.58', 'm'=>'.85', 's'=>'.4'));

    Экранирование:
        $text = ten\text::strip($text);

    Добавление подстрок:
        Добавление подстроки в начало и конец строки, если её там ещё нет:
            $text = ten\text::gum($text,  'substr');
        Добавление подстроки в начало строки, если её там ещё нет:
            $text = ten\text::lgum($text, 'substr');
        Добавление подстроки в конец строки, если её там ещё нет:
            $text = ten\text::rgum($text, 'substr');

    Удаление подстрок:
        $substr = string || array;                                           // Можно передать одну строку или массив строк

        Удаление подстроки из начала и конца строки, если она там есть
            $text = ten\text::del($text,  $substr);
        Удаление подстроки из начала строки, если она там есть
            $text = ten\text::ldel($text, $substr);
        Удаление подстроки из конца строки, если она там есть
            $text = ten\text::rdel($text, $substr);

        Рекурсивное удаление подстрок:
            Те же самые функции с приставкой all: delall, ldelall, rdelall
*/

namespace ten;

class text extends core {

    protected static $cyrillic_alphabet = array(                             // Кириллический алфавит
        'А'=>'а', 'Б'=>'б', 'В'=>'в', 'Г'=>'г', 'Д'=>'д',
        'Е'=>'е', 'Ё'=>'ё', 'Ж'=>'ж', 'З'=>'з', 'И'=>'и',
        'Й'=>'й', 'К'=>'к', 'Л'=>'л', 'М'=>'м', 'Н'=>'н',
        'О'=>'о', 'П'=>'п', 'Р'=>'р', 'С'=>'с', 'Т'=>'т',
        'У'=>'у', 'Ф'=>'ф', 'Х'=>'х', 'Ц'=>'ц', 'Ч'=>'ч',
        'Ш'=>'ш', 'Щ'=>'щ', 'Ъ'=>'ъ', 'Ы'=>'ы', 'Ь'=>'ь',
        'Э'=>'э', 'Ю'=>'ю', 'Я'=>'я'
    );

    protected static $latin_alphabet = array(                                // Латинский алфавит
        'A'=>'a', 'B'=>'b', 'C'=>'c', 'D'=>'d', 'E'=>'e',
        'F'=>'f', 'G'=>'g', 'H'=>'h', 'I'=>'i', 'J'=>'j',
        'K'=>'k', 'L'=>'l', 'M'=>'m', 'N'=>'n', 'O'=>'o',
        'P'=>'p', 'Q'=>'q', 'R'=>'r', 'S'=>'s', 'T'=>'t',
        'U'=>'u', 'V'=>'v', 'W'=>'w', 'X'=>'x', 'Y'=>'y',
        'Z'=>'z'
    );

    /**
     * Функция перевода строки в нижний регистр
     *
     * @param string $string Строка для преобразования
     * @return string
     *
     * P.S. Было решено написать отдельно эту функцию, т.к. встроенная функция PHP strtolower() конфликтует с кириллицей
     */
    public static function lower_case($string) {

        return strtr(
            $string,
            self::$cyrillic_alphabet + self::$latin_alphabet                 // Объединение массивов кириллического и латинского алфавита
        );
    }

    protected static $exchange_letters = array(                              // Массив с латинским обозначением кириллических символов
        'А'=>'A', 'Б'=>'B', 'В'=>'V', 'Г'=>'G', 'Д'=>'D',
        'Е'=>'E', 'Ё'=>'E', 'Ж'=>'J', 'З'=>'Z', 'И'=>'I',
        'Й'=>'Y', 'К'=>'K', 'Л'=>'L', 'М'=>'M', 'Н'=>'N',
        'О'=>'O', 'П'=>'P', 'Р'=>'R', 'С'=>'S', 'Т'=>'T',
        'У'=>'U', 'Ф'=>'F', 'Х'=>'H', 'Ц'=>'TS', 'Ч'=>'CH',
        'Ш'=>'SH', 'Щ'=>'SCH', 'Ъ'=>'', 'Ы'=>'YI', 'Ь'=>'',
        'Э'=>'E', 'Ю'=>'YU', 'Я'=>'YA',
        'а'=>'a', 'б'=>'b', 'в'=>'v', 'г'=>'g', 'д'=>'d',
        'е'=>'e', 'ё'=>'e', 'ж'=>'j', 'з'=>'z', 'и'=>'i',
        'й'=>'y', 'к'=>'k', 'л'=>'l', 'м'=>'m', 'н'=>'n',
        'о'=>'o', 'п'=>'p', 'р'=>'r', 'с'=>'s', 'т'=>'t',
        'у'=>'u', 'ф'=>'f', 'х'=>'h', 'ц'=>'ts', 'ч'=>'ch',
        'ш'=>'sh', 'щ'=>'sch', 'ъ'=>'y', 'ы'=>'yi', 'ь'=>'',
        'э'=>'e', 'ю'=>'yu', 'я'=>'ya'
    );

    protected static $exchange_other = array(                                // Массив со специальными символами для замены в URI
        ' '=>'_'
    );

    /**
     * Функция преобразования текста в транслит
     *
     * @param  string $text Текст для преобразования
     * @return string
     */
    public static function translit($text) {

        return strtr($text, self::$exchange_letters);
    }

    /**
     * Функция преобразования URI в транслит
     *
     * @param  string $uri URI для преобразования
     * @return string
     */
    public static function translit_uri($uri) {

        return urlencode(strtr(
            self::lower_case($uri),                                      // Преобразование полученной строки в нижний регистр
            self::$exchange_letters + self::$exchange_other              // Объединение массивов для замены символов и спецсимволов
        ));
    }

    /*
    PHP-implementation of ArtLebedevStudio.RemoteTypograf class (web-service client)

    Copyright (c) Art. Lebedev Studio | http://www.artlebedev.ru/

    Typograf homepage: http://typograf.artlebedev.ru/
    Web-service address: http://typograf.artlebedev.ru/webservices/typograf.asmx
    WSDL-description: http://typograf.artlebedev.ru/webservices/typograf.asmx?WSDL

    Default charset: UTF-8

    Version: 1.0 (August 30, 2005)
    Author: Andrew Shitov (ash@design.ru)


    Example:
        print ten\text::typograf('"Вы все еще кое-как верстаете в "Ворде"? - Тогда мы идем к вам!"');
    */

    public static $entityType = 1;                                           // html = 1; xml = 2; no = 3; mixed = 4;
    public static $useBr = 1;
    public static $useP = 0;
    public static $maxNobr = 3;
    public static $encoding = 'UTF-8';
    public static $quotA = 'laquo raquo';
    public static $quotB = 'bdquo ldquo';

    public static function typograf($text) {

        $text = stripslashes($text);

        $text = str_replace('&', '&amp;', $text);
        $text = str_replace('<', '&lt;', $text);
        $text = str_replace('>', '&gt;', $text);

        $SOAPBody = '<?xml version="1.0" encoding="' . self::$encoding . '"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <ProcessText xmlns="http://typograf.artlebedev.ru/webservices/">
      <text>' . $text . '</text>
      <entityType>' . self::$entityType . '</entityType>
      <useBr>' . self::$useBr . '</useBr>
      <useP>' . self::$useP . '</useP>
      <maxNobr>' . self::$maxNobr . '</maxNobr>
      <quotA>' . self::$quotA . '</quotA>
      <quotB>' . self::$quotB . '</quotB>
    </ProcessText>
  </soap:Body>
</soap:Envelope>';

        $host = 'typograf.artlebedev.ru';
        $SOAPRequest = 'POST /webservices/typograf.asmx HTTP/1.1
Host: typograf.artlebedev.ru
Content-Type: text/xml
Content-Length: ' . strlen($SOAPBody). '
SOAPAction: "http://typograf.artlebedev.ru/webservices/ProcessText"

'.
    $SOAPBody;

        $remoteTypograf = fsockopen($host, 80);
        fwrite($remoteTypograf, $SOAPRequest);
        $typografResponse = '';
        while(!feof($remoteTypograf))
            $typografResponse .= fread($remoteTypograf, 8192);

        fclose($remoteTypograf);

        $startsAt = strpos($typografResponse, '<ProcessTextResult>') + 19;
        $endsAt = strpos($typografResponse, '</ProcessTextResult>');
        $typografResponse = substr($typografResponse, $startsAt, $endsAt - $startsAt - 1);

        $typografResponse = str_replace('&amp;', '&', $typografResponse);
        $typografResponse = str_replace('&lt;', '<', $typografResponse);
        $typografResponse = str_replace('&gt;', '>', $typografResponse);
        $typografResponse = str_replace('<br />', '<br>', $typografResponse);	 // Чтобы одиночный тег <br> использовался без закрывающего слеша

        return $typografResponse;
    }

    protected static $wean_symbols = array(                                      // Символы для отбивки
        '&laquo;'=>'b', '&#171;'=>'b', '«'=>'b',
        '('=>'m',
        '&bdquo;'=>'s', '&#8222;'=>'s', '„'=>'s'
    );

    protected static $wean_fonts = array(                                        // Отступы для разных шрифтов
        'arial'        => array('b'=>'.55', 'm'=>'.355', 's'=>'.3'),
        'verdana'      => array('b'=>'.65', 'm'=>'.45',  's'=>'.45'),
        'georgia'      => array('b'=>'.58', 'm'=>'.35',  's'=>'.4'),
        'tahoma'       => array('b'=>'.58', 'm'=>'.38',  's'=>'.4'),
        'times'        => array('b'=>'.50', 'm'=>'.33',  's'=>'.25'),
        'helvetica'    => array('b'=>'.55', 'm'=>'.33',  's'=>'.3'),
        'garamond'     => array('b'=>'.36', 'm'=>'.28',  's'=>'.45'),
        'trebuchet ms' => array('b'=>'.52', 'm'=>'.38',  's'=>'.52')
    );

    /**
     * Функция отбивки кавычек и скобок в тексте
     *
     * @param  string $text Текст для преобразования
     * @param  array $font Шрифт текста
     * @return string
     */
    public static function wean($text, $font = 'arial') {

        $ws_full = self::$wean_symbols;

        foreach($ws_full as $key => $val) {
            $ws_full['&nbsp;' . $key] = $val;
            $ws_full[' ' . $key] = $val;
        }

        if(is_array($font))                                                      // Если передан массив с отступами
            $wean = $font;
        else                                                                     // Иначе ставятся отступы для заданного шрифта
            $wean = self::$wean_fonts[$font];

        $big    = '<span style="margin-right: ' . $wean['b'] . 'em;"> </span><span style="margin-left: -' . $wean['b'] . 'em;">{{symbol}}</span>';
        $middle = '<span style="margin-right: ' . $wean['m'] . 'em;"> </span><span style="margin-left: -' . $wean['m'] . 'em;">{{symbol}}</span>';
        $small  = '<span style="margin-right: ' . $wean['s'] . 'em;"> </span><span style="margin-left: -' . $wean['s'] . 'em;">{{symbol}}</span>';

        foreach($ws_full as $key => $val) {

            $symbol = str_replace('&nbsp;', '', ltrim($key));

            if($val == 'b')
                $ws_full[$key] = str_replace('{{symbol}}', $symbol, $big);
            else if($val == 'm')
                $ws_full[$key] = str_replace('{{symbol}}', $symbol, $middle);
            else if($val == 's')
                $ws_full[$key] = str_replace('{{symbol}}', $symbol, $small);
        }

        return strtr($text, array_reverse($ws_full));
    }

    /**
     * Функция экранирование нежелательных символов
     *
     * @param  string $text Текст для преобразования
     * @return string
     */
     public static function strip($text) {

        $text = strip_tags(trim($text));                             // Удаление HTML и PHP тегов; Удаление пробелов из начала и конца строки

        if(!get_magic_quotes_gpc())                                  // Если магические кавычки выключены
            $text = addslashes($text);                               // то надо добавить экранирование спецсимволов

        return $text;
     }

     /**
     * Добавление подстроки в начало и конец строки, если её там нет
     *
     * @param  string $string Строка
     * @param  string $substr Подстрока
     * @return string
     */
    public static function gum($string, $substr) {

        $string = self::lgum($string, $substr);
        return    self::rgum($string, $substr);
    }

    /**
     * Добавление подстроки в начало строки, если её там нет
     *
     * @param  string $string Строка
     * @param  string $substr Подстрока
     * @return string
     */
    public static function lgum($string, $substr) {

        return (substr($string, 0, strlen($substr)) != $substr) ? $substr . $string : $string;
    }

    /**
     * Добавление подстроки в конец строки, если её там нет
     *
     * @param  string $string Строка
     * @param  string $substr Подстрока
     * @return string
     */
    public static function rgum($string, $substr) {

        return (substr($string, -strlen($substr)) != $substr) ? $string . $substr : $string;
    }

    /**
     * Удаление подстрок из начала и конца строки, если они там есть
     *
     * @param  string         $string Строка
     * @param  string | array $substr Подстрока или массив подстрок
     * @return string
     */
    public static function del($string, $substr) {

        $string = self::ldel($string, $substr);
        return    self::rdel($string, $substr);
    }

    /**
     * Рекурсивное удаление подстрок из начала и конца строки
     *
     * @param  string         $string Строка
     * @param  string | array $substr Подстрока или массив подстрок
     * @return string
     */
    public static function delall($string, $substr) {

        $string = self::ldelall($string, $substr);
        return    self::rdelall($string, $substr);
    }

    /**
     * Удаление подстрок из начала строки, если они там есть
     *
     * @param  string         $string Строка
     * @param  string | array $substr Подстрока или массив подстрок
     * @return string
     */
    public static function ldel($string, $substr) {

        return self::delChar($string, $substr, 'left');
    }

    /**
     * Рекурсивное удаление подстрок из начала строки
     *
     * @param  string         $string Строка
     * @param  string | array $substr Подстрока или массив подстрок
     * @return string
     */
    public static function ldelall($string, $substr) {

        return self::delChar($string, $substr, 'left', true);
    }

    /**
     * Удаление подстрок из конца строки, если он там есть
     *
     * @param  string         $string Строка
     * @param  string | array $substr Подстрока или массив подстрок
     * @return string
     */
    public static function rdel($string, $substr) {

        return self::delChar($string, $substr, 'right');
    }

    /**
     * Рекурсивное удаление подстрок из конца строки
     *
     * @param  string         $string Строка
     * @param  string | array $substr Подстрока или массив подстрок
     * @return string
     */
    public static function rdelall($string, $substr) {

        return self::delChar($string, $substr, 'right', true);
    }

    /**
     * Возвращение массива подстрок для функций с неоднозначным типом данных параметра
     *
     * @param  string | array $substr Подстрока или массив подстрок
     * @return array
     */
    private static function getCharArr($substr) {

        if(gettype($substr) == 'string')
            $charArr[0] = $substr;
        else
            $charArr    = $substr;

        return $charArr;
    }

    /**
     * Выполнение операции удаления подстрок
     *
     * @param  string         $string   Строка
     * @param  string | array $substr   Подстрока или массив подстрок
     * @param  string         $position Откуда удалять подстроку
     * @param  boolean        $all      Флаг удаления всех подстрок
     * @return string
     */
    private static function delChar($string, $substr, $position, $all = false) {

        foreach(self::getCharArr($substr) as $charCur) {

            switch($position) {

                case 'left':
                    $sub = substr($string, 0, strlen($charCur));
                    $ret = substr($string,    strlen($charCur));
                    break;

                case 'right':
                    $sub = substr($string,    -strlen($charCur));
                    $ret = substr($string, 0, -strlen($charCur));
                    break;
            }

            if($sub == $charCur)
                if(!$all)
                    return $ret;
                else
                    return self::delChar($ret, $substr, $position, $all);
        }

        return $string;
    }
}