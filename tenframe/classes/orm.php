<?php

/**
 * Работа с базой данных
 * @version 0.0.1
 */

/* Использование

    Правило именования ключей в БД:
        Первичный ключ: table_id
        Внешний ключ:   table_fk

    Подключение к MySQL:
        ten\orm::connect('host', 'login', 'password');

    Выбор базы данных:
        ten\orm::db('dbname');

    Добавление записи:
        $last_id = ten\orm::insert('table', array(                     // Возвращается идентификатор добавленной записи или false, если запрос не был выполнен
            'field_1' => 'value',                                      // Перечисление полей и значений
            'field_2' => 'func: now()'                                 // Для использования функций применяется ключевое слово "func:"
        ));

    Обновление записи:
        ten\orm::update('table', array(                                // Возвращается true или false
            'field_1' => 'value',                                      // Перечисление полей и значений
            'field_2' => 'func: now()'                                 // Для использования функций применяется ключевое слово "func:"
        ))
        ->where('table_id > 10');                                      // Обязательная опция. В качестве условия может быть строка
        ->where(10);                                                   // или число (такая запись идентична: table_id = 10)
        ->where('all');                                                // или применить для всех строк таблицы (длинная запись)
        ->where('*');                                                  //     применить для всех строк таблицы (краткая запись)

    Удаление записи:
        ten\orm::delete('table')                                       // Возвращается true или false
            ->where(...);                                              // Обязательная опция. Условия удаления

    Выборка записей:
        $result =                                                      // Результаты выборки возвращаются в виде массива объектов
                                                                       // или в виде одного объекта, если был указан "->where(число)"
            ten\orm::select('table')                                   // По умолчанию выбираются все поля таблицы
                ->sub(array(                                           // Подзапросы
                    'select count(*) from tab1' => 'count1'            // Идентично select count(*) from tab1 as `count1`
                ))
                ->fields('*, sum(field1)')                             // Явное          указание select
                ->addfields('sum(field1)')                             // Дополнительное указание select (Аналогично предыдущей строке)
                ->order('field1')                                      // Сортировка
                ->group('field1')                                      // Группировка
                ->limit('0, 10')                                       // Лимит
                ->prefix('prefix_')                                    // Префикс
                ->where(...);                                          // Обязательно указывать последней! Последовательность предыдущих опций свободна

        $result =                                                      // Результатом выборки будет всегда массив объектов
            ten\orm::join('table', array(                              // From table и массив join-таблиц

                array(                                                 // Описание подключаемой таблицы
                    'table'  => 'tablename_1',                         // Обязательный. Имя подключаемой таблицы
                    'join'   => 'inner',                               // Тип join: inner (по умолчанию), left outer, right outer, full outer, cross
                    'on'     => '...',                                 // Дополнительное условие для соединения таблиц
                    'left'   => 'users',                               // left | right; По умолчанию: 'left' => 'table' (Обычное направление связи к первоначальной таблице)
                    'prefix' => 'prefix_'                              // Префикс для полей данной таблицы
                ),

                array(
                    'table'  => 'tablename_2',                         // Таблица tablename_2
                    'right'  => 'tablename_1'                          // Подключается к таблице tablename_1 в обратном направлении связи
                                                                       // Иначе говоря, в данном случае tablename_1 играет роль таблицы-связки
                ),

                array(
                    'table'  => 'tablename_3'                          // Таблица tablename_3 подключится к первоначальной таблице table
                )
            ))
            ->sub, fields, addfields, order, group, limit              // те же опции, что и в select
            ->prefix('prefix_{table}')                                 // Префикс. Вместо {table} подставится имя таблицы
            ->where(...);                                              // Обязательно указывать последней! Последовательность предыдущих опций свободна

            Важно:
                Если в результате объединения таблиц появляются одинаковые поля, то они автоматически будут приведены в вид: таблица_поле.
                Данная проверка осуществляется, если для таблицы явно не указан префикс ('prefix' => 'prefix_').

    Отладка:
        ten\orm::result($result);                                      // Печать результатов выборки в удобочитаемом виде
        ten\orm::debug();                                              // Статистика проведённых до этого момента запросов
*/

namespace ten;

class orm extends core {

    public static $mysqli;                                                 // Объект работы с MySQL

    private static $queries = array();                                     // Массив данных о выполняемых запросах
    private static $parameters = null;                                     // Массив параметров текущей операции
    private static $object;                                                // Текущий объект

    /**
     * Конструктор для сохранения текущей операции
     *
     * @param string $operation Название текущей операции
     */
    private function __construct($operation = null) {

        self::$queries[count(self::$queries)]->name = $operation;

        self::$limit      = null;                                           // Обнуление дополнительных переменных перед каждым новым запросом
        self::$order      = null;
        self::$group      = null;
        self::$fields     = null;
        self::$addfields  = null;
        self::$subqueries = null;
        self::$prefix     = null;

        self::$single     = false;                                          // Выключение флага одиночной выборки
    }

    /**
     * Функция подключения к MySQL
     *
     * @param string $host     Имя хоста
     * @param string $login    Логин
     * @param string $password Пароль
     */
    public static function connect($host, $login, $password) {

        self::$mysqli = new \mysqli($host, $login, $password);
        self::$mysqli->set_charset('utf8');
    }

    /**
     * Функция выбора базы данных
     *
     * @param string $db Имя базы данных
     */
    public static function db($db) {

        if(!self::$mysqli->select_db($db))
            message::error('Selected database <b>' . $db . '</b> not found');
    }

    /**
     * Функция преобразования значений для использования в SQL-запросе
     *
     * @param mixed $val Значение для преобразования
     * @return mixed
     */
    private static function get_value($val) {

        $quote = '';

        if(strpos($val, 'func:') !== false) {                              // Если в значении присутствует ключевое слово, указывающее на функцию

            $val = str_replace('func:', '', $val);                         // Удаление ключевого слова из значения
            $val = str_replace(' ', '', $val);                             // Удаление пробелов из значения
        }
        else
            $quote = (
                gettype($val) == 'string'    &&                            // Если у значения строковый тип
                    !preg_match('/^\d+$/', $val) &&                        // и это не число со строковым типом
                    strtolower($val) != 'null'                             // и это не null
            ) ? '\'' : '';                                                 // то надо добавить кавычки

        return $quote . $val . $quote;                                     // При необходимости возвращаемое значение обрамляется в апострофы
    }

    /**
     * Функция добавления записи в базу данных
     *
     * @param string $table  Имя таблицы
     * @param array  $values Массив со значениями
     * @return integer || boolean
     */
    public static function insert($table, $values) {

        self::$parameters = array($table, $values);

        new torm(__FUNCTION__);

        self::set_debug(debug_backtrace());

        $fields    = '';
        $variables = '';

        foreach($values as $key => $val) {

            $fields .= $key . ', ';
            $variables .=  self::get_value($val) . ', ';
        }

        if(!self::execute_query('insert into ' . $table . '(' . substr($fields, 0, -2) . ') values (' . substr($variables, 0, -2) . ')'))
            return false;                                                  // Запрос не выполнен и возвращается отрицательный результат

        return self::$mysqli->insert_id;                                   // Возвращается последний добавленный идентификатор
    }

    /**
     * Функция обновления записи в базе данных
     *
     * @param string $table  Имя таблицы
     * @param array  $values Массив со значениями
     * @return object
     */
    public static function update($table, $values) {

        self::$parameters = array($table, $values);

        self::$object = new torm(__FUNCTION__);
        self::set_debug(debug_backtrace());

        return self::$object;
    }

    /**
     * Функция обработки данных перед отправкой на выполнение запроса на обновление записи
     *
     * @param string $table  Имя таблицы
     * @param array  $values Массив со значениями
     * @return boolean
     */
    private static function update_query($table, $values) {

        $variables = '';

        foreach($values as $key => $val)
            $variables .= $key . ' = ' . self::get_value($val) . ', ';

        return self::execute_query('update ' . $table . ' set ' . substr($variables, 0, -2) . self::$where);
    }

    /**
     * Функция удаления записи из базы данных
     *
     * @param string $table Имя таблицы
     * @return object
     */
    public static function delete($table) {

        self::$parameters = array($table);
        self::$object = new torm(__FUNCTION__);
        self::set_debug(debug_backtrace());

        return self::$object;
    }

    /**
     * Функция вызова запроса на удаление записи
     *
     * @param string $table  Имя таблицы
     * @return boolean
     */
    private static function delete_query($table) {

        return self::execute_query('delete from ' . $table . self::$where);
    }

    /**
     * Функция выборки записей из базы данных
     *
     * @param string $table Имя таблицы
     * @return object
     */
    public static function select($table) {

        self::$parameters = array($table);
        self::$object = new torm(__FUNCTION__);
        self::set_debug(debug_backtrace());

        return self::$object;
    }

    private static $int_array = array(                                     // Массив типов данных базы данных, которые необходимо перевести в integer
        'tinyint' => 1, 'smallint'  => 2, 'integer' => 3,
        'bigint'  => 8, 'mediumint' => 9, 'year'    => 13
    );

    private static $float_array = array(                                   // Массив типов данных базы данных, которые необходимо перевести в float
        'float' => 4, 'double' => 5
    );

    /**
     * Функция вызова запроса на выборку
     *
     * @param string $table  Имя таблицы
     * @return array || boolean
     */
    private static function select_query($table) {

        $select = (!is_null(self::$fields)) ? self::$fields : '*';

        return self::modernize_selection(

            self::execute_query(
                'select '         .
                    $select           .
                    self::$addfields   .
                    self::$subqueries  .
                    ' from ' . $table .
                    self::$where .
                    self::$group .
                    self::$order .
                    self::$limit
            )
        );
    }

    /**
     * Функция соединения таблиц базы данных
     *
     * @param string $table Имя левой таблицы
     * @param array  $join  Массив массивов с описанием правых таблиц
     * @return object
     */
    public static function join($table, $join) {

        self::$parameters = array($table, $join);
        self::$object = new torm(__FUNCTION__);
        self::set_debug(debug_backtrace());

        return self::$object;
    }

    /**
     * Функция вызова join-запроса
     *
     * @param string $table Имя левой таблицы
     * @param array  $join  Массив массивов с описанием правых таблиц
     * @return array || boolean
     */
    private static function join_query($table, $join = array()) {

        $exist_fields = array();                                                                    // Массив для хранения всех выбранных полей

        $fields = self::execute_query('show columns from ' . $table);                               // Запрос на получение списка полей левой таблицы

        while($field = $fields->fetch_object())                                                     // Цикл по полученному списку полей левой таблицы
            array_push($exist_fields, $field->Field);                                               // Добавление поля в массив полей

        $joins  = '';                                                                               // Строка для конкатенации подключения таблиц

        $select = (!is_null(self::$fields)) ? self::$fields : $table . '.*';                        // Если задано значение для select, то используется оно, иначе все поля левой таблицы

        foreach($join as $tab) {                                                                    // Цикл по массивам с описанием правых таблиц

            $type = (!isset($tab['join'])) ? 'inner' : $tab['join'];                                // Если не задан тип join, то по умолчанию устанавливается inner

            if(isset($tab['right']))                                                                // Если задано правое направление связи
                $on = $tab['table'] . '.' . $tab['table'] . '_id = ' . $tab['right'] . '.' . $tab['table'] . '_fk';

            else {                                                                                  // Иначе правое направление связи не задано

                $left = (isset($tab['left'])) ? $tab['left'] : $table;                              // Если явно задано левое направление связи, то нужно использовать указанную в направлении таблицу, иначе использовать левую таблицу

                $on = $tab['table'] . '.' . $left . '_fk = ' . $left . '.' . $left . '_id';
            }

            $on .= (isset($tab['on'])) ? ' and ' . $tab['on'] : '';                                 // Добавление опции on

            $joins .= ' ' . $type . ' join ' . $tab['table'] . ' on ' . $on;                        // Конкатенация полной строки подключения таблицы

            if(!is_null(self::$fields))                                                             // Если задано значение для select
                continue;                                                                           // то нужно пропустить последующие операции и перейти к следующей правой таблице

            $fields = self::execute_query('show columns from ' . $tab['table']);                    // Запрос на получение списка полей текущей правой таблицы

            if(isset($tab['prefix']))                                                               // Если задан префикс для текущей правой таблицы
                while($field = $fields->fetch_object())                                             // Цикл по полученному списку полей правой таблицы
                    $select .= ', ' . $tab['table'] . '.' . $field->Field . ' as ' . $tab['prefix'] . $field->Field;

            else                                                                                    // Иначе префикс для текущей правой таблицы не задан
                while($field = $fields->fetch_object()) {                                           // Цикл по полученному списку полей правой таблицы

                    $select .= ', ' . $tab['table'] . '.' . $field->Field;

                    if(in_array($field->Field, $exist_fields)) {                                    // Если поле, с именем текущего уже было в одной из предыдущих таблиц

                        $field->Field = $tab['table'] . '_' . $field->Field;                        // Значит этому полю нужно добавить табличный префикс

                        $select .= ' as ' . $field->Field;                                          // и в запросе указать его в качестве as
                    }

                    array_push($exist_fields, $field->Field);                                       // Добавление текущего поля в массив хранения всех полей
                }
        }

        return self::modernize_selection(

            self::execute_query(
                'select '         .
                    $select           .
                    self::$addfields   .
                    self::$subqueries  .
                    ' from ' . $table .
                    $joins            .
                    self::$where .
                    self::$group .
                    self::$order .
                    self::$limit
            )
        );
    }

    /**
     * Функция обработки результатов выборки
     *
     * @param array $result Результат выборки
     * @return array || boolean
     */
    private static function modernize_selection($result) {

        if(!$result)                                                                                // Если запрос не был выполнен
            return false;

        else {                                                                                      // Иначе запрос был успешно выполнен

            $result_array = array();                                                                // Результирующий массив

            while($current_row = $result->fetch_object()) {                                         // Цикл по строкам результатов выборки

                foreach($result->fetch_fields() as $val) {                                          // Цикл по полям текущей строки

                    $name = $val->name;                                                             // Имя текущего поля

                    if(!is_null(self::$prefix)) {                                                   // Если требуется добавить префикс

                        $prefix = str_replace('{table}', $val->table, self::$prefix);

                        $prefix_name = $prefix . $name;                                             // Формирование нового имени для поля
                        $current_row->$prefix_name = $current_row->$name;                           // Присваивание значения из старого свойства объекта свойству с новым именем
                        unset($current_row->$name);                                                 // Удаление свойства со старым именем
                        $name = $prefix_name;                                                       // Замена основного имени на новое с префиксом
                    }

                    if(in_array($val->type, self::$int_array))                                      // Если тип данных текущего поля является числовым и целым
                        $current_row->$name = intval($current_row->$name);                          // то это поле надо перевести в целое число

                    else if(in_array($val->type, self::$float_array))                               // Если тип данных текущего поля является числовым и дробным
                        $current_row->$name = floatval($current_row->$name);                        // то это поле надо перевести в дробное число
                }

                array_push($result_array, $current_row);                                            // Запись строки в результирующий массив
            }

            if(self::$single)                                                                       // Если нужно выбрать одну строку
                return $result_array[0];                                                            // то нужно вернуть именно её
            else
                return $result_array;                                                               // иначе массив записей
        }
    }

    private static $where;                                                                          // Переменная, хранящая переданные условия
    private static $single = false;                                                                 // Флаг выборки одной строки

    /**
     * Функция условия
     *
     * @param string || integer $where Текст условия
     * @return mixed
     */
    public function where($where) {

        $query_name = self::$queries[count(self::$queries) - 1]->name;                              // Имя текущей операции

        if(!$where)                                                                                 // Если аргумент отсутствует
            message::error('Missing argument for <b>where</b> in <b>' . $query_name . '</b> query');

        else if(gettype($where) == 'integer' || preg_match('/^\d+$/', $where)) {                    // иначе если аргумент имеется и это целое число или это строка, являющаяся числом

            if($query_name == 'select')                                                             // Если выполняется select
                self::$single = true;                                                               // нужно отметить, что к выборке требуется одна строка

            self::$where = ' where ' . self::$parameters[0] . '_id = ' . $where;
        }

        else if(gettype($where) == 'string')                                                        // иначе если аргумент имеется и это строка
            self::$where = ($where == 'all' || $where == '*') ? '' : ' where ' . $where;            // Если запрос выполняется для всех записей, то условие не нужно

        else                                                                                        // Иначе аргумент имеется, но у него неверный тип данных
            message::error('Wrong argument for <b>where</b> in <b>' . $query_name . '</b> query');

        return call_user_func_array(
            array('self', $query_name . '_query'),
            self::$parameters
        );
    }

    private static $limit;                                             // Переменная, хранящая значение для оператора limit

    /**
     * Функция добавления значения для оператора limit к запросу
     *
     * @param string $limit Значение оператора
     */
    public function limit($limit) {

        self::$limit = ' limit ' . $limit;

        return self::$object;
    }

    private static $fields;                                            // Переменная, хранящая значение для оператора select

    /**
     * Функция изменения полей в select запроса
     *
     * @param string $fields Перечисление полей
     */
    public function fields($fields) {

        self::$fields = $fields;

        return self::$object;
    }

    private static $addfields;                                         // Переменная, хранящая дополнительное значение для оператора select

    /**
     * Функция добавления полей в select запроса
     *
     * @param string $addfields Перечисление полей
     */
    public function addfields($addfields) {

        self::$addfields = ', ' . $addfields;

        return self::$object;
    }

    private static $order;                                             // Переменная, хранящая значение для оператора order

    /**
     * Функция добавления значения для оператора order к запросу
     *
     * @param string $order Значение оператора
     */
    public function order($order) {

        self::$order = ' order by ' . $order;

        return self::$object;
    }

    private static $group;                                             // Переменная, хранящая значение для оператора group

    /**
     * Функция добавления значения для оператора group к запросу
     *
     * @param string $group Значение оператора
     */
    public function group($group) {

        self::$group = ' group by ' . $group;

        return self::$object;
    }

    private static $subqueries;                                        // Переменная, хранящая подзапросы

    /**
     * Функция добавления подзапросов
     *
     * @param array $subqueries Массив с текстами подзапросов
     */
    public function sub($subqueries) {

        foreach($subqueries as $val => $key)
            self::$subqueries .= ', (' . $val . ') as ' . $key;

        return self::$object;
    }

    private static $prefix;                                            // Переменная, хранящая значение префикса

    /**
     * Функция добавления значения префикса для полей таблицы
     *
     * @param string $prefix Значение для префикса
     */
    public function prefix($prefix) {

        self::$prefix = $prefix;

        return self::$object;
    }

    private static $selection_operation = array('select', 'join');                                          // Операции, выполняющие выборку данных

    /**
     * Функция непосредственного выполнения запроса
     *
     * @param string $query SQL-запрос
     * @return boolean
     */
    private static function execute_query($query) {

        self::$queries[count(self::$queries) - 1]->query = $query;                                          // Запись в массив данных текста текущего запроса

        $start = microtime(true);                                                                           // Время начала выполнения запроса
        $result = self::$mysqli->query($query);                                                             // Выполнение самого запроса
        self::$queries[count(self::$queries) - 1]                                                           // Запись в массив данных
            ->duration = microtime(true) - $start;                                                          // длительности выполнения запроса

        if(!$result) {                                                                                      // Если запрос не был выполнен

            self::$queries[count(self::$queries) - 1]->result = '<b>error:</b> ' . self::$mysqli->error;    // Запись ошибки в результат выполнения запроса
            return false;                                                                                   // и возвращение отрицательного результата
        }
        else if(in_array(self::$queries[count(self::$queries) - 1]->name, self::$selection_operation)) {    // Иначе запрос был выполнен и если текущая операция относится к операциям, выполняющим выборку данных

            self::$queries[count(self::$queries) - 1]->result = 'complete: ' . $result->num_rows . ' rows'; // Запись количества выбранных строк в результат выполнения запроса
            return $result;                                                                                 // и возвращение результата выборки
        }
        else {                                                                                              // Иначе запрос успешно выполнен, но текущая операция не относится к выполняющим выборку

            self::$queries[count(self::$queries) - 1]->result = 'complete';                                 // Запись сообщения об успешном выполнении в качестве результата
            return true;                                                                                    // и возвращение положительного результата
        }
    }

    /**
     * Функция добавления информации по выполняемым запросам
     *
     */
    private static function set_debug($backtrace) {

        self::$queries[count(self::$queries) - 1]->file = $backtrace[0]['file'];                              // Запись в массив данных пути к файлу
        self::$queries[count(self::$queries) - 1]->line = $backtrace[0]['line'];                              // и строки, откуда был вызван запрос
    }

    /**
     * Функция вывода информации по отработанным запросам
     *
     */
    public static function debug() {

        echo "<pre><b>Queries debuger:</b>\n\n";

        $duration_sum = 0;

        foreach(self::$queries as $key => $val) {

            echo $key + 1 . " -> " . $val->name . " [\n"
                . "\t"   . "file     -> " . $val->file
                . "\n\t" . "line     -> " . $val->line
                . "\n\t" . "query    -> " . $val->query
                . "\n\t" . "duration -> " . $val->duration
                . "\n\t" . "result   -> " . $val->result
                . "\n]\n\n";

            $duration_sum += $val->duration;
        }

        echo "total [\n"
            . "\t" .   "count    -> " . count(self::$queries)
            . "\n\t" . "duration -> " . $duration_sum
            . "\n]</pre>";
    }

    /**
     * Функция вывода массива выборки в удобочитаемом виде
     *
     * @param array || object $query
     */
    public static function result($query) {

        if(gettype($query) == 'object')                                // Если параметр является объектом (одна строка в результате выборки)
            $table[0] = $query;                                        // то надо добавить его в массив
        else                                                           // Иначе это массив
            $table = $query;                                           // и его надо просто переприсвоить

        echo "<pre><b>Query result: </b>";

        if(count($table) == 0)                                         // Если выборка пуста
            echo "empty\n\n";
        else {                                                         // Если есть результаты выборки

            echo "\n\n";

            foreach($table as $num => $row) {                          // Цикл по строкам результата выборки

                echo $num + 1 . " -> [\n";

                foreach($row as $key => $val)                          // Цикл по полям текущей строки
                    echo "\t" . $key . " => " . $val . "\n";

                echo "]\n\n";
            }
        }

        echo "</pre>";
    }
}