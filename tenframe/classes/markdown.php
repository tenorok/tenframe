<?php

/** doc
 *
 * # Парсер markdown
 *
 * Обёртка для tenframe над [dflydev-markdown](https://github.com/dflydev/dflydev-markdown).
 *
 * ## Пример
 *
 * Преобразование markdown в html:
 *     ten\markdown::html('# Hello World!');
 *
 */

namespace ten;

class markdown extends core {

    /**
     * Преобразование markdown в html
     *
     * @param string $markdown Строка в формате markdown
     * @return string
     */
    public static function html($markdown) {
        $markdownParser = new \dflydev\markdown\MarkdownExtraParser();
        return $markdownParser->transformMarkdown($markdown);
    }
}