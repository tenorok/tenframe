<?php

class joinTest extends PHPUnit_Framework_TestCase {

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

        $result = ten\join::files([
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

        $result = ten\join::files([
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

        $result = ten\join::files([
            'files' => [
                self::file('a.html'),
                self::file('b.css'),
                self::file('c.js')
            ],
            'before' => '{'
        ]);

        $this->assertEquals($result, '{a-html{b-css{c-js');
    }

    /**
     * Объединение с добавлением строки после каждого файла
     */
    public function testAfter() {

        $result = ten\join::files([
            'files' => [
                self::file('a.html'),
                self::file('b.css'),
                self::file('c.js')
            ],
            'after' => '}'
        ]);

        $this->assertEquals($result, 'a-html}b-css}c-js}');
    }

    /**
     * Объединение с добавлением строки перед и после каждого файла
     */
    public function testBeforeAfter() {

        $result = ten\join::files([
            'files' => [
                self::file('a.html'),
                self::file('b.css'),
                self::file('c.js')
            ],
            'before' => '{',
            'after' => '}'
        ]);

        $this->assertEquals($result, '{a-html}{b-css}{c-js}');
    }

    /**
     * Объединение с добавлением строки перед и после каждого файла и в начало объединения
     */
    public function testBeforeAfterStart() {

        $result = ten\join::files([
            'files' => [
                self::file('a.html'),
                self::file('b.css'),
                self::file('c.js')
            ],
            'before' => '{',
            'after' => '}',
            'start' => '['
        ]);

        $this->assertEquals($result, '[{a-html}{b-css}{c-js}');
    }

    /**
     * Объединение с добавлением строки перед и после каждого файла и в конец объединения
     */
    public function testBeforeAfterEnd() {

        $result = ten\join::files([
            'files' => [
                self::file('a.html'),
                self::file('b.css'),
                self::file('c.js')
            ],
            'before' => '{',
            'after' => '}',
            'end' => ']'
        ]);

        $this->assertEquals($result, '{a-html}{b-css}{c-js}]');
    }

    /**
     * Объединение с добавлением строки перед и после каждого файла и в начало и в конец объединения
     */
    public function testBeforeAfterStartEnd() {

        $result = ten\join::files([
            'files' => [
                self::file('a.html'),
                self::file('b.css'),
                self::file('c.js')
            ],
            'before' => '{',
            'after' => '}',
            'start' => '[',
            'end' => ']'
        ]);

        $this->assertEquals($result, '[{a-html}{b-css}{c-js}]');
    }
}