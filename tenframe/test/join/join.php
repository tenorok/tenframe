<?php

class joinTest extends PHPUnit_Framework_TestCase {

    public static function setUpBeforeClass() {

        // Константа DEV нужна для сохранения путей в debug
        ten\test\env::define('DEV', true);
    }

    protected function tearDown() {

        // После каждого теста удаляются тестовые файлы
        $files = ['saved.txt', 'saved.js', 'saved.css', 'saved.html'];
        foreach($files as $file) {
            $saved = self::save($file);
            file_exists($saved) && unlink($saved);
        }

        // Сбрасывается массив дебага
        ten\join::$debug = [];
    }

    private static function file($file) {
        return ten\core::resolveRealPath(__DIR__, 'files', $file);
    }

    private static function save($file) {
        return ten\core::resolveRelativePath(__DIR__, 'save', $file);
    }

    private static function directory() {
        return ten\core::resolveRealPath(__DIR__, 'files');
    }

    private static function directory2() {
        return ten\core::resolveRealPath(__DIR__, 'files2');
    }

    /**
     * Простое объединение
     */
    public function testSimple() {

        $join = new ten\join();

        $result = $join->combine([
            self::file('a.html'),
            self::file('b.css'),
            self::file('c.js')
        ]);

        $this->assertEquals($result, 'a-htmlb-cssc-js');
    }

    /**
     * Объединение с сохранением в файл
     */
    public function testSave() {

        $join = new ten\join();

        $result = $join->combine([
            self::file('a.html'),
            self::file('b.css'),
            self::file('c.js')
        ], [
            'save' => self::save('saved.txt')
        ]);

        $this->assertEquals($result, 'a-htmlb-cssc-js');
        $this->assertEquals(file_get_contents(self::save('saved.txt')), 'a-htmlb-cssc-js');
    }

    /**
     * Указание относительных путей
     */
    public function testRelative() {

        $join = new ten\join();

        $result = $join->combine([
            'tenframe/test/join/files/a.html',
            'tenframe/test/join/files/b.css',
            'tenframe/test/join/files/c.js'
        ], [
            'save' => 'tenframe/test/join/save/saved.txt'
        ]);

        $this->assertEquals($result, 'a-htmlb-cssc-js');
        $this->assertEquals(file_get_contents(self::save('saved.txt')), 'a-htmlb-cssc-js');

        $this->assertEquals(ten\join::$debug, [[
            'files' => [
                self::file('a.html'),
                self::file('b.css'),
                self::file('c.js')
            ],
            'save' => self::save('saved.txt')
        ]]);
    }

    /**
     * Указание относительных путей с базовой директорией
     */
    public function testRelativeResolve() {

        $join = new ten\join([
            'resolve' => 'tenframe/test/join/'
        ]);

        $result = $join->combine([
            'files/a.html',
            'files/b.css',
            'files/c.js'
        ], [
            'save' => 'save/saved.txt'
        ]);

        $this->assertEquals($result, 'a-htmlb-cssc-js');
        $this->assertEquals(file_get_contents(self::save('saved.txt')), 'a-htmlb-cssc-js');

        $this->assertEquals(ten\join::$debug, [[
            'files' => [
                self::file('a.html'),
                self::file('b.css'),
                self::file('c.js')
            ],
            'save' => self::save('saved.txt')
        ]]);
    }

    /**
     * Объединение с добавлением строки перед каждым файлом
     */
    public function testBefore() {

        $join = new ten\join([
            'before' => '{'
        ]);

        $result = $join->combine([
            self::file('a.html'),
            self::file('b.css'),
            self::file('c.js')
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
            self::file('a.html'),
            self::file('b.css'),
            self::file('c.js')
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
            self::file('a.html'),
            self::file('b.css'),
            self::file('c.js')
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
            self::file('a.html'),
            self::file('b.css'),
            self::file('c.js')
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
            self::file('a.html'),
            self::file('b.css'),
            self::file('c.js')
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
            self::file('a.html'),
            self::file('b.css'),
            self::file('c.js')
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

        $result = $join->combine([$a, $b, $c]);

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

        $result = $join->combine([$a, $b, $c]);

        $this->assertEquals($result, "($a)a-html($a|$a);($b)b-css($b|$b);($c)c-js($c|$c);");
    }

    /**
     * Объединение по расширению файла
     */
    public function testExtension() {

        $join = new ten\join([
            'directory' => self::directory()
        ]);

        $result = $join->extension('css');

        $this->assertEquals($result, 'b-cssd-cssf-cssaa-csscc-cssccc-css');
    }

    /**
     * Объединение по расширению файла с указанием глубины рекурсии
     */
    public function testExtensionDepth() {

        $join = new ten\join([
            'directory' => self::directory(),
            'depth' => 1
        ]);

        $result = $join->extension('css');

        $this->assertEquals($result, 'b-cssd-cssf-cssaa-csscc-css');
    }

    /**
     * Объединение по нескольким расширениям файлов
     */
    public function testExtensions() {

        $join = new ten\join([
            'directory' => self::directory()
        ]);

        $result = $join->extension(['css', 'html']);

        $this->assertEquals($result, 'a-htmlb-cssd-cssf-cssaa-csscc-cssccc-cssddd-html');
    }

    /**
     * Объединение по расширению файла с приоритетами
     */
    public function testExtensionPriority() {

        $join = new ten\join([
            'directory' => self::directory()
        ]);

        $result = $join->extension('css', [
            'priority' => [
                self::file('/nested/nested/ccc.css'),
                self::file('/nested/bb.js')
            ]
        ]);

        $this->assertEquals($result, 'ccc-cssbb-jsb-cssd-cssf-cssaa-csscc-css');
    }

    /**
     * Объединение по расширению файла с приоритетами и относительными путями
     */
    public function testExtensionPriorityRelative() {

        $join = new ten\join([
            'directory' => 'tenframe/test/join/files/'
        ]);

        $result = $join->extension('css', [
            'priority' => [
                'tenframe/test/join/files/nested/nested/ccc.css',
                'tenframe/test/join/files/nested/bb.js'
            ],
            'save' => 'tenframe/test/join/save/saved.txt'
        ]);

        $this->assertEquals($result, 'ccc-cssbb-jsb-cssd-cssf-cssaa-csscc-css');
        $this->assertEquals(file_get_contents(self::save('saved.txt')), 'ccc-cssbb-jsb-cssd-cssf-cssaa-csscc-css');

        $this->assertEquals(ten\join::$debug, [[
            'files' => [
                self::file('nested/nested/ccc.css'),
                self::file('nested/bb.js'),
                self::file('b.css'),
                self::file('d.css'),
                self::file('f.css'),
                self::file('nested/aa.css'),
                self::file('nested/cc.css')
            ],
            'save' => self::save('saved.txt')
        ]]);
    }

    /**
     * Объединение по расширению файла с приоритетами и относительными путями и базовой директорией
     */
    public function testExtensionPriorityRelativeResolve() {

        $join = new ten\join([
            'resolve' => 'tenframe/test/join/',
            'directory' => 'files/'
        ]);

        $result = $join->extension('css', [
            'priority' => [
                'files/nested/nested/ccc.css',
                'files/nested/bb.js'
            ],
            'save' => 'save/saved.txt'
        ]);

        $this->assertEquals($result, 'ccc-cssbb-jsb-cssd-cssf-cssaa-csscc-css');
        $this->assertEquals(file_get_contents(self::save('saved.txt')), 'ccc-cssbb-jsb-cssd-cssf-cssaa-csscc-css');

        $this->assertEquals(ten\join::$debug, [[
            'files' => [
                self::file('nested/nested/ccc.css'),
                self::file('nested/bb.js'),
                self::file('b.css'),
                self::file('d.css'),
                self::file('f.css'),
                self::file('nested/aa.css'),
                self::file('nested/cc.css')
            ],
            'save' => self::save('saved.txt')
        ]]);
    }

    /**
     * Объединение по расширению файла из нескольких директорий
     */
    public function testExtensionDirectories() {

        $join = new ten\join([
            'directory' => [self::directory(), self::directory2()]
        ]);

        $result = $join->extension('css');

        $this->assertEquals($result, 'b-cssd-cssf-cssaa-csscc-cssccc-cssa2-cssb2-css');
    }

    /**
     * Объединение по нескольким расширениям файлов с сохранением в файл
     */
    public function testExtensionsSave() {

        $join = new ten\join([
            'directory' => self::directory()
        ]);

        $result = $join->extension(['css', 'html'], [
            'save' => self::save('saved.txt')
        ]);

        $rightResult = 'a-htmlb-cssd-cssf-cssaa-csscc-cssccc-cssddd-html';
        $this->assertEquals($result, $rightResult);
        $this->assertEquals(file_get_contents(self::save('saved.txt')), $rightResult);
    }

    /**
     * Объединение по нескольким расширениям из нескольких директорий с заданной глубиной и приоритетами с сохранением в файл
     */
    public function testExtensionsMulti() {

        $join = new ten\join([
            'directory' => [self::directory(), self::directory2()],
            'depth' => 1,

            'before' => '{',
            'after' => '}',
            'start' => '[',
            'end' => ']'
        ]);

        $result = $join->extension(['css', 'js'], [
            'priority' => [
                self::file('/nested/nested/aaa.js'),
                self::file('/nested/cc.css')
            ],
            'save' => self::save('saved.html')
        ]);

        $rightResult = '[{aaa-js}{cc-css}{b-css}{c-js}{d-css}{e-js}{f-css}{aa-css}{bb-js}{a2-css}{b2-css}{c2-js}{bb2-js}]';
        $this->assertEquals($result, $rightResult);
        $this->assertEquals(file_get_contents(self::save('saved.html')), $rightResult);
    }

    /**
     * Объединение по регулярному выражению
     */
    public function testRegexp() {

        $join = new ten\join([
            'directory' => self::directory()
        ]);

        $result = $join->regexp('/\.css$/');

        $this->assertEquals($result, 'b-cssd-cssf-cssaa-csscc-cssccc-css');
    }

    /**
     * Объединение по регулярному выражению из нескольких директорий с заданной глубиной и приоритетами с сохранением в файл
     */
    public function testRegexpMulti() {

        $join = new ten\join([
            'directory' => [self::directory(), self::directory2()],
            'depth' => 1,

            'before' => '{',
            'after' => '}',
            'start' => '[',
            'end' => ']'
        ]);

        $result = $join->regexp('/^[ac]{2,}2?\.(?:html|css)$/', [
            'priority' => [
                self::file('/a.html'),
                self::file('/nested/nested/ddd.html')
            ],
            'save' => self::save('saved.js')
        ]);

        $rightResult = '[{a-html}{ddd-html}{aa-css}{cc-css}{aa2-html}]';
        $this->assertEquals($result, $rightResult);
        $this->assertEquals(file_get_contents(self::save('saved.js')), $rightResult);
    }

}