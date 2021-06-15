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

class ReturnClauseMgrTest2 extends TestCase
{

    use FimDataProviderTrait; // FcnInvokeMgrTest3ArgumentProvider + FcnInvokeMgrFunctionProvider

    /**
     * @return array
     */
    public function ReturnClauseMgrTest21DataProvider() : array
    {
        $testData = [];

        foreach( self::FcnInvokeMgrTest3ArgumentProvider() as $argSet ) {
            foreach( self::FcnInvokeMgrFunctionProvider() as $function ) {

                $testData[] = [
                    $argSet[0] . '-' . $function[0],   // case
                    [ $function[1], $function[2] ],    // function
                    $argSet[1],                        // set of args
                    $function[3]                       // expected
                ];

            }
        }

        return $testData;
    }

    /**
     * Testing ReturnClauseMgrTest with FcnInvokeMgr as source
     *
     * Same intro as in AssignClauseMgrTest2::AssignClauseMgrTest21()
     *
     * @test
     * @dataProvider ReturnClauseMgrTest21DataProvider
     *
     * @param string       $case
     * @param array        $method
     * @param string|array $argSet
     * @param string       $expFcnName
     */
    public function ReturnClauseMgrTest21( string $case, array $method, $argSet, string $expFcnName ) {
        $rcm = ReturnClauseMgr::init( PHP_EOL, '    ', '    ' );
        $initNo = array_rand( array_flip( [ 1, 2, 3 ] ));
        if( empty( $argSet )) {
            $argSet = null;
        }
        elseif( ! is_array( $argSet )) {
            $argSet = [ $argSet ];
        }
        switch( true ) {
            case (( 1 == $initNo ) ||
                empty( $method[0] ) ||
                (( FcnInvokeMgr::THIS_KW != $method[0] ) && ! Util::isVarPrefixed( $method[0] ))) :
                $rcm->appendInvoke( FcnInvokeMgr::factory( $method[0], $method[1], $argSet ));
                $initNo = 1;
                break;
            case ( 2 == $initNo ) :
                $rcm->appendInvoke( FcnInvokeMgr::factory( $method[0], $method[1], $argSet ));
                $rcm->appendInvoke( FcnInvokeMgr::factory( '$testClass1', 'testMethod1', $argSet ));
                $rcm->appendInvoke( FcnInvokeMgr::factory( '$testClass2', 'testMethod2' ));
                break;
            default :
                $chainedInvokes = [
                    FcnInvokeMgr::factory( $method[0], $method[1], $argSet ),
                    FcnInvokeMgr::factory( '$testClass1', 'testMethod1', $argSet ),
                    FcnInvokeMgr::factory( '$testClass2', 'testMethod2' )
                ];
                $rcm->setFcnInvoke( $chainedInvokes );
                break;
        }
        $code = $rcm->toString();
        $this->assertTrue(
            ( false !== strpos( $code, $expFcnName )),
            $case . '-' . $initNo . '-A actual : ' . trim( $code ). ' expected : ' . $expFcnName
        );

        switch( true ) {
            case empty( $argSet ) :
                $expected = 'return ' . $expFcnName . '()';
                break;
            case is_string( $argSet ) :
                $argSet   = Util::setVarPrefix( $argSet );
                $expected = 'return ' . $expFcnName . '( ' . $argSet . ' )';
                $this->assertTrue(
                    ( false !== strpos( $code, $expected )),
                    $case . '-' . $initNo . '-B actual : ' . trim( $code ). ' expected : ' . $expected
                );
                break;
            case ( is_array( $argSet ) && ( 4 >= count( $argSet ))) :

                // echo $case . '-' . $initNo . '-C argSet(' . count( $argSet ) . ') : ' . var_export( $argSet, true ) . PHP_EOL; // test ###

                foreach( $argSet as & $arg ) {
                    if( is_array( $arg )) {
                        $arg = reset( $arg ); // first is argName
                    }
                    $arg = Util::setVarPrefix( $arg );
                } // end foreach
                $expected = 'return ' . $expFcnName . '( ' . implode( ', ', $argSet ) . ' )';
                $this->assertTrue(
                    ( false !== strpos( $code, $expected )),
                    $case . '-' . $initNo . '-C actual : ' . ltrim( $code ). ' expected : ' . $expected
                );
                break;
            default :
                $expected = 'return ' . $expFcnName . '(';
                $this->assertTrue(
                    ( false !== strpos( $code, $expected )),
                    $case . '-' . $initNo . '-D actual : ' . ltrim( $code ). ' expected : ' . $expected
                );
                break;
        }

        $code2 = str_replace( [ PHP_EOL, ' ' ] , '', $code );
        static $EXP3  = '($';
        static $EXP4  = '()';
        $this->assertTrue(
            (( false !== strpos( $code2, $EXP3  )) || ( false !== strpos( $code2, $EXP4  ))),
            $case . '-' . $initNo . '-E actual : ' . ltrim( $code ) . ' expected : ' . trim( $expected )
        );

        if( DISPLAYrcm2) {
            echo __FUNCTION__ . ' ' . $case . '-' . $initNo . ' : ' . trim( $code ) . PHP_EOL;
        }
    }

    /**
     * Testing ChainInvokeMgr::AppendChainInvoke
     *
     * @test
     */
    public function chainInvokeMgrTest29() {
        try {
            ReturnClauseMgr::init()
                ->appendInvoke(
                    FcnInvokeMgr::factory( null, 'testMethod1', [ 'testArg' ] )
                )
                ->appendInvoke( FcnInvokeMgr::factory( '$testClass2', 'testMethod2' ));
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }

        try {
            ReturnClauseMgr::init()
                ->appendInvoke(
                    FcnInvokeMgr::factory( '$testClass1', 'testMethod1', [ 'testArg' ] )
                )
                ->appendInvoke( FcnInvokeMgr::factory( null, 'testMethod2' ));
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }

        try {
            ChainInvokeMgr::init()->toArray();
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }

    }


}
