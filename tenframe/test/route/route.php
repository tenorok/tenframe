<?php

class routeTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        // Перед каждым тестом маршрут не должен считаться проведённым
        ten\route::$called = false;
    }

    private static function method($method) {
        $_SERVER['REQUEST_METHOD'] = strtoupper($method);
    }

    /**
     * Имитация запроса
     *
     * @param string $method get/post/put/etc
     * @param string $path   Запрос
     */
    private static function request($method, $path) {
        self::method($method);
        ten\test\env::setTestUrl($path);
    }

    /**
     * Простой маршрут
     */
    public function testGetSimple() {

        self::request('get', '/path/to/');

        $this->assertNull(ten\route::get(array(
            'url' => '/',
            'call' => 'controllerRouteTest::simple'
        )));

        $this->assertTrue(ten\route::get(array(
            'url' => '/path/to',
            'call' => 'controllerRouteTest::simple'
        )));
    }

    /**
     * В режиме разработки
     */
    public function testGetDev() {

        ten\test\env::define('DEV', false);
        self::request('get', '/path/to/');

        $this->assertNull(ten\route::get(array(
            'url' => '/',
            'call' => 'controllerRouteTest::simple',
            'dev' => true
        )));

        $this->assertNull(ten\route::get(array(
            'url' => '/path/to',
            'call' => 'controllerRouteTest::simple',
            'dev' => true
        )));
    }

    /**
     * Массив маршрутов
     */
    public function testGetArray() {

        self::request('get', '/user/100/');

        $this->assertNull(ten\route::get(array(
            'url' => array(
                '/',
                '/user/200'
            ),
            'call' => array('controllerRouteTest', 'simple')
        )));

        $this->assertTrue(ten\route::get(array(
            'url' => array(
                '/user/',
                'user/100'
            ),
            'call' => array('controllerRouteTest', 'simple')
        )));
    }

    /**
     * Маршруты с одним параметром
     *
     * @dataProvider providerGetParam
     */
    public function testGetParam($path, $param) {

        self::request('get', $path);

        $this->assertNull(ten\route::get(array(
            'url' => '/',
            'call' => 'controllerRouteTest::param'
        )));

        $this->assertNull(ten\route::get(array(
            'url' => '/post/500',
            'call' => 'controllerRouteTest::param'
        )));

        $this->assertEquals(
            ten\route::get(array(
                'url' => '/post/{post}',
                'call' => 'controllerRouteTest::param'
            )),
            $param
        );

        $this->assertNull(ten\route::get(array(
            'url' => '/post/{post}',
            'call' => 'controllerRouteTest::param'
        )));
    }

    public function providerGetParam() {
        return array(
            array('/post/502', 502),
            array('/post/new/', 'new'),
            array('/post/true', 'true')
        );
    }

    /**
     * Маршруты с несколькими параметрами
     *
     * @dataProvider providerGetParams
     */
    public function testGetParams($path, $params) {

        self::request('get', $path);

        $this->assertNull(ten\route::get(array(
            'url' => '/date/',
            'call' => 'controllerRouteTest::params'
        )));

        $this->assertEquals(
            ten\route::get(array(
                'url' => '/date/{day}/{month}/{year}/',
                'call' => 'controllerRouteTest::params'
            )),
            $params
        );
    }

    public function providerGetParams() {
        return array(
            array('/date/16/06/1990/', array(
                'day' => 16,
                'month' => 06,
                'year' => 1990
            )),
            array('/date/1/january/1970/', array(
                'day' => 1,
                'month' => 'january',
                'year' => 1970
            )),
            array('/date/null/false/1970/', array(
                'day' => 'null',
                'month' => 'false',
                'year' => 1970
            ))
        );
    }

    /**
     * Маршруты с проверкой на параметры
     *
     * @dataProvider providerGetParamsRule
     */
    public function testGetParamsRule($path, $params) {

        self::request('get', $path);

        $this->assertEquals(
            ten\route::get(array(
                'url' => '/date/{day}/{month}/{year}/',
                'call' => 'controllerRouteTest::params',
                'rule' => array(
                    'day' => '/^\d+$/',
                    'year' => '/^\d+$/',
                )
            )),
            $params
        );
    }

    public function providerGetParamsRule() {
        return array(
            array('/date/16/june/1990/', array(
                'day' => 16,
                'month' => 'june',
                'year' => 1990
            )),
            array('/date/first/1/1970/', false),
            array('/date/18/03/space/', false)
        );
    }

    /**
     * Тестирование проверочных шаблонов
     *
     * @dataProvider providerGetRuleTemplates
     */
    public function testGetRuleTemplates($ruleTemplate, $value, $result = 1) {

        self::request('get', '/post/' . $value);

        $unexpected = array(
            'natural' => 'int'
        );

        $resultVal = ten\route::get(array(
            'url' => '/post/{post}/',
            'call' => 'controllerRouteTest::param',
            'rule' => array(
                'post' => '(' . $ruleTemplate . ')'
            )
        ));

        $this->assertEquals($resultVal, $result !== 1? $result : $value);

        if(!is_null($result)) {
            $this->assertInternalType(
                isset($unexpected[$ruleTemplate])? $unexpected[$ruleTemplate] : $ruleTemplate,
                $resultVal
            );
        }
    }

    public function providerGetRuleTemplates() {
        return array(
            array('numeric', -100),
            array('numeric', -24.7),
            array('numeric', 0),
            array('numeric', 001),
            array('numeric', 200),
            array('numeric', 36.6),

            array('int', -100),
            array('int', -24.7, null),
            array('int', 0),
            array('int', 001),
            array('int', 200),
            array('int', 36.6, null),

            array('float', -100, null),
            array('float', -24.7),
            array('float', 0, null),
            array('float', 001, null),
            array('float', 200, null),
            array('float', 36.6),

            array('natural', -100, null),
            array('natural', -24.7, null),
            array('natural', 0, null),
            array('natural', 001),
            array('natural', 200),
            array('natural', 36.6, null),

            array('bool', 'true', true),
            array('bool', 'false', false),
            array('bool', 100, null),
            array('bool', 'not', null)
        );
    }

    /**
     * Проверка передачи данных
     *
     * @dataProvider providerGetData
     */
    public function testGetData($data, $get) {

        self::request('get', '/data/?' . $data);
        $_GET = $get;

        $this->assertEquals(
            ten\route::get([
                'url' => '/data/',
                'call' => 'controllerRouteTest::data'
            ]),
            $get
        );
    }

    public function providerGetData() {
        return [
            ['day=16&month=june&year=1990', [
                'day' => 16,
                'month' => 'june',
                'year' => 1990
            ]],
            ['days[]=16&days[]=20&month[]=june&month[]=march&year=1990', [
                'day' => [16, 20],
                'month' => ['june', 'march'],
                'year' => 1990
            ]]
        ];
    }

    /**
     * Проверка передачи данных в POST
     *
     * @dataProvider providerPostData
     */
    public function testPostData($post) {

        self::request('post', '/data/');
        $_POST = $post;

        $this->assertEquals(
            ten\route::post([
                'url' => '/data/',
                'call' => 'controllerRouteTest::data'
            ]),
            $post
        );
    }

    public function providerPostData() {
        return [
            [
                'day' => 16,
                'month' => 'june',
                'year' => 1990
            ],
            [
                'day' => [16, 20],
                'month' => ['june', 'march'],
                'year' => 1990
            ]
        ];
    }

    /**
     * GET и POST вместе
     */
    public function testGetPostSimple() {

        self::request('get', '/path/to/');

        $this->assertNull(ten\route::get(array(
            'url' => '/',
            'call' => 'controllerRouteTest::simple'
        )));

        $this->assertNull(ten\route::post(array(
            'url' => '/path/to/',
            'call' => 'controllerRouteTest::simple'
        )));

        $this->assertTrue(ten\route::get(array(
            'url' => '/path/to',
            'call' => 'controllerRouteTest::simple'
        )));
    }
}