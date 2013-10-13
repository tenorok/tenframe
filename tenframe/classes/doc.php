<?php

/** doc
 *
 * # Парсер документации
 *
 * Документация хранится в произвольных местах исходных файлов.
 * Формат похож на PHPDoc.
 * Начало документации должна открывать последовательность `/** doc`.
 * Каждая новая строка начинается со звёздочки, а окончание документации символизирует звёздочка со слешем.
 *
 * Если в файле имеется несколько блоков документации, то все они будут обработаны.
 *
 * ## Примеры
 *
 * Простое получение документации:
 *
 *     $doc = new ten\doc('/** doc ...');
 *     echo $doc->html();
 *
 * Использование метода `text()`. Эта запись аналогична предыдущему примеру:
 *
 *     $doc = new ten\doc();
 *     $doc->text('/** doc ...')->html();
 *
 * Использование метода `addText()`. Конкатенирует текст к добавленному ранее:
 *
 *     $doc = new ten\doc();
 *     $doc
 *         ->addText('first file /** doc ...')
 *         ->addText('second file /** doc ...')
 *         ->html();
 *
 * Использование метода `file()`. Принимает полный путь до файла и обрабатывает его содержимое:
 *
 *     $doc = new ten\doc();
 *     $doc->file('/Users/user/project/file.php')->html();
 *
 * Использование метода `addFile()`. Работает аналогично методу `file`, но вместо переопределения обрабатываемого текста, конкатенирует содержимое:
 *
 *     $doc = new ten\doc();
 *     $doc
 *         ->addFile('/Users/user/project/file1.php')
 *         ->addFile('/Users/user/project/file2.php')
 *         ->html();
 *
 */

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