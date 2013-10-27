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

        if(!$this->isDepsExist($entity)){
            array_push($this->decl, $entity);
        }

        return $entity;
    }

    /**
     * Добавить зависимости сущности из shouldDeps
     *
     * @param array $dependency Зависимость сущности
     * @return array
     */
    private function addShould($dependency) {
        return $this->addDeps($dependency, 'shouldDeps', function($dependency) {
            array_push($this->decl, $dependency);
        });
    }

    /**
     * Добавить зависимости сущности из mustDeps
     *
     * @param array $dependency Зависимость сущности
     * @return array
     */
    private function addMust($dependency) {
        return $this->addDeps($dependency, 'mustDeps', function($dependency) {
            array_unshift($this->decl, $dependency);
        });
    }

    /**
     * Добавить зависимости сущности
     *
     * @param array $dependency Зависимость сущности
     * @param string $key Ключ зависимостей
     * @param callback $callback Функция с инструкциями по добавлению зависимости
     * @return array
     */
    private function addDeps($dependency, $key, $callback) {
        if(!array_key_exists($key, $dependency)) return $this->decl;

        // Если указана одна сущность
        if(parent::isAssoc($dependency[$key])) {
            $this->unsetExistDeps($dependency[$key]);
            $callback($dependency[$key]);
            return $this->decl;
        }

        // Указано несколько сущностей
        foreach($dependency[$key] as $curDependency) {
            $this->unsetExistDeps($curDependency);
            $callback($curDependency);
        }

        return $this->decl;
    }

    /**
     * Удалить зависимость, если она повторяется
     *
     * @param array $dependency Зависимость сущности
     * @return array
     */
    private function unsetExistDeps($dependency) {

        $existIndex = $this->isDepsExist($dependency);

        // Если такой зависимости ещё нет
        if(!$existIndex) return $this->decl;

        // Повтор зависимости, старую нужно удалить
        unset($this->decl[$existIndex]);

        return $this->decl;
    }

    /**
     * Является ли зависимость повторной
     *
     * @param array $dependency Зависимость сущности
     * @return number|boolean Индекс повторяющейся зависимости или false, если она не является повтором
     */
    private function isDepsExist($dependency) {

        foreach($this->decl as $index => $declDependency) {
            if($declDependency === $dependency) return $index;
        }

        return false;
    }
}