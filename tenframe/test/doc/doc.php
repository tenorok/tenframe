<?php

class docTest extends PHPUnit_Framework_TestCase {

    private static function text($file) {
        return file_get_contents(ten\core::resolveRealPath(__DIR__, 'text', $file));
    }

    private static function result($key) {

        $results = [
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
                "</ol>\n"
        ];

        return $results[$key];
    }

    /**
     * Однострочный маркдаун
     */
    public function testSimple() {

        $doc = new ten\doc(self::text('simple.php'));
        $this->assertEquals($doc->html(), "<h1>Hello world!</h1>\n");
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
        //        $doc->addText('text')->addFile('filename.php')->html();

        $this->assertEquals(
            $doc->file(ten\core::resolveRealPath(__DIR__, 'text', 'multi.php'))->html(),
            self::result('multi')
        );
    }

}