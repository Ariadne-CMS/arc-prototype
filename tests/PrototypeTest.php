<?php

    /*
     * This file is part of the Ariadne Component Library.
     *
     * (c) Muze <info@muze.nl>
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code.
     */

    class PrototypeTest extends PHPUnit_Framework_TestCase
    {
        function testPrototype()
        {
            $view = \arc\prototype::create( [
                'foo' => 'bar',
                'bar' => function () {
                    return $this->foo;
                }
            ] );
            $this->assertEquals( $view->foo, 'bar' );
            $this->assertEquals( $view->bar(), 'bar' );
        }

        function testPrototypeInheritance()
        {
            $foo = \arc\prototype::create( [
                'foo' => 'bar',
                'bar' => function () {
                    return $this->foo;
                }
            ]);
            $bar = \arc\prototype::extend( $foo, [
                'foo' => 'rab'
            ]);
            $this->assertEquals( $foo->foo, 'bar' );
            $this->assertEquals( $bar->foo, 'rab' );
            $this->assertEquals( $foo->bar(), 'bar' );
            $this->assertEquals( $bar->bar(), 'rab' );
            $this->assertTrue( \arc\prototype::hasOwnProperty($bar, 'foo') );
            $this->assertFalse( \arc\prototype::hasOwnProperty($bar, 'bar') );
        }

        function testPrototypeInheritance2()
        {
            $foo = \arc\prototype::create([
                'bar' => function () {
                    return 'bar';
                }
            ]);
            $bar = \arc\prototype::extend($foo, [
                'bar' => function () use ($foo) {
                    return 'foo'.$foo->bar();
                }
            ]);
            $this->assertEquals( $bar->bar(), 'foobar' );
        }

        function testPrototypeInheritance3()
        {
            $foo = \arc\prototype::create([
                'bar' => function () {
                    return 'bar';
                },
                'foo' => function () {
                    return '<b>'.$this->bar().'</b>';
                }
            ]);
            $bar = \arc\prototype::extend($foo, [
                'bar' => function () use ($foo) {
                    return 'foo'.$foo->bar();
                }
            ]);
            $this->assertEquals( $bar->foo(), '<b>foobar</b>' );
        }

        function testStatic() 
        {
            $foo = \arc\prototype::create([
                'bar' => 'Bar',
                ':foo' => static function($self) {
                    return $self->bar;
                }
            ]);
            $this->assertEquals( $foo->foo(), 'Bar');
        }

        function testToString()
        {
            $foo = \arc\prototype::create([
                '__toString' => function() {
                    return 'Foo';
                }
            ]);
            $this->assertEquals('Foo', (string) $foo);
        }
        
        function testStaticToString()
        {            
            $bar = \arc\prototype::create([
                'bar' => 'Bar',
                ':__toString' => static function($self) {
                    return $self->bar;
                }
            ]);
            $this->assertEquals('Bar', $bar.'');
        }

        function testObserve()
        {
            $foo = \arc\prototype::create([]);
            $f = function($ob, $name, $value) {
                return false;
            };
            \arc\prototype::observe($foo, $f);
            $foo->bar = 'bar';
            $this->assertArrayNotHasKey('bar', \arc\prototype::entries($foo));
            \arc\prototype::unobserve($foo, $f);
            $foo->bar = 'bar';
            $this->assertEquals($foo->bar, 'bar');
            \arc\prototype::observe($foo, $f);
            $foo->bar = 'baz';
            $this->assertEquals($foo->bar, 'bar');
        }

        function testFreeze()
        {
            $foo = \arc\prototype::create([]);
            \arc\prototype::freeze($foo);
            $foo->bar = 'bar';
            $this->assertArrayNotHasKey('bar', \arc\prototype::entries($foo));
            \arc\prototype::unfreeze($foo);
            $foo->bar = 'bar';
            $this->assertEquals($foo->bar, 'bar');
        }

        function testNotExtendable()
        {
            $foo = \arc\prototype::create([
                'bar' => 'Bar'
            ]);
            \arc\prototype::preventExtensions($foo);
            $bar = \arc\prototype::extend($foo, [
                'foo' => 'Foo'
            ]);
            $this->assertNull($bar);
        }

        function testAssign()
        {
            $foo = \arc\prototype::create([
                'bar' => 'Bar'
            ]);
            $bar = \arc\prototype::extend($foo, [
                'foo' => 'Foo'
            ]);
            $zod = \arc\prototype::create([
                'zod' => 'Zod'
            ]);
            $zed = \arc\prototype::create([
                'zed' => 'Zed'
            ]);
            $zoom = \arc\prototype::assign($zod, $bar, $zed);
            $this->assertEquals($zoom->bar, $foo->bar);
            $this->assertEquals($zoom->zod, $zod->zod);
        }


    }
