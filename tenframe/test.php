<?php

/**
 * Тестирование классов
 */

require 'core.php';
ten\core::initTest();

class test {

    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite();

        $tests = array(
            'coreTest',
            'routeTest',
            'joinTest',
            'docTest'
        );

        foreach($tests as $test) {
            $suite->addTestSuite($test);
        }

        return $suite;
    }
}