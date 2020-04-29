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
    public function ReturnClauseMgrTest21DataProvider() {
        $testData = [];

        foreach( self::FcnInvokeMgrTest3ArgumentProvider() as $argSet ) {
            foreach( self::FcnInvokeMgrFunctionProvider() as $function ) {

                $testData[] = [
                    $argSet[0] . '-' . $function[0],   // case
                    [ $function[1], $function[2] ],    // function
                    $argSet[1],                        // args
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
     * @param string $case
     * @param array  $method
     * @param array  $argset
     * @param string $expFcnName
     */
    public function ReturnClauseMgrTest21( $case, array $method, $argset, $expFcnName ) {
        $rcm = ReturnClauseMgr::init( PHP_EOL, '    ' )
            ->setBaseIndent( '    ' );
        $initNo = array_rand( array_flip( [ 1, 2, 3 ] ));
        switch( true ) {
            case (( 1 == $initNo ) ||
                empty( $method[0] ) ||
                (( FcnInvokeMgr::THIS_KW != $method[0] ) && ! Util::isVarPrefixed( $method[0] ))) :
                $rcm->setFcnInvoke( FcnInvokeMgr::factory( $method[0], $method[1], $argset ));
                $initNo = 1;
                break;
            case ( 2 == $initNo ) :
                $rcm->setFcnInvoke( FcnInvokeMgr::factory( $method[0], $method[1], $argset ));
                $rcm->appendChainedInvoke( FcnInvokeMgr::factory( '$testClass1', 'testMethod1', $argset ));
                $rcm->appendChainedInvoke( FcnInvokeMgr::factory( '$testClass2', 'testMethod2' ));
                break;
            default :
                $chainedInvokes = [
                    FcnInvokeMgr::factory( $method[0], $method[1], $argset ),
                    FcnInvokeMgr::factory( '$testClass1', 'testMethod1', $argset ),
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
            case empty( $argset ) :
                break;
            case is_string( $argset ) :
                if( ! Util::isVarPrefixed( $argset )) {
                    $argset = ReturnClauseMgr::VARPREFIX . $argset;
                }
                $expected = 'return ' . $expFcnName . '( ' . $argset . ' )';
                $this->assertTrue(
                    ( false !== strpos( $code, $expected )),
                    $case . '-' . $initNo . '-B actual : ' . trim( $code ). ' expected : ' . $expected
                );
                break;
            case ( is_array( $argset ) && ( 4 >= count( $argset ))) :
                foreach( $argset as & $arg ) {
                    if( is_array( $arg )) {
                        $arg = reset( $arg ); // first is argName
                    }
                    if( ! Util::isVarPrefixed( $arg )) {
                        $arg = ReturnClauseMgr::VARPREFIX . $arg;
                    }
                }
                $expected = 'return ' . $expFcnName . '( ' . implode( ', ', $argset ) . ' )';
                $this->assertTrue(
                    ( false !== strpos( $code, $expected )),
                    $case . '-' . $initNo . '-C actual : ' . trim( $code ). ' expected : ' . $expected
                );
                break;
            default :
                $expected = 'return ' . $expFcnName . '(';
                $this->assertTrue(
                    ( false !== strpos( $code, $expected )),
                    $case . '-' . $initNo . '-D actual : ' . trim( $code ). ' expected : ' . $expected
                );
                break;
        }

        if( DISPLAYrcm2) {
            echo __FUNCTION__ . ' ' . $case . '-' . $initNo . ' : ' . trim( $code ) . PHP_EOL;
        }
    }


}
