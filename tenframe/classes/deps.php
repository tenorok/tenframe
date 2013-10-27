<?php

namespace ten;

class deps extends core {

    /**
     * Массив зависимостей для обработки
     *
     * @var array
     */
    private $deps;

    /**
     * Массив результирующей декларации
     *
     * @var array
     */
    private $decl;

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
            $this->expandModsList($this->expandElems($dependency)),
            $this->expandModsVals($this->expandMods($dependency))
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

        foreach(['elems', 'mods'] as $expandField) {
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
            return is_string($elem)
                ? [ 'block' => $dependency['block'], 'elem' => $elem ]
                : parent::addNotExistField($elem, 'block', $dependency['block']);
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
            return parent::copyField('elem', $dependency, [
                'block' => $dependency['block'],
                'mod' => $mod,
                'val' => $val
            ]);
        });
    }

    /**
     * Развернуть список зависимостей с сахарным полем mods
     *
     * @param array $dependencyList Массив зависимостей
     * @return array
     */
    private function expandModsList($dependencyList) {

        $expand = [];

        foreach($dependencyList as $dependency) {

            array_push($expand, $this->getClearDepsEntity($dependency));

            $expandMods = $this->expandMods($dependency);
            if($expandMods) {
                $expand = array_merge($expand, $expandMods);
            }
        }

        return $expand;
    }

    /**
     * Развернуть сахарный массив в качестве значения модификатора
     *
     * @param array $expandMods Сущности, развёрнутые в результате выполнения expandMods
     * @return array
     */
    private function expandModsVals($expandMods) {

        $vals = [];

        foreach($expandMods as $dependency) {

            if(!array_key_exists('val', $dependency) || !is_array($dependency['val'])) {
                array_push($vals, $dependency);
                continue;
            }

            $vals = array_merge($vals, $this->expandKey('val', $dependency, function($index, $val) use ($dependency) {
                return parent::copyField('elem', $dependency, [
                    'block' => $dependency['block'],
                    'mod' => $dependency['mod'],
                    'val' => $val
                ]);

            }));
        }

        return $vals;
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