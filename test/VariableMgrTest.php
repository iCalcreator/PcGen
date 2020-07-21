<?php
/**
 * PcGen is a PHP Code Generation support package
 *
 * Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved
 * Link <https://kigkonsult.se>
 * Support <https://github.com/iCalcreator/PcGen>
 *
 * This file is part of PcGen.
 *
 * PcGen is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PcGen is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PcGen.  If not, see <https://www.gnu.org/licenses/>.
 */
namespace Kigkonsult\PcGen;

use Exception;
use Kigkonsult\PcGen\Dto\VarDto;
use PHPUnit\Framework\TestCase;

class VariableMgrTest extends TestCase
{

    /**
     * Testing visibility
     *
     * @test
     */
    public function visibilityTest1() {
        $vm = VariableMgr::init( PHP_EOL, '    ' );

        $this->assertEquals(
            '    public $theVariableName11 = null;' . PHP_EOL,
            $vm->setName( 'theVariableName11' )->toString()
        );

        $this->assertEquals(
            '    protected $theVariableName12 = null;' . PHP_EOL,
            $vm->setName( 'theVariableName12' )->setVisibility( VariableMgr::PROTECTED_ )->toString()
        );

        $this->assertEquals(
            '    $theVariableName13 = null;' . PHP_EOL,
            $vm->setName( 'theVariableName13' )->setVisibility()->toString()
        );

    }

    /**
     * Testing staticly...
     *
     * @test
     */
    public function staticTest2() {
        $vm = VariableMgr::init( PHP_EOL, '    ' );

        $this->assertEquals(
            '    public static $theVariableName21 = null;' . PHP_EOL,
            $vm->setName( 'theVariableName21' )->setStatic( true )->toString()
        );

        $this->assertEquals(
            '    private static $theVariableName22 = null;' . PHP_EOL,
            $vm->setName( 'theVariableName22' )->setVisibility( VariableMgr::PRIVATE_ )->toString()
        );

        $this->assertEquals(
            '    static $theVariableName23 = null;' . PHP_EOL,
            $vm->setName( 'theVariableName23' )->setVisibility()->toString()
        );

    }

    /**
     * Testing initValue
     *
     * @test
     */
    public function initValueTest3() {
        $vm = VariableMgr::init( PHP_EOL, '    ' );

        $this->assertEquals(
            '    public $theVariableName311 = null;' . PHP_EOL,
            $vm->setName( 'theVariableName311' )
                ->toString(),
            'test null, 311'
        );
        $this->assertEquals(
            '    public $theVariableName312 = null;' . PHP_EOL,
            $vm->setName( 'theVariableName312' )
                ->setInitValue( VariableMgr::NULL_T )
                ->toString(),
            'test null, 312'

        );

        $this->assertEquals(
            '    public $theVariableName321 = [];' . PHP_EOL,
            $vm->setName( 'theVariableName321' )
                ->setInitValue( 'array()' )
                ->toString(),
            'test \'array()\', 321'
        );
        $this->assertEquals(
            '    public $theVariableName322 = [];' . PHP_EOL,
            $vm->setName( 'theVariableName322' )
                ->setInitValue( VariableMgr::ARRAY2_T )->toString(),
            'test ARRAY2_T, 322'
        );


        $arr = [ true, false, 0, 331, 3.31, 'three_dot_three_one' ];
        $exp =
            '    static $theVariableName331 = [' . PHP_EOL .
            '        true,' . PHP_EOL .
            '        false,' . PHP_EOL .
            '        0,' . PHP_EOL .
            '        331,' . PHP_EOL .
            '        3.31,' . PHP_EOL .
            '        "three_dot_three_one",' . PHP_EOL .
            '    ];' . PHP_EOL;
        $output = $vm->setName( 'theVariableName331' )
            ->setStatic( true )
            ->setVisibility()
            ->setInitValue( $arr )
            ->toString();
        $this->assertEquals( $exp, $output, 'test array, 331' );

        $arr = [ 'key1' => 'value1', 'key2' => 'value2', ];
        $exp =
            '    static $theVariableName332 = [' . PHP_EOL .
            '        "key1" => "value1",' . PHP_EOL .
            '        "key2" => "value2",' . PHP_EOL .
            '    ];' . PHP_EOL;
        $output = $vm->setName( 'theVariableName332' )
            ->setStatic( true )
            ->setVisibility()
            ->setInitValue( $arr )
            ->toString();
        $this->assertEquals( $exp, $output, 'test array, 332' );

        if( DISPLAYvm ) {
            echo __FUNCTION__ . ' : ' . PHP_EOL . $output . PHP_EOL;
        }


        $this->assertEquals(
            '    private $theVariableName34 = 34;' . PHP_EOL,
            $vm->setName( 'theVariableName34' )
                ->setVisibility( VariableMgr::PRIVATE_ )
                ->setStatic( false )
                ->setInitValue( 34 )
                ->toString(),
            'test int, 34'
        );

        $this->assertEquals(
            '    private $theVariableName35 = false;' . PHP_EOL,
            $vm->setName( 'theVariableName35' )
                ->setInitValue( false )
                ->toString(),
            'test false, 35'
        );

        $this->assertEquals(
            '    $theVariableName36 = 3.6;' . PHP_EOL,
            $vm->setName( 'theVariableName36' )
                ->setVisibility()
                ->setInitValue( 3.6 )
                ->toString(),
            'test float, 36'
        );

        $output = $vm->setName( 'theVariableName37' )
            ->setInitValue( 'test37' )
            ->toString();
        $this->assertEquals(
            '    $theVariableName37 = "test37";' . PHP_EOL,
            $output,
            'test string, 37'
        );

        if( DISPLAYvm ) {
            echo __FUNCTION__ . ' : ' . PHP_EOL . $output . PHP_EOL;
        }

    }

    /**
     * Testing closure (i.e. body) without indent and no visibility
     *
     *   simple function     (set using 'setBody') NOT tested here
     *   anonymous function  (set using 'setBody')
     *
     * @test
     */
    public function closureTest41() {
        $body = ' /* this is the closure body */';
        $closure = FcnFrameMgr::init()
            ->setBaseIndent()
            ->setVisibility()
            ->setArguments( [ 'arg' ] )
            ->setBody( $body )
            ->toString();

        $vm   = VariableMgr::init()->setVisibility()->setBaseIndent();

        $output = $vm->setName( 'theVariableName41' )->setBody( $closure )->toString();
        $exp =
            '$theVariableName41 = function( $arg )' . PHP_EOL .
            '{' . PHP_EOL .
            '     /* this is the closure body */' . PHP_EOL .
            '}' .PHP_EOL;
        $this->assertEquals( $exp, $output, 'error in ' . PHP_EOL . $exp );

        if( DISPLAYvm ) {
            echo __FUNCTION__ . ' : ' . PHP_EOL . $output . PHP_EOL;
        }
    }

    /**
     * Testing closure (i.e. body) with indent and visibility
     *
     *   simple function     (set using 'setBody') NOT tested here
     *   anonymous function  (set using 'setBody')
     *
     * @test
     */
    public function closureTest42() {
        $closure = FcnFrameMgr::init()
            ->setBaseIndent()
            ->setVisibility()
            ->setArguments( [ 'arg' ] )
            ->setBody( [ '', ' /* this is the closure body */', '' ] ) // note : empty rows here
            ->toArray();
        $output = VariableMgr::init()
            ->setStatic( true )
            ->setName( 'theVariableName42' )
            ->setBody( $closure )
            ->toString();
        $exp =
            '    public static $theVariableName42 = function( $arg )' . PHP_EOL .
            '    {' . PHP_EOL .
            '         /* this is the closure body */' . PHP_EOL .
            '    }' .PHP_EOL;
        $this->assertEquals( $exp, $output, 'error in ' . __FUNCTION__ . PHP_EOL . $exp );
        if( DISPLAYvm ) {
            echo __FUNCTION__ . ' : ' . PHP_EOL . $output . PHP_EOL;
        }
    }

    /**
     * Testing const
     *
     * @test
     */
    public function constTest5() {
        $output = VariableMgr::init( PHP_EOL, '    ' )->setIsConst( true )
            ->setName( 'constant5')
            ->setInitValue( 'constant5' )
            ->toString();
        $exp    = '    const CONSTANT5 = "constant5";' . PHP_EOL;
        $this->assertEquals( $exp, $output, 'error in ' . PHP_EOL . $exp );
        if( DISPLAYvm ) {
            echo __FUNCTION__ . ' : ' . PHP_EOL . $output . PHP_EOL;
        }
    }

    /**
     * Testing initValue
     *
     *   instantiated sourceObject+method, passed as an array             : [$sourceObject, methodName]
     *   class variable and static (factory?) method, passed as an array: [FQCN, methodName]
     *   instantiated sourceObject, class has an (magic) __call method    : $sourceObject
     *   class variable, class has an (magic) __callStatic method       : FQCN
     *   instantiated sourceObject, class has an (magic) __invoke method  : $sourceObject
     *
     * @test
     */
    public function callBackTest6() {
        $vm = VariableMgr::init( PHP_EOL, '    ' )->setStatic( true );

        $vm->setName( 'theVariableName61' )->setCallback( '$objectInstance', 'methodName' );
        $callBack = $vm->getCallback();
        $this->assertEquals(
            [ '$objectInstance', 'methodName' ],
            $callBack,
            'instantiated sourceObject+method, passed as an array, 1a'
        );
        $output = $vm->toString();
        $exp =
            '    public static $theVariableName61 = [' . PHP_EOL .
            '        $objectInstance,' . PHP_EOL .
            '        \'methodName\'' . PHP_EOL .
            '    ];' . PHP_EOL;
        $this->assertEquals(
            $exp,
            $output,
            'instantiated sourceObject+method, passed as an array, 1b'
        );
        if( DISPLAYvm ) {
            echo __FUNCTION__ . ' : ' . PHP_EOL . $output . PHP_EOL;
        }

        $vm->setName( 'theVariableName62' )
            ->setCallback( 'NameSpaced\FQCN\Klass', 'staticMethodName' );
        $callBack = $vm->getCallback();
        $this->assertEquals(
            [ 'NameSpaced\FQCN\Klass', 'staticMethodName' ],
            $callBack,
            'instantiated sourceObject+method, passed as an array, 2a'
        );
        $output = $vm->toString();
        $exp =
            '    public static $theVariableName62 = [' . PHP_EOL .
            '        \'NameSpaced\FQCN\Klass\',' . PHP_EOL .
            '        \'staticMethodName\'' . PHP_EOL .
            '    ];' . PHP_EOL;
        $this->assertEquals(
            $exp,
            $output,
            'class variable and static (factory?) method, passed as an array, 2b'
        );
        if( DISPLAYvm ) {
            echo __FUNCTION__ . ' : ' . PHP_EOL . $output . PHP_EOL;
        }

        $vm->setName( 'theVariableName635' )->setCallback( '$objectInstanceWith__CallOrInvokeMethod' );
        $callBack = $vm->getCallback();
        $this->assertEquals(
            '$objectInstanceWith__CallOrInvokeMethod',
            $callBack,
            'instantiated sourceObject+method, passed as an array, 3 + 5 a'
        );
        $output = $vm->toString();
        $exp = '    public static $theVariableName635 = $objectInstanceWith__CallOrInvokeMethod;' . PHP_EOL;
        $this->assertEquals(
            $exp,
            $output,
            'instantiated sourceObject, class has an (magic) __call method, 3 + 5 b'
        );
        if( DISPLAYvm ) {
            echo __FUNCTION__ . ' : ' . PHP_EOL . $output . PHP_EOL;
        }

        $vm->setName( 'theVariableName64' )
            ->setCallback( 'NameSpaced\FQCN\ClassWithCallStaticMethod' );
        $callBack = $vm->getCallback();
        $this->assertEquals(
            'NameSpaced\FQCN\ClassWithCallStaticMethod',
            $callBack,
            'class variable, class has an (magic) __callStatic method, 4a'
        );
        $output = $vm->toString();
        $exp = '    public static $theVariableName64 = \'NameSpaced\FQCN\ClassWithCallStaticMethod\';' . PHP_EOL;
        $this->assertEquals(
            $exp,
            $output,
            'class variable, class has an (magic) __callStatic method, 4b'
        );

        if( DISPLAYvm ) {
            echo __FUNCTION__ . ' : ' . PHP_EOL . $output . PHP_EOL;
        }

    }

    /**
     * @test
     */
    public function VariableMgrTest58() {
        $varDto = VarDto::factory( 'test58' );
        $this->assertTrue(
            VariableMgr::init()->setVarDto( $varDto )->getVarDto() instanceof VarDto
        );
    }

    /**
     * @test
     */
    public function VariableMgrTest61() {
        try {
            $vm = VariableMgr::factory();
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * @test
     */
    public function VariableMgrTest71() {
        try {
            $vm = VariableMgr::init()->toString();
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * @test
     */
    public function VariableMgrTest72() {
        try {
            $vm = VariableMgr::init()->setName( 72 )->toString();
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }

        try {
            $vm = VariableMgr::init()->setName( 'array' )->toString();
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * @test
     */
    public function VariableMgrTest73() {
        try {
            $vm = VariableMgr::factory( 73 );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * @test
     */
    public function VariableMgrTest99() {
        $this->assertTrue(
            is_string(
                VariableMgr::init()
                    ->setName( 'theVariableName99' )
                    ->setBody( ' // code...' )
                    ->__toString()
            )
        );
        $this->assertTrue(
            is_string(
                VariableMgr::init()
                    ->setName( 'theVariableName99' )
                    ->setCallback( '$objectInstanceWith__CallOrInvokeMethod' )
                    ->__toString()
            )
        );
        $this->assertTrue(
            is_string(
                VariableMgr::init()
                    ->setName( 'theVariableName99' )
                    ->setInitValue( true )
                    ->__toString()
            )
        );
    }

    /**
     * @test
     */
    public function propertyMgrTest101() {
        $propertyMgr = new PropertyMgr();

        $this->assertTrue( $propertyMgr->isMakeGetter());
        $this->assertTrue( $propertyMgr->isMakeSetter());

        $propertyMgr->setMakeGetter( false );
        $propertyMgr->setMakeSetter( false );

        $this->assertFalse( $propertyMgr->isMakeGetter());
        $this->assertFalse( $propertyMgr->isMakeSetter());

        $propertyMgr->setMakeGetter( true );
        $propertyMgr->setMakeSetter( true );
        $propertyMgr->setIsConst( true );
        $this->assertFalse( $propertyMgr->isMakeGetter());
        $this->assertFalse( $propertyMgr->isMakeSetter());

        $propertyMgr->setMakeGetter( true );
        $propertyMgr->setMakeSetter( true );
        $propertyMgr->setStatic( true );
        $this->assertFalse( $propertyMgr->isMakeGetter());
        $this->assertFalse( $propertyMgr->isMakeSetter());

    }
    /**
     * @test
     */
    public function util301() {
        try {
            Util::renderScalarValue( [ 1, 2, 3 ] );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }


}
