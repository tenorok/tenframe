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

    function testABCelem() {

        $deps = new ten\deps([
            'block' => 'a',
            'shouldDeps' => [
                [
                    'block' => 'b'
                ],
                [
                    'block' => 'c',
                    'elem' => 'cc'
                ]
            ],
            'mustDeps' => [
                [
                    'block' => 'b',
                    'elem' => 'bb'
                ]
            ]
        ]);

        $this->assertEquals($deps->getDecl(), [
            [ 'block' => 'b', 'elem' => 'bb' ],
            [ 'block' => 'a' ],
            [ 'block' => 'b' ],
            [ 'block' => 'c', 'elem' => 'cc' ]
        ]);
    }

    function testABCDelem() {

        $deps = new ten\deps([
            'block' => 'a',
            'shouldDeps' => [
                [
                    'block' => 'b',
                    'mod' => 'c',
                    'val' => 'd'
                ],
                [
                    'block' => 'c',
                    'elem' => 'd'
                ]
            ],
            'mustDeps' => [
                'block' => 'b',
                'elem' => 'c',
                'mod' => 'd',
                'val' => 'e'
            ]
        ]);

        $this->assertEquals($deps->getDecl(), [
            [ 'block' => 'b', 'elem' => 'c', 'mod' => 'd', 'val' => 'e' ],
            [ 'block' => 'a' ],
            [ 'block' => 'b', 'mod' => 'c', 'val' => 'd' ],
            [ 'block' => 'c', 'elem' => 'd' ]
        ]);
    }

    function testABCelems() {

        $deps = new ten\deps([
            'block' => 'a',
            'shouldDeps' => [
                [
                    'block' => 'b',
                    'elems' => ['c', 'd']
                ],
                [
                    'block' => 'c',
                    'elem' => 'd'
                ]
            ],
            'mustDeps' => [
                'block' => 'd',
                'mod' => 'a',
                'val' => 'b',
                'elems' => ['e', 'f']
            ]
        ]);

        $this->assertEquals($deps->getDecl(), [
            [ 'block' => 'd', 'mod' => 'a', 'val' => 'b' ],
            [ 'block' => 'd', 'elem' => 'e' ],
            [ 'block' => 'd', 'elem' => 'f' ],
            [ 'block' => 'a' ],
            [ 'block' => 'b' ],
            [ 'block' => 'b', 'elem' => 'c' ],
            [ 'block' => 'b', 'elem' => 'd' ],
            [ 'block' => 'c', 'elem' => 'd' ]
        ]);
    }

    function testABCelemsArray() {

        $deps = new ten\deps([
            'block' => 'a',
            'shouldDeps' => [
                [
                    'block' => 'b',
                    'elems' => [
                        [ 'elem' => 'c' ],
                        [ 'elem' => 'd' ]
                    ]
                ],
                [
                    'block' => 'c',
                    'elem' => 'd'
                ]
            ],
            'mustDeps' => [
                'block' => 'd',
                'mod' => 'a',
                'val' => 'b',
                'elems' => [
                    [ 'elem' => 'e' ],
                    [ 'elem' => 'f' ]
                ]
            ]
        ]);

        $this->assertEquals($deps->getDecl(), [
            [ 'block' => 'd', 'mod' => 'a', 'val' => 'b' ],
            [ 'block' => 'd', 'elem' => 'e' ],
            [ 'block' => 'd', 'elem' => 'f' ],
            [ 'block' => 'a' ],
            [ 'block' => 'b' ],
            [ 'block' => 'b', 'elem' => 'c' ],
            [ 'block' => 'b', 'elem' => 'd' ],
            [ 'block' => 'c', 'elem' => 'd' ]
        ]);
    }

    function testABCmods() {

        $deps = new ten\deps([
            'block' => 'a',
            'shouldDeps' => [
                [
                    'block' => 'b',
                    'mod' => 'g',
                    'val' => 'h',
                    'mods' => [
                        'c' => 'd',
                        'e' => 'f'
                    ]
                ],
                [
                    'block' => 'c',
                    'mod' => 'd',
                    'val' => 'e'
                ]
            ],
            'mustDeps' => [
                'block' => 'd',
                'elem' => 'e',
                'mods' => [
                    'f' => 'g',
                    'h' => 'i'
                ]
            ]
        ]);

        $this->assertEquals($deps->getDecl(), [
            [ 'block' => 'd', 'elem' => 'e' ],
            [ 'block' => 'd', 'elem' => 'e', 'mod' => 'f', 'val' => 'g' ],
            [ 'block' => 'd', 'elem' => 'e', 'mod' => 'h', 'val' => 'i' ],
            [ 'block' => 'a' ],
            [ 'block' => 'b', 'mod' => 'g', 'val' => 'h' ],
            [ 'block' => 'b', 'mod' => 'c', 'val' => 'd' ],
            [ 'block' => 'b', 'mod' => 'e', 'val' => 'f' ],
            [ 'block' => 'c', 'mod' => 'd', 'val' => 'e' ]
        ]);
    }

    function testABCmodsArray() {

        $deps = new ten\deps([
            'block' => 'a',
            'shouldDeps' => [
                [
                    'block' => 'b',
                    'mod' => 'g',
                    'val' => 'h',
                    'mods' => [
                        'c' => ['d', 'e', 'f']
                    ]
                ],
                [
                    'block' => 'c',
                    'mods' => [
                        'd' => ['e', 'f']
                    ]
                ]
            ],
            'mustDeps' => [
                'block' => 'd',
                'elem' => 'e',
                'mods' => [
                    'f' => ['g', 'h', 'i']
                ]
            ]
        ]);

        $this->assertEquals($deps->getDecl(), [
            [ 'block' => 'd', 'elem' => 'e' ],
            [ 'block' => 'd', 'elem' => 'e', 'mod' => 'f', 'val' => 'g' ],
            [ 'block' => 'd', 'elem' => 'e', 'mod' => 'f', 'val' => 'h' ],
            [ 'block' => 'd', 'elem' => 'e', 'mod' => 'f', 'val' => 'i' ],
            [ 'block' => 'a' ],
            [ 'block' => 'b', 'mod' => 'g', 'val' => 'h' ],
            [ 'block' => 'b', 'mod' => 'c', 'val' => 'd' ],
            [ 'block' => 'b', 'mod' => 'c', 'val' => 'e' ],
            [ 'block' => 'b', 'mod' => 'c', 'val' => 'f' ],
            [ 'block' => 'c' ],
            [ 'block' => 'c', 'mod' => 'd', 'val' => 'e' ],
            [ 'block' => 'c', 'mod' => 'd', 'val' => 'f' ]
        ]);
    }

    function testABCelemsArrayMods() {

        $deps = new ten\deps([
            'block' => 'a',
            'shouldDeps' => [
                [
                    'block' => 'b',
                    'elems' => [
                        [ 'elem' => 'c', 'mods' => ['e' => 'f'] ],
                        [ 'elem' => 'd', 'mods' => ['g' => 'h'] ]
                    ]
                ],
                [
                    'block' => 'c',
                    'elem' => 'd'
                ]
            ],
            'mustDeps' => [
                'block' => 'd',
                'mod' => 'a',
                'val' => 'b',
                'elems' => [
                    [ 'elem' => 'e', 'mods' => ['f' => 'g', 'h' => 'i'] ],
                    [ 'elem' => 'f' ]
                ]
            ]
        ]);

        $this->assertEquals($deps->getDecl(), [
            [ 'block' => 'd', 'mod' => 'a', 'val' => 'b' ],
            [ 'block' => 'd', 'elem' => 'e' ],
            [ 'block' => 'd', 'elem' => 'e', 'mod' => 'f', 'val' => 'g' ],
            [ 'block' => 'd', 'elem' => 'e', 'mod' => 'h', 'val' => 'i' ],
            [ 'block' => 'd', 'elem' => 'f' ],
            [ 'block' => 'a' ],
            [ 'block' => 'b' ],
            [ 'block' => 'b', 'elem' => 'c' ],
            [ 'block' => 'b', 'elem' => 'c', 'mod' => 'e', 'val' => 'f' ],
            [ 'block' => 'b', 'elem' => 'd' ],
            [ 'block' => 'b', 'elem' => 'd', 'mod' => 'g', 'val' => 'h' ],
            [ 'block' => 'c', 'elem' => 'd' ]
        ]);
    }
}