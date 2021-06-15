<?php
/**
 * PcGen is a PHP Code Generation support package
 *
 * This file is part of PcGen.
 *
 * @author    Kjell-Inge Gustafsson, kigkonsult <ical@kigkonsult.se>
 * @copyright 2020-2021 Kjell-Inge Gustafsson, kigkonsult, All rights reserved
 * @link      https://kigkonsult.se
 * @license   Subject matter of licence is the software PcGen.
 *            PcGen is free software: you can redistribute it and/or modify
 *            it under the terms of the GNU General Public License as published by
 *            the Free Software Foundation, either version 3 of the License, or
 *            (at your option) any later version.
 *
 *            PcGen is distributed in the hope that it will be useful,
 *            but WITHOUT ANY WARRANTY; without even the implied warranty of
 *            MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *            GNU General Public License for more details.
 *
 *            You should have received a copy of the GNU General Public License
 *            along with PcGen.  If not, see <https://www.gnu.org/licenses/>.
 */
declare( strict_types = 1 );
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
        $fcnName = 'fcnName101';
        $this->assertEquals(
            $fcnName . '()' . PHP_EOL,
            FcnInvokeMgr::factory( null, $fcnName )
                ->toString()
        );

        $fcnName = 'fcnName102';
        $this->assertEquals(
            $fcnName . '()' . PHP_EOL,
            FcnInvokeMgr::factory( null, $fcnName )
                ->toString()
        );

        $fcnName = 'fcnName103';
        $this->assertEquals(
            'self::' . $fcnName . '()' . PHP_EOL,
            FcnInvokeMgr::factory( FcnInvokeMgr::SELF_KW, $fcnName )
                ->toString()
        );

        $fcnName = 'fcnName104';
        $this->assertEquals(
            'self::' . $fcnName . '()' . PHP_EOL,
            FcnInvokeMgr::factory( FcnInvokeMgr::SELF_KW, $fcnName )
                ->toString()
        );

        $fcnName = 'fcnName105';
        $this->assertEquals(
            '$this->' . $fcnName . '()' . PHP_EOL,
            FcnInvokeMgr::factory( FcnInvokeMgr::THIS_KW, $fcnName )
                ->toString()
        );

        $fcnName = 'fcnName106';
        $this->assertEquals(
            '$this->' . $fcnName . '()' . PHP_EOL,
            FcnInvokeMgr::factory( FcnInvokeMgr::THIS_KW, $fcnName )
                ->toString()
        );

        $fcnName = 'fcnName107';
        $this->assertEquals(
            '$class::' . $fcnName . '()' . PHP_EOL,
            FcnInvokeMgr::factory( '$class', $fcnName )
                ->setIsStatic( true )
                ->toString()
        );

        $fcnName = 'fcnName108';
        $this->assertEquals(
            '$class::' . $fcnName . '()' . PHP_EOL,
            FcnInvokeMgr::factory( '$class', $fcnName )
                ->setIsStatic( true )
                ->toString()
        );

        $fcnName = 'fcnName109';
        $this->assertEquals(
            '$class->' . $fcnName . '()' . PHP_EOL,
            FcnInvokeMgr::factory( '$class', $fcnName )
                ->toString()
        );

        $fcnName = 'fcnName110';
        $this->assertEquals(
            '$class->' . $fcnName . '()' . PHP_EOL,
            FcnInvokeMgr::factory( '$class', '$' . $fcnName )
                ->toString()
        );

        $fcnName = 'fcnName111';
        $this->assertEquals(
            'fqcn::' . $fcnName . '()' . PHP_EOL,
            FcnInvokeMgr::factory( 'fqcn', $fcnName )
                ->toString()
        );

        $fcnName = 'fcnName112';
        $this->assertEquals(
            'fqcn::' . $fcnName .'()' . PHP_EOL,
            FcnInvokeMgr::factory( 'fqcn', '$' . $fcnName )
                ->toString()
        );

    }

    use FimDataProviderTrait; // FcnInvokeMgrTest3ArgumentProvider + FcnInvokeMgrFunctionProvider

    /**
     * @return array
     */
    public function FcnInvokeMgrTest3DataProvider() : array
    {
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
    public function FcnInvokeMgrTest3( string $case, $class, string $name, $argSet, string $expFcnName ) {
        $case = '3-' . $case;
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
                echo __FUNCTION__ . ' ' . $case . ' ' . trim( $code );
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
        $fcnName = 'method35';
        $this->assertEquals(
            '$class->' . $fcnName . '()' . PHP_EOL,
            FcnInvokeMgr::factory( null, $fcnName )
                ->setClass( '$class' )
                ->toString()
        );
    }

    /**
     * @test
     */
    public function FcnInvokeMgrTest36() {
        $fcnName = 'method361';
        $this->assertEquals(
            '$class->' . $fcnName . '()' . PHP_EOL,
            FcnInvokeMgr::factory( '$class', $fcnName )
                ->toString()
        );

        $fcnName = 'method361';
        $this->assertEquals(
            '$class::' . $fcnName . '()' . PHP_EOL,
            FcnInvokeMgr::factory( '$class', $fcnName )
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
            $this->assertTrue( false, 'case 43-101' );
        }
        catch( Exception $e ) {
            $this->assertTrue( true, 'case 43-102' );
        }

        try {
            FcnInvokeMgr::init()->setName( null, null );
            $this->assertTrue( false, 'case 43-111' );
        }
        catch( Exception $e ) {
            $this->assertTrue( true, 'case 43-112' );
        }

        try {
            FcnInvokeMgr::init()->setIsStatic( true );
            $this->assertTrue( false, 'case 43-131' );
        }
        catch( Exception $e ) {
            $this->assertTrue( true, 'case 43-132' );
        }
    }

    /**
     * Testing AssignClauseMgr and ChainInvokeMgr (consecutive invokes, FcnInvokeMgr) as source
     *
     * @test
     */
    public function ChainInvokeMgrTest56() {
        $invokes = [
            FcnInvokeMgr::factory( 'SourceClass', FcnInvokeMgr::FACTORY, [ 'arg561', 'arg562' ] ),
            FcnInvokeMgr::factory( 'SourceClass', 'method2', [ 'arg5621', 'arg5622' ] ),
            FcnInvokeMgr::factory( 'SourceClass', 'method3', [ 'arg5631', 'arg5632' ] ),
            FcnInvokeMgr::factory( 'SourceClass', 'method4', [ 'arg5641', 'arg5642' ] ),
            FcnInvokeMgr::factory( 'SourceClass', __FUNCTION__ )
        ];
        $acceptedTail = 'SourceClass::factory( $arg561, $arg562 )' . PHP_EOL .
            '    ->method2( $arg5621, $arg5622 )' . PHP_EOL .
            '    ->method3( $arg5631, $arg5632 )' . PHP_EOL .
            '    ->method4( $arg5641, $arg5642 )' . PHP_EOL .
            '    ->' . __FUNCTION__ . '();' . PHP_EOL;

        $rcm = ReturnClauseMgr::init()
            ->setBaseIndent()
            ->setFcnInvoke( $invokes );
        $code = ltrim( $rcm->toString());
        $this->assertEquals(
            'return ' . $acceptedTail,
            $code,
            'case 56-1'
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
            '$target = ' . $acceptedTail,
            $code,
            'case 56-2'
        );
        if( DISPLAYfim ) {
            echo $code . PHP_EOL;
        }
    }

    /**
     * Test Exception (Throwable?) or not
     *
     * @test
     */
    public function ChainInvokeMgrTest57() {
        // Not accepted, no invoke class at all
        try {
            $rcm = ReturnClauseMgr::init()
                ->setFcnInvoke(
                    [
                        FcnInvokeMgr::factory( null, FcnInvokeMgr::FACTORY),
                        FcnInvokeMgr::factory( null, 'method57_1'),
                    ]
                );
            if( DISPLAYfim ) {
                echo ltrim( $rcm->toString()) . PHP_EOL;
            }
            $this->assertTrue( false, 'case 57-1-1 ' );
        }
        catch( Exception $e ) {
            /*
            echo get_class( $e ) . ' in ' . $e->getFile() . '(' . $e->getLine() . ')' . PHP_EOL; // test ###
            echo $e->getMessage() . PHP_EOL; // test ###
            echo $e->getTraceAsString() . PHP_EOL; // test ###
            */
            $this->assertTrue( true, 'case 57-1-2' );
        }

        // accepted !!
        try {
            $rcm = ReturnClauseMgr::init()
                ->setFcnInvoke(
                    [
                        FcnInvokeMgr::factory( 'SourceClass', FcnInvokeMgr::FACTORY),
                        FcnInvokeMgr::factory( null, 'method57_2'),
                    ]
                );
            $this->assertTrue( true, 'case 57-2-1' );
        }
        catch( Exception $e ) {
            $this->assertTrue( false, 'case 57-2-2' );
        }

        // Not accepted, no (first) invoke class
        try {
            $rcm = ReturnClauseMgr::init()
                ->setFcnInvoke(
                    [
                        FcnInvokeMgr::factory( null, FcnInvokeMgr::FACTORY),
                        FcnInvokeMgr::factory( 'SourceClass', 'method57_3'),
                    ]
                );
            if( DISPLAYfim ) {
                echo ltrim( $rcm->toString()) . PHP_EOL;
            }
            $this->assertTrue( false, 'case 57-3-1' );
        }
        catch( Exception $e ) {
            $this->assertTrue( true, 'case 57-3-2' );
        }

        // Not accepted (#2) invoke class
        try {
            $rcm = ReturnClauseMgr::init()
                ->setFcnInvoke(
                    [
                        FcnInvokeMgr::factory( 'SourceClass', FcnInvokeMgr::FACTORY),
                        FcnInvokeMgr::factory( 'ns\123\src\Klass', 'method57_4'),
                        ]
                );
            if( DISPLAYfim ) {
                echo ltrim( $rcm->toString()) . PHP_EOL;
            }
            $this->assertTrue( false, 'case 57-4-1' );
        }
        catch( Exception $e ) {
            $this->assertTrue( true, 'case 57-4-2' );
        }

        // Accepted, invoke class replace by first
        try {
            $rcm = ReturnClauseMgr::init()
                ->setFcnInvoke(
                    [
                        FcnInvokeMgr::factory( 'SourceClass', FcnInvokeMgr::FACTORY),
                        FcnInvokeMgr::factory( 'ns\acme\src\Klass', 'method57_5'),
                        ]
                );
            if( DISPLAYfim ) {
                echo ltrim( $rcm->toString()) . PHP_EOL;
            }
            $this->assertTrue( true, 'case 57-5-1' );
        }
        catch( Exception $e ) {
            $this->assertTrue( false, 'case 57-5-2' );
        }
    }
}
