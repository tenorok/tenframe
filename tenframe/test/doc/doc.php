<?php

class docTest extends PHPUnit_Framework_TestCase {

    private static function text($file) {
        return file_get_contents(ten\core::resolveRealPath(__DIR__, 'text', $file));
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
        //        $doc->text('text')->file('filename.php')->html();
        //        $doc->addText('text')->addFile('filename.php')->html();

        $this->assertEquals(
            $doc->html(),
            "<h1>Header first level</h1>\n\n" .
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
        );
    }

}