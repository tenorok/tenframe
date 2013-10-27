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
            $this->addEntity($dependency);
            $this->addShould($dependency);
            $this->addMust($dependency);
        }

        return $this->decl;
    }

    /**
     * Получить массив с информацией о сущности для которой обрабатываются зависимости
     *
     * @param array $dependency Зависимость сущности
     * @return array
     */
    private function addEntity($dependency) {

        $entity = [];

        foreach(['block', 'elem', 'mod', 'val'] as $key) {
            if(array_key_exists($key, $dependency)) {
                $entity[$key] = $dependency[$key];
            }
        }

        array_push($this->decl, $entity);
        return $entity;
    }

    private function addShould($dependency) {
        if(!array_key_exists('shouldDeps', $dependency)) return $this->decl;

        $shouldDeps = $dependency['shouldDeps'];

        // Если указана одна сущность
        if(parent::isAssoc($shouldDeps)) {
            array_push($this->decl, $shouldDeps);
            return $this->decl;
        }

        // Указано несколько сущностей
        foreach($shouldDeps as $shouldDependency) {
            array_push($this->decl, $shouldDependency);
        }

        return $this->decl;
    }

    private function addMust($dependency) {
        if(!array_key_exists('mustDeps', $dependency)) return $this->decl;

        $mustDeps = $dependency['mustDeps'];

        // Если указана одна сущность
        if(parent::isAssoc($mustDeps)) {
            array_unshift($this->decl, $mustDeps);
            return $this->decl;
        }

        // Указано несколько сущностей
        foreach($mustDeps as $mustDependency) {
            array_unshift($this->decl, $mustDependency);
        }

        return $this->decl;
    }
}