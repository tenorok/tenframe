<?php

/**
 * Печать простых стилизованных данных на страницу
 * в терминах блока и его элементов
 * @version 0.0.1
 */

/* Использование

    Инициализация:
        $viewer = new ten\viewer(
            'blockname',                            // Имя блока
            array(                                  // Стили блока
                'padding' => '10px'
            ),
            array(                                  // Стили элементов блока
                'elem1' => array(
                    'margin' => '6px 0'
                ),
                'elem2' => array(
                    'font-size' => '16px'
                )
            )
        );

    Печать тега:
        echo $viewer->tag(
            'div',                                  // Обязательный. Имя тега
            'content',                              // Обязательный. Контент тега
            'elem'                                  // Имя элемента (без этого параметра в атрибуте class будет указан блок)
        );

    Печать стилей:
        echo $viewer->style();
*/

namespace ten;

class viewer extends core {

    /**
     * Конструктор
     *
     * @param string $blockname  Имя блока
     * @param array  $blockstyle Стили блока
     * @param array  $elemsstyle Элементы и их стили
     */
    public function __construct($blockname, $blockstyle, $elemsstyle) {
        $this->blockname = $blockname;
        $this->blockstyle = $blockstyle;
        $this->elemsstyle = $elemsstyle;
    }

    /**
     * Печать тега
     *
     * @param  string        $tag     Имя тега
     * @param  string        $content Контент тега
     * @param  string | bool $elem    Имя элемента
     * @return string                 Готовый тег
     */
    public function tag($tag, $content, $elem = false) {
        return
            '<' . $tag . ' class="' . $this->blockname .
            (($elem) ? '__' . $elem : '') .                             // Если элемент указан, то его надо напечатать
            '">' .
                $content .
            '</' . $tag . '>';
    }

    /**
     * Печать стилей
     *
     * @return string Стили
     */
    public function style() {
        return
            '<style type="text/css">' .
                  $this->blockRules() .
                  $this->elemsRules() .
            '</style>';
    }

    /**
     * Печать стилей блока
     *
     * @return string Стили блока
     */
    private function blockRules() {
        return $this->selector(
            $this->blockname,
            $this->properties(
                $this->blockstyle
            )
        );
    }

    /**
     * Печать стилей элементов
     *
     * @return string Стили элементов
     */
    private function elemsRules() {
        $rules = '';
        foreach($this->elemsstyle as $elem => $props) {
            $rules .= $this->selector(
                $this->blockname . '__' . $elem,
                $this->properties($props)
            );
        }
        return $rules;
    }

    /**
     * Печать селектора
     *
     * @param  string $selector   Селектор
     * @param  string $properties Правила
     * @return string             Блок селектора
     */
    private function selector($selector, $properties) {
        return
            '.' . $selector . '{' .
                $properties .
            '}';
    }

    /**
     * Печать правил для блока селектора
     *
     * @param  array $rules Массив стилей
     * @return string       Список правил
     */
    private function properties($rules) {
        $style = '';
        foreach($rules as $prop => $val) {
            $style .= $prop . ':' . $val . ';';
        }
        return $style;
    }
}