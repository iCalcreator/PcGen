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
use PHPUnit\Framework\TestCase;

if( ! in_array( __DIR__ . '/FimDataProviderTrait.php', get_included_files())) {
    include( __DIR__ . '/FimDataProviderTrait.php' );
}

class FcnInvokeMgrTest extends TestCase
{

    /**
     * @test
     */
    public function FcnInvokeMgrTest1() {

        $this->assertEquals(
            'fcnName()' . PHP_EOL,
            FcnInvokeMgr::factory( null, 'fcnName' )
                ->toString()
        );

        $this->assertEquals(
            'fcnName()' . PHP_EOL,
            FcnInvokeMgr::factory( null, '$fcnName' )
                ->toString()
        );

        $this->assertEquals(
            'self::fcnName()' . PHP_EOL,
            FcnInvokeMgr::factory( FcnInvokeMgr::SELF_KW, 'fcnName' )
                ->toString()
        );

        $this->assertEquals(
            'self::fcnName()' . PHP_EOL,
            FcnInvokeMgr::factory( FcnInvokeMgr::SELF_KW, '$fcnName' )
                ->toString()
        );

        $this->assertEquals(
            '$this->fcnName()' . PHP_EOL,
            FcnInvokeMgr::factory( FcnInvokeMgr::THIS_KW, 'fcnName' )
                ->toString()
        );

        $this->assertEquals(
            '$this->fcnName()' . PHP_EOL,
            FcnInvokeMgr::factory( FcnInvokeMgr::THIS_KW, '$fcnName' )
                ->toString()
        );

        $this->assertEquals(
            '$class::fcnName()' . PHP_EOL,
            FcnInvokeMgr::factory( '$class', 'fcnName' )
                ->setIsStatic( true )
                ->toString()
        );

        $this->assertEquals(
            '$class::fcnName()' . PHP_EOL,
            FcnInvokeMgr::factory( '$class', '$fcnName' )
                ->setIsStatic( true )
                ->toString()
        );

        $this->assertEquals(
            '$class->fcnName()' . PHP_EOL,
            FcnInvokeMgr::factory( '$class', 'fcnName' )
                ->toString()
        );

        $this->assertEquals(
            '$class->fcnName()' . PHP_EOL,
            FcnInvokeMgr::factory( '$class', '$fcnName' )
                ->toString()
        );

        $this->assertEquals(
            'fqcn::fcnName()' . PHP_EOL,
            FcnInvokeMgr::factory( 'fqcn', 'fcnName' )
                ->toString()
        );

        $this->assertEquals(
            'fqcn::fcnName()' . PHP_EOL,
            FcnInvokeMgr::factory( 'fqcn', '$fcnName' )
                ->toString()
        );

    }

    use FimDataProviderTrait; // FcnInvokeMgrTest3ArgumentProvider + FcnInvokeMgrFunctionProvider

    /**
     * @return array
     */
    public function FcnInvokeMgrTest3DataProvider() {
        $testData = [];

        foreach( self::FcnInvokeMgrFunctionProvider() as $function ) {
            foreach( self::FcnInvokeMgrTest3ArgumentProvider() as $argSet ) {

                $testData[] = [
                    $argSet[0] . '-' . $function[0],
                    $function[1],
                    $function[2],
                    $argSet[1],
                    $function[3]
                ];

            }
        }

        return $testData;
    }

    /**
     * Testing FcnInvokeMgr
     *
     * @test
     * @dataProvider FcnInvokeMgrTest3DataProvider
     *
     * @param string       $case
     * @param string       $class
     * @param string       $name
     * @param string|array $argSet
     * @param string       $expFcnName
     */
    public function FcnInvokeMgrTest3( $case, $class, $name, $argSet, $expFcnName ) {
        if( empty( $argSet )) {
            $argSet = null;
        }
        elseif( ! is_array( $argSet )) {
            $argSet = [ $argSet ];
        }
        switch( array_rand( [ 1, 2, 3 ] )) {
            case 1 :
                $fim = FcnInvokeMgr::factory( $class, $name, $argSet );
                break;
            case 2 :
                $fim = FcnInvokeMgr::init()->setName( $class, $name )
                    ->setArguments( $argSet );
                break;
            default :
                $fim = FcnInvokeMgr::init()->setName( EntityMgr::factory( $class, $name ))
                    ->setArguments( $argSet );
                break;
        } // end switch
        $code   = $fim->toString();

        $this->assertTrue(
            ( false !== strpos( $code, $expFcnName )),
            $case . ' actual : ' . trim( $code ). ' expected : ' . $expFcnName
        );

        $name = Util::unSetVarPrefix( $name );
        if( empty( $argSet )) {
            $case .= '-A ';
            $expected = EntityMgr::factory( $class, $name )->setForceVarPrefix( false )->toString() . '()';
            $this->assertEquals(
                $expected,
                trim( $code ),
                $case . ' actual : ' . trim( $code ). ' expected : ' . $expected
            );
            if( DISPLAYfim ) {
                echo __FUNCTION__ . ' ' . $case . ' ' . $code;
            }
            return;
        }
        $invokeFcn = EntityMgr::factory( $class, $name )->setForceVarPrefix( false )->toString();
        $expected = $invokeFcn . '(';
        $this->assertTrue(
            ( false !== strpos( $code, $expected )),
            $case . '-B actual : ' . trim( $code ). ' expected : ' . $expected
        );
        if( DISPLAYfim ) {
            echo __FUNCTION__ . ' ' . $case . '-B ' . $code;
        }

        if( is_string( $argSet )) {
            $argSet = Util::unSetVarPrefix( $argSet );
            $expected = $invokeFcn . '( $' . $argSet . ' )';
            $this->assertEquals(
                $expected,
                trim( $code ),
                $case . '-C actual : ' . trim( $code ). ' expected : ' . $expected
            );
            if( DISPLAYfim ) {
                echo __FUNCTION__ . ' ' . $case . '-C ' . $code;
            }
        }
        elseif( is_string( $argSet[0] )) {
            $argSet[0] = Util::unSetVarPrefix( $argSet[0] );
            $expected = $invokeFcn . '( $' . $argSet[0];
            $this->assertTrue(
                ( false !== strpos( $code, $expected )),
                $case . '-D actual : ' . trim( $code ). ' expected : ' . $expected
            );
            if( DISPLAYfim ) {
                echo __FUNCTION__ . ' ' . $case . '-D ' . $code;
            }
        }
        else {
            foreach( $argSet as $aIx => $arg ) {
                $arg[0] = Util::unSetVarPrefix( $arg[0] );
                $expected = ' $' . $arg[0];
                $this->assertTrue(
                    ( false !== strpos( $code, $expected )),
                    $case . '-E' . $aIx . ' actual : ' . trim( $code ). ' expected : ' . $expected
                );
            }
            if( DISPLAYfim ) {
                echo __FUNCTION__ . ' ' . $case . '-E ' . $code;
            }
        }
    }

    /**
     * @test
     */
    public function FcnInvokeMgrTest35() {
        $this->assertEquals(
            '$class->method()' . PHP_EOL,
            FcnInvokeMgr::factory( null, 'method' )
                ->setClass( '$class' )
                ->toString()
        );
    }

    /**
     * @test
     */
    public function FcnInvokeMgrTest36() {
        $this->assertEquals(
            '$class->method()' . PHP_EOL,
            FcnInvokeMgr::factory( '$class', 'method' )
                ->toString()
        );
        $this->assertEquals(
            '$class::method()' . PHP_EOL,
            FcnInvokeMgr::factory( '$class', 'method' )
                ->setIsStatic( true )
                ->toString()
        );
    }

    /**
     * @test
     */
    public function FcnInvokeMgrTest43() {
        try {
            FcnInvokeMgr::init()->toArray();
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }

        try {
            FcnInvokeMgr::init()->setName( null, null );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }

        try {
            FcnInvokeMgr::init()->setIsStatic( true );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * Testing AssignClauseMgr and ChainInvokeMgr (consecutive invokes, FcnInvokeMgr) as source
     *
     * @test
     */
    public function ChainInvokeMgrTest56() {
        $invokes = [
            FcnInvokeMgr::factory( 'SourceClass', FcnInvokeMgr::FACTORY, [ 'arg11', 'arg12' ] ),
            FcnInvokeMgr::factory( 'SourceClass', 'method2', [ 'arg21', 'arg22' ] ),
            FcnInvokeMgr::factory( 'SourceClass', 'method3', [ 'arg31', 'arg32' ] ),
            FcnInvokeMgr::factory( 'SourceClass', 'method4', [ 'arg41', 'arg42' ] ),
            FcnInvokeMgr::factory( 'SourceClass', __FUNCTION__ )
        ];

        $rcm = ReturnClauseMgr::init()
            ->setBaseIndent()
            ->setFcnInvoke( $invokes );
        $code = ltrim( $rcm->toString());
        $this->assertEquals(
            'return SourceClass::factory( $arg11, $arg12 )' . PHP_EOL .
            '    ->method2( $arg21, $arg22 )' . PHP_EOL .
            '    ->method3( $arg31, $arg32 )' . PHP_EOL .
            '    ->method4( $arg41, $arg42 )' . PHP_EOL .
            '    ->' . __FUNCTION__ . '();' . PHP_EOL,
            $code
        );
        if( DISPLAYfim ) {
            echo $code . PHP_EOL;
        }

        $acm = AssignClauseMgr::init()
            ->setBaseIndent()
            ->setTarget( null, 'target' );
        foreach( $invokes as $invoke ) {
            $acm->appendInvoke( $invoke );
        }
        $code = ltrim( $acm->toString());
        $this->assertEquals(
            '$target = SourceClass::factory( $arg11, $arg12 )' . PHP_EOL .
            '    ->method2( $arg21, $arg22 )' . PHP_EOL .
            '    ->method3( $arg31, $arg32 )' . PHP_EOL .
            '    ->method4( $arg41, $arg42 )' . PHP_EOL .
            '    ->' . __FUNCTION__ . '();' . PHP_EOL,
            $code
        );
        if( DISPLAYfim ) {
            echo $code . PHP_EOL;
        }
    }

    /**
     * @test
     */
    public function ChainInvokeMgrTest57() {
        try {
            $rcm = ReturnClauseMgr::init()
                ->setFcnInvoke(
                    [
                        FcnInvokeMgr::factory( null, FcnInvokeMgr::FACTORY),
                        FcnInvokeMgr::factory( null, 'method2'),
                    ]
                );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }

        try {
            $rcm = ReturnClauseMgr::init()
                ->setFcnInvoke(
                    [
                        FcnInvokeMgr::factory( 'SourceClass', FcnInvokeMgr::FACTORY),
                        FcnInvokeMgr::factory( null, 'method2'),
                    ]
                );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }

        try {
            $rcm = ReturnClauseMgr::init()
                ->setFcnInvoke(
                    [
                        FcnInvokeMgr::factory( null, FcnInvokeMgr::FACTORY),
                        FcnInvokeMgr::factory( 'SourceClass', 'method2'),
                    ]
                );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }

        try {
            $rcm = ReturnClauseMgr::init()
                ->setFcnInvoke(
                    [
                        FcnInvokeMgr::factory( 'SourceClass', FcnInvokeMgr::FACTORY),
                        FcnInvokeMgr::factory( 'ns\123\src\Klass', 'method2'),
                    ]
                );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

}
