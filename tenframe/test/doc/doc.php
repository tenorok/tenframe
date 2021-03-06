<?php

class docTest extends PHPUnit_Framework_TestCase {

    private static function text($file) {
        return file_get_contents(ten\core::resolveRealPath(__DIR__, 'text', $file));
    }

    private static function result($key) {

        $results = [

            'simple' => "<h1>Hello world!</h1>\n",

            'multi' => "<h1>Header first level</h1>\n\n" .
                "<h1>Header first level</h1>\n\n" .
                "<h2>Header second level</h2>\n\n" .
                "<h2>Header second level</h2>\n\n" .
                "<h3>Header third level</h3>\n\n" .
                "<p>Paragraph with <em>italic</em> and <strong>bold</strong> text.</p>\n\n" .
                "<blockquote>\n" .
                "  <p>Text in blockquote</p>\n" .
                "</blockquote>\n\n" .
                "<p><a href=\"//localhost\">Link to localhost</a></p>\n\n" .
                "<p>List:</p>\n\n" .
                "<ul>\n" .
                "<li>first</li>\n" .
                "<li>second</li>\n" .
                "</ul>\n\n" .
                "<p>Numbered list:</p>\n\n" .
                "<ol>\n" .
                "<li>One</li>\n" .
                "<li>Two</li>\n" .
                "<li>Three</li>\n" .
                "</ol>\n\n" .
                "<p><code>var a = new Doc();</code></p>\n\n" .
                "<pre><code>var b = 502;\n" .
                "</code></pre>\n"
        ];

        return $results[$key];
    }

    /**
     * Однострочный маркдаун
     */
    public function testSimple() {

        $doc = new ten\doc(self::text('simple.php'));
        $this->assertEquals($doc->html(), self::result('simple'));
    }

    /**
     * Насыщенный маркдаун
     */
    public function testMulti() {

        $doc = new ten\doc(self::text('multi.php'));

        $this->assertEquals(
            $doc->html(),
            self::result('multi')
        );
    }

    /**
     * Несколько блоков документации
     */
    public function testSome() {

        $doc = new ten\doc(self::text('some.php'));

        $this->assertEquals(
            $doc->html(),
            self::result('multi')
        );
    }

    /**
     * Добавление текста в отдельном методе
     */
    public function testText() {

        $doc = new ten\doc();

        $this->assertEquals(
            $doc->text(self::text('some.php'))->html(),
            self::result('multi')
        );
    }

    /**
     * Добавление текста из файла в отдельном методе
     */
    public function testFile() {

        $doc = new ten\doc();

        $this->assertEquals(
            $doc->file(ten\core::resolveRealPath(__DIR__, 'text', 'multi.php'))->html(),
            self::result('multi')
        );
    }

    /**
     * Добавление нескольких кусков текста отдельными методами
     */
    public function testAddText() {

        $doc = new ten\doc();

        $this->assertEquals(

            $doc
                ->addText(self::text('simple.php'))
                ->addText(self::text('multi.php'))
                ->html(),

            self::result('simple') .
            "\n" .
            self::result('multi')
        );
    }

    /**
     * Добавление нескольких кусков текста из файлов отдельными методами
     */
    public function testAddFile() {

        $doc = new ten\doc();

        $this->assertEquals(

            $doc
                ->addFile(ten\core::resolveRealPath(__DIR__, 'text', 'simple.php'))
                ->addFile(ten\core::resolveRealPath(__DIR__, 'text', 'multi.php'))
                ->html(),

            self::result('simple') .
            "\n" .
            self::result('multi')
        );
    }

}