<?php

namespace ten;

class doc extends core {

    /**
     * Конструктор
     *
     * @param string [$text] Текст с документацией
     */
    function __construct($text = '') {
        $this->text = $text;
    }

    /**
     * Задать текст для преобразования
     *
     * @param string $text Текст
     * @return $this
     */
    public function text($text) {
        $this->text = $text;
        return $this;
    }

    /**
     * Добавить текст для преобразования
     *
     * @param string $text Текст
     * @return $this
     */
    public function addText($text) {
        $this->text .= $text;
        return $this;
    }

    /**
     * Задать текст для преобразования из файла
     *
     * @param string $file Полный путь до файла
     * @return $this
     */
    public function file($file) {
        $this->text = file_get_contents($file);
        return $this;
    }

    /**
     * Добаить текст для преобразования из файла
     *
     * @param string $file Полный путь до файла
     * @return $this
     */
    public function addFile($file) {
        $this->text .= file_get_contents($file);
        return $this;
    }

    /**
     * Получить документацию в html
     *
     * @return string
     */
    public function html() {
        $body = $this->getDocBody();
        $markdown = $this->getDocMarkdown($body);
        return markdown::html($markdown);
    }

    /**
     * Получить тело документации из текста
     *
     * @return string
     */
    private function getDocBody() {

        preg_match_all(
            '/' .
                '\s*\/\*\* doc\n' .
                    '((?:\s+\*\s*.*)*)' .
                '\s+\*\/' .
            '/i',
            $this->text,
            $matches
        );

        return is_array($matches[1]) ? implode('', $matches[1]) : $matches[1];
    }

    /**
     * Получить чистый markdown из тела документации
     *
     * @param string $body Тело документации
     * @return string
     */
    private function getDocMarkdown($body) {
        return preg_replace('/\s+\*\s/', "\n", $body . "\n");
    }
}