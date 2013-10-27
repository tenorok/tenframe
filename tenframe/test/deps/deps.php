<?php

class depsTest extends PHPUnit_Framework_TestCase {

    function testAB() {

        $deps = new ten\deps([
            'block' => 'a',
            'shouldDeps' => [ 'block' => 'b' ]
        ]);

        $this->assertEquals($deps->getDecl(), [
            [ 'block' => 'a' ],
            [ 'block' => 'b' ]
        ]);
    }

    function testABC() {

        $deps = new ten\deps([
            'block' => 'a',
            'shouldDeps' => [
                [ 'block' => 'b' ],
                [ 'block' => 'c' ]
            ]
        ]);

        $this->assertEquals($deps->getDecl(), [
            [ 'block' => 'a' ],
            [ 'block' => 'b' ],
            [ 'block' => 'c' ]
        ]);
    }

    function testABMust() {

        $deps = new ten\deps([
            'block' => 'a',
            'mustDeps' => [
                [ 'block' => 'b' ]
            ]
        ]);

        $this->assertEquals($deps->getDecl(), [
            [ 'block' => 'b' ],
            [ 'block' => 'a' ]
        ]);
    }

    function testABCMust() {

        $deps = new ten\deps([
            'block' => 'a',
            'mustDeps' => [
                [ 'block' => 'b' ],
                [ 'block' => 'c' ]
            ]
        ]);

        $this->assertEquals($deps->getDecl(), [
            [ 'block' => 'c' ],
            [ 'block' => 'b' ],
            [ 'block' => 'a' ]
        ]);
    }

    function testABShouldCMust() {

        $deps = new ten\deps([
            'block' => 'a',
            'shouldDeps' => [ 'block' => 'b' ],
            'mustDeps' => [ 'block' => 'c' ]
        ]);

        $this->assertEquals($deps->getDecl(), [
            [ 'block' => 'c' ],
            [ 'block' => 'a' ],
            [ 'block' => 'b' ]
        ]);
    }

    function testABCShouldDEMust() {

        $deps = new ten\deps([
            'block' => 'a',
            'shouldDeps' => [
                [ 'block' => 'b' ],
                [ 'block' => 'c' ]
            ],
            'mustDeps' => [
                [ 'block' => 'd' ],
                [ 'block' => 'e' ]
            ]
        ]);

        $this->assertEquals($deps->getDecl(), [
            [ 'block' => 'e' ],
            [ 'block' => 'd' ],
            [ 'block' => 'a' ],
            [ 'block' => 'b' ],
            [ 'block' => 'c' ]
        ]);
    }

    function testArrayABCD() {

        $deps = new ten\deps([
            [
                'block' => 'a',
                'shouldDeps' => [ 'block' => 'b' ]
            ],
            [
                'block' => 'c',
                'shouldDeps' => [ 'block' => 'd' ]
            ]
        ]);

        $this->assertEquals($deps->getDecl(), [
            [ 'block' => 'a' ],
            [ 'block' => 'b' ],
            [ 'block' => 'c' ],
            [ 'block' => 'd' ]
        ]);
    }

    function testArrayABCShouldDEMustAndFGHShouldIJMust() {

        $deps = new ten\deps([
            [
                'block' => 'a',
                'shouldDeps' => [
                    [ 'block' => 'b' ],
                    [ 'block' => 'c' ]
                ],
                'mustDeps' => [
                    [ 'block' => 'd' ],
                    [ 'block' => 'e' ]
                ]
            ],
            [
                'block' => 'f',
                'shouldDeps' => [
                    [ 'block' => 'g' ],
                    [ 'block' => 'h' ]
                ],
                'mustDeps' => [
                    [ 'block' => 'i' ],
                    [ 'block' => 'j' ]
                ]
            ]
        ]);

        $this->assertEquals($deps->getDecl(), [
            [ 'block' => 'j' ],
            [ 'block' => 'i' ],
            [ 'block' => 'e' ],
            [ 'block' => 'd' ],
            [ 'block' => 'a' ],
            [ 'block' => 'b' ],
            [ 'block' => 'c' ],
            [ 'block' => 'f' ],
            [ 'block' => 'g' ],
            [ 'block' => 'h' ]
        ]);
    }

    function testRepeatABC() {

        $deps = new ten\deps([
            [
                'block' => 'a',
                'shouldDeps' => [ 'block' => 'b' ]
            ],
            [
                'block' => 'b',
                'shouldDeps' => [ 'block' => 'c' ]
            ]
        ]);

        $this->assertEquals($deps->getDecl(), [
            [ 'block' => 'a' ],
            [ 'block' => 'b' ],
            [ 'block' => 'c' ]
        ]);
    }

    function testRepeatACB() {

        $deps = new ten\deps([
            [
                'block' => 'a',
                'shouldDeps' => [ 'block' => 'b' ]
            ],
            [
                'block' => 'c',
                'mustDeps' => [ 'block' => 'b' ]
            ]
        ]);

        $this->assertEquals($deps->getDecl(), [
            [ 'block' => 'b' ],
            [ 'block' => 'a' ],
            [ 'block' => 'c' ]
        ]);
    }

    function testRepearABCShouldDEMustAndFGHShouldIJMust() {

        $deps = new ten\deps([
            [
                'block' => 'a',
                'shouldDeps' => [
                    [ 'block' => 'b' ],
                    [ 'block' => 'c' ]
                ],
                'mustDeps' => [
                    [ 'block' => 'd' ],
                    [ 'block' => 'e' ]
                ]
            ],
            [
                'block' => 'c',
                'shouldDeps' => [
                    [ 'block' => 'i' ],
                    [ 'block' => 'a' ]
                ],
                'mustDeps' => [
                    [ 'block' => 'd' ],
                    [ 'block' => 'b' ]
                ]
            ]
        ]);

        $this->assertEquals($deps->getDecl(), [
            [ 'block' => 'b' ],
            [ 'block' => 'd' ],
            [ 'block' => 'e' ],
            [ 'block' => 'c' ],
            [ 'block' => 'i' ],
            [ 'block' => 'a' ]
        ]);
    }
}