<?php

namespace ten;

class doc extends core {

    function __construct($text = '') {
        $this->text = $text;
    }

    public function html() {
        $body = $this->getDocBody();
        $markdown = $this->getDocMarkdown($body);
        return markdown::html($markdown);
    }

    private function getDocBody() {

        preg_match(
            '/' .
                '\s*\/\*\* doc\n' .
                    '((?:\s+\*\s*.*)*)' .
                '\s+\*\/' .
            '/i',
            $this->text,
            $matches
        );

        return $matches[1];
    }

    private function getDocMarkdown($body) {
        return preg_replace('/(\s+\*\s)/', "\n", $body);
    }
}