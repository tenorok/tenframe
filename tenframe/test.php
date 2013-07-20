<?php

/**
 * Тестирование классов
 */

require 'core.php';
ten\core::initTest();

class test {

    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite();
        $suite->addTestSuite('coreTest');
        return $suite;
    }
}