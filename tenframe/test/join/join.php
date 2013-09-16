<?php

class joinTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        // Перед каждым тестом удаляется тестовый файл
        $saved = self::save('saved.txt');
        file_exists($saved) && unlink($saved);
    }

    private static function file($file) {
        return ten\core::resolveRelativePath(__DIR__, 'files', $file);
    }

    private static function save($file) {
        return ten\core::resolveRelativePath(__DIR__, 'save', $file);
    }

    /**
     * Простое объединение
     */
    public function testSimple() {

        $join = new ten\join();

        $result = $join->combine([
            'files' => [
                self::file('a.html'),
                self::file('b.css'),
                self::file('c.js')
            ]
        ]);

        $this->assertEquals($result, 'a-htmlb-cssc-js');
    }

    /**
     * Объединение с сохранением в файл
     */
    public function testSave() {

        $join = new ten\join();

        $result = $join->combine([
            'files' => [
                self::file('a.html'),
                self::file('b.css'),
                self::file('c.js')
            ],
            'save' => self::save('saved.txt')
        ]);

        $this->assertEquals($result, 'a-htmlb-cssc-js');
        $this->assertEquals(file_get_contents(self::save('saved.txt')), 'a-htmlb-cssc-js');
    }

    /**
     * Объединение с добавлением строки перед каждым файлом
     */
    public function testBefore() {

        $join = new ten\join([
            'before' => '{'
        ]);

        $result = $join->combine([
            'files' => [
                self::file('a.html'),
                self::file('b.css'),
                self::file('c.js')
            ]
        ]);

        $this->assertEquals($result, '{a-html{b-css{c-js');
    }

    /**
     * Объединение с добавлением строки после каждого файла
     */
    public function testAfter() {

        $join = new ten\join([
            'after' => '}'
        ]);

        $result = $join->combine([
            'files' => [
                self::file('a.html'),
                self::file('b.css'),
                self::file('c.js')
            ]
        ]);

        $this->assertEquals($result, 'a-html}b-css}c-js}');
    }

    /**
     * Объединение с добавлением строки перед и после каждого файла
     */
    public function testBeforeAfter() {

        $join = new ten\join([
            'before' => '{',
            'after' => '}'
        ]);

        $result = $join->combine([
            'files' => [
                self::file('a.html'),
                self::file('b.css'),
                self::file('c.js')
            ]
        ]);

        $this->assertEquals($result, '{a-html}{b-css}{c-js}');
    }

    /**
     * Объединение с добавлением строки перед и после каждого файла и в начало объединения
     */
    public function testBeforeAfterStart() {

        $join = new ten\join([
            'before' => '{',
            'after' => '}',
            'start' => '['
        ]);

        $result = $join->combine([
            'files' => [
                self::file('a.html'),
                self::file('b.css'),
                self::file('c.js')
            ]
        ]);

        $this->assertEquals($result, '[{a-html}{b-css}{c-js}');
    }

    /**
     * Объединение с добавлением строки перед и после каждого файла и в конец объединения
     */
    public function testBeforeAfterEnd() {

        $join = new ten\join([
            'before' => '{',
            'after' => '}',
            'end' => ']'
        ]);

        $result = $join->combine([
            'files' => [
                self::file('a.html'),
                self::file('b.css'),
                self::file('c.js')
            ]
        ]);

        $this->assertEquals($result, '{a-html}{b-css}{c-js}]');
    }

    /**
     * Объединение с добавлением строки перед и после каждого файла и в начало и в конец объединения
     */
    public function testBeforeAfterStartEnd() {

        $join = new ten\join([
            'before' => '{',
            'after' => '}',
            'start' => '[',
            'end' => ']'
        ]);

        $result = $join->combine([
            'files' => [
                self::file('a.html'),
                self::file('b.css'),
                self::file('c.js')
            ]
        ]);

        $this->assertEquals($result, '[{a-html}{b-css}{c-js}]');
    }

    /**
     * Объединение с добавлением строки перед и после каждого файла с переменной {filename}
     */
    public function testBeforeAfterFilename() {

        $join = new ten\join([
            'before' => '({filename})',
            'after' => ';'
        ]);

        list($a, $b, $c) = [self::file('a.html'), self::file('b.css'), self::file('c.js')];

        $result = $join->combine([
            'files' => [$a, $b, $c]
        ]);

        $this->assertEquals($result, "($a)a-html;($b)b-css;($c)c-js;");
    }

    /**
     * Объединение с добавлением строки перед и после каждого файла с повторяющейся переменной {filename}
     */
    public function testBeforeAfterFilename2() {

        $join = new ten\join([
            'before' => '({filename})',
            'after' => '({filename}|{filename});'
        ]);

        list($a, $b, $c) = [self::file('a.html'), self::file('b.css'), self::file('c.js')];

        $result = $join->combine([
            'files' => [$a, $b, $c]
        ]);

        $this->assertEquals($result, "($a)a-html($a|$a);($b)b-css($b|$b);($c)c-js($c|$c);");
    }
}