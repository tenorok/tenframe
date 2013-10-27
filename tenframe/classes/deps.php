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
            return array_merge($this->decl, $dependency);
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
            return array_merge($dependency, $this->decl);
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
            return $this->expandDeps($dependency[$key], $callback);
        }

        // Указано несколько сущностей
        foreach($dependency[$key] as $curDependency) {
            $this->expandDeps($curDependency, $callback);
        }

        return $this->decl;
    }

    /**
     * Развернуть сахарные поля
     *
     * @param array $dependency Зависимость сущности
     * @param callback $callback Функция с инструкциями по добавлению зависимости
     * @return array
     */
    private function expandDeps($dependency, $callback) {

        $expandedDependencies = array_merge(
            [$this->getClearDepsEntity($dependency)],
            $this->expandElems($dependency),
            $this->expandMods($dependency)
        );

        foreach($expandedDependencies as $expandedDependency) {
            $this->unsetExistDeps($expandedDependency);
        }

        return $this->decl = $callback($expandedDependencies);
    }

    /**
     * Получить чистую сущность без сахарных полей
     *
     * @param array $dependency Зависимость сущности
     * @return array
     */
    private function getClearDepsEntity($dependency) {

        foreach(['elems', 'mods', 'vals'] as $expandField) {
            unset($dependency[$expandField]);
        }

        return $dependency;
    }

    /**
     * Развернуть сахарное поле elems
     *
     * @param array $dependency Зависимость сущности
     * @return array
     */
    private function expandElems($dependency) {
        return $this->expandKey('elems', $dependency, function($index, $elem) use ($dependency) {
            return [
                'block' => $dependency['block'],
                'elem' => $elem
            ];
        });
    }

    /**
     * Развернуть сахарное поле mods
     *
     * @param array $dependency Зависимость сущности
     * @return array
     */
    private function expandMods($dependency) {

        return $this->expandKey('mods', $dependency, function($mod, $val) use ($dependency) {

            $expand = [
                'block' => $dependency['block'],
                'mod' => $mod,
                'val' => $val
            ];

            if(array_key_exists('elem', $dependency)) {
                $expand['elem'] = $dependency['elem'];
            }

            return $expand;
        });
    }

    /**
     * Помощник для развёртывания сахарных полей
     *
     * @param string $key Имя сахарного поля
     * @param array $dependency Зависимость сущности
     * @param callback $callback Функция возвращающая развёрнутую зависимость
     * @return array
     */
    private function expandKey($key, $dependency, $callback) {
        if(!array_key_exists($key, $dependency)) return [];

        $expandKey = [];

        foreach($dependency[$key] as $index => $value) {
            array_push($expandKey, $callback($index, $value));
        }

        return $expandKey;
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