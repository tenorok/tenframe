<?php

class controllerRouteTest {

    public static function simple() {
        return true;
    }

    public static function param() {
        return ten\route::url()->post;
    }

    public static function params() {
        $params = ten\route::url();
        return array(
            'day'   => $params->day,
            'month' => $params->month,
            'year'  => $params->year
        );
    }

    public static function data($data) {
        return $data;
    }
}