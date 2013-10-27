<?php

class depsTest extends PHPUnit_Framework_TestCase {

    function testSimple() {

        $deps = new ten\deps([
            'block' => 'a',
            'shouldDeps' => [ 'block' => 'b' ]
        ]);

        $this->assertEquals($deps->getDecl(), [
            [ 'block' => 'a' ],
            [ 'block' => 'b' ]
        ]);
    }
}