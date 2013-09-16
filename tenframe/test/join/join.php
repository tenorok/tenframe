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
}