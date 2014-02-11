<?php

class coreTest extends PHPUnit_Framework_TestCase {

    public function testResolveRealPath() {

        $this->assertEquals(
            ten\core::resolveRealPath(__DIR__, 'testResolveRealPath', 'one'),
            __DIR__ . '/testResolveRealPath/one'
        );

        $this->assertEquals(
            ten\core::resolveRealPath(__DIR__, 'testResolveRealPath', 'one/cat/', '..', 'cat', 'cat.txt'),
            __DIR__ . '/testResolveRealPath/one/cat/cat.txt'
        );

        $this->assertEquals(
            ten\core::resolveRealPath(__DIR__, 'testResolveRealPath', 'one/../two', 'bird'),
            __DIR__ . '/testResolveRealPath/two/bird'
        );

        $this->assertEquals(
            ten\core::resolveRealPath('tenframe', '/test', 'core/'),
            __DIR__
        );

        $this->assertEquals(
            ten\core::resolveRealPath('/tenframe/test/core', 'testResolveRealPath/two/..'),
            __DIR__ . '/testResolveRealPath'
        );

        $this->assertFalse(
            ten\core::resolveRealPath(__DIR__, 'testResolveRealPath', 'three')
        );
    }

    public function testResolvePath() {

        $this->assertEquals(
            ten\core::resolvePath(__DIR__, 'virtualPath', 'one'),
            __DIR__ . '/virtualPath/one'
        );

        $this->assertEquals(
            ten\core::resolvePath(__DIR__, 'virtualPath', 'one/cat/', '..', 'cat', 'cat.txt'),
            __DIR__ . '/virtualPath/one/cat/cat.txt'
        );

        $this->assertEquals(
            ten\core::resolvePath(__DIR__, 'virtualPath', 'one/../two', 'bird'),
            __DIR__ . '/virtualPath/two/bird'
        );

        $this->assertEquals(
            ten\core::resolvePath('tenframe', '/test', 'core/virtualPath'),
            __DIR__ . '/virtualPath'
        );

        $this->assertEquals(
            ten\core::resolvePath('/tenframe/test/core', 'virtualPath/two/..'),
            __DIR__ . '/virtualPath'
        );
    }

    public function testIsAssoc() {

        $this->assertFalse(ten\core::isAssoc([1, 2, 3]));
        $this->assertFalse(ten\core::isAssoc([1, [ 'a' => 2 ], 3]));
        $this->assertTrue(ten\core::isAssoc([1, 2, 'a' => 3]));
        $this->assertTrue(ten\core::isAssoc(['a' => 1, 'b' => 2, 'c' => 3]));
    }

    public function testAddNotExistField() {

        $this->assertEquals(ten\core::addNotExistField([0, 1, 2], 'a', 3), [
            0, 1, 2, 'a' => 3
        ]);

        $this->assertEquals(ten\core::addNotExistField([ 'a' => 1 ], 'a', 2), [
            'a' => 1
        ]);

        $this->assertEquals(ten\core::addNotExistField([ 'a' => 1 ], 'b', 2), [
            'a' => 1,
            'b' => 2
        ]);
    }

    public function testCopyField() {

        $this->assertEquals(ten\core::copyField('a', [1, 2, 3], []), []);

        $this->assertEquals(ten\core::copyField('a', [ 'a' => 1 ], [ 'a' => 2 ]), [
            'a' => 2
        ]);

        $this->assertEquals(ten\core::copyField('a', [ 'a' => 1 ], [ 'b' => 2 ]), [
            'a' => 1,
            'b' => 2
        ]);
    }

    public function testCopyFieldForce() {

        $this->assertEquals(ten\core::copyFieldForce('a', [1, 2, 3], []), []);

        $this->assertEquals(ten\core::copyFieldForce('a', [ 'a' => 1 ], [ 'a' => 2 ]), [
            'a' => 1
        ]);

        $this->assertEquals(ten\core::copyFieldForce('a', [ 'a' => 1 ], [ 'b' => 2 ]), [
            'a' => 1,
            'b' => 2
        ]);
    }
}