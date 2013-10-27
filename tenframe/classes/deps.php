<?php

namespace ten;

class deps extends core {

    /**
     * Конструктор
     *
     * @param array $deps Одна зависимость или массив зависимостей
     */
    function __construct($deps = []) {
        $this->deps = parent::isAssoc($deps) ? [$deps] : $deps;

        $this->decl = [];
    }

    public function add() {}

    /**
     * Получить декларацию на основе зависимостей
     *
     * @return array
     */
    public function getDecl() {

        foreach($this->deps as $dependency) {
            array_push($this->decl, $this->getEntity($dependency));
            array_push($this->decl, $this->getShould($dependency));
        }

        return $this->decl;
    }

    /**
     * Получить массив с информацией о сущности для которой обрабатываются зависимости
     *
     * @param array $dependency Зависимость сущности
     * @return array
     */
    private function getEntity($dependency) {

        $entity = [];

        foreach(['block', 'elem', 'mod', 'val'] as $key) {
            if(array_key_exists($key, $dependency)) {
                $entity[$key] = $dependency[$key];
            }
        }

        return $entity;
    }

    private function getShould($dependency) {
        if(!array_key_exists('shouldDeps', $dependency)) return false;

        $shouldDeps = $dependency['shouldDeps'];

        // Если указана одна сущность
        if(parent::isAssoc($shouldDeps)) {
            return $shouldDeps;
        }
    }
}