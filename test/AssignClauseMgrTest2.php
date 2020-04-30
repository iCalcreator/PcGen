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

if( ! in_array( __DIR__ . '/AcmDataProviderTrait.php', get_included_files())) {
    include( __DIR__ . '/AcmDataProviderTrait.php' );
}
if( ! in_array( __DIR__ . '/FimDataProviderTrait.php', get_included_files())) {
    include( __DIR__ . '/FimDataProviderTrait.php' );
}

class AssignClauseMgrTest2 extends TestCase
{

    use AcmDataProviderTrait; // getTargetArr

    use FimDataProviderTrait; // FcnInvokeMgrTest3ArgumentProvider + FcnInvokeMgrFunctionProvider

    /**
     * @return array
     */
    public function AssignClauseMgrTest21DataProvider() {
        $testData = [];

        foreach( self::getTargetArr1() as $target ) {
            foreach( self::FcnInvokeMgrTest3ArgumentProvider() as $argSet ) {
                foreach( self::FcnInvokeMgrFunctionProvider() as $function ) {

                    $testData[] = [
                        $target[0] . '-' . $argSet[0] . '-' . $function[0], // case
                        [ $target[1], $target[2], $target[3], ],            // target
                        [ $function[1], $function[2] ],                     // function
                        $argSet[1],                                         // args
                        [ $target[4], $function[3] ]                        // expected
                    ];

                }
            }
        }

        return $testData;
    }

    /**
     * Testing AssignClauseMgr and FcnInvokeMgr as source
     *
     * Samer intro as in ReturnClauseMgrTest2::ReturnClauseMgrTest21()
     *
     * @test
     * @dataProvider AssignClauseMgrTest21DataProvider
     *
     * @param string $case
     * @param array  $target
     * @param array  $method
     * @param array  $argset
     * @param array  $expected
     */
    public function AssignClauseMgrTest21( $case, array $target, array $method, $argset, array $expected ) {
        $acm = AssignClauseMgr::init()
            ->setTarget( $target[0], $target[1], $target[2] );
        $initNo = array_rand( array_flip( [ 1, 2, 3 ] ));
        if( empty( $argset )) {
            $argset = null;
        }
        elseif( ! is_array( $argset )) {
            $argset = [ $argset ];
        }
        switch( true ) {
            case (( 1 == $initNo ) ||
                empty( $method[0] ) ||
                (( FcnInvokeMgr::THIS_KW != $method[0] ) && ! Util::isVarPrefixed( $method[0] ))) :
                $acm->setFcnInvoke( FcnInvokeMgr::factory( $method[0], $method[1], $argset ));
                $initNo = 1;
                break;
            case ( 2 == $initNo ) :
                $acm->setFcnInvoke( FcnInvokeMgr::factory( $method[0], $method[1], $argset ));
                $acm->appendChainedInvoke( FcnInvokeMgr::factory( '$testClass1', 'testMethod1', $argset ));
                $acm->appendChainedInvoke( FcnInvokeMgr::factory( '$testClass2', 'testMethod2' ));
                break;
            default :
                $acm->setFcnInvoke(
                    [
                        FcnInvokeMgr::factory( $method[0], $method[1], $argset ),
                        FcnInvokeMgr::factory( '$testClass1', 'testMethod1', $argset ),
                        FcnInvokeMgr::factory( '$testClass2', 'testMethod2' )
                    ]
                );
                break;
        }
        $code = $acm->toString();
        $this->assertTrue(
            ( false !== strpos( $code, $expected[0] )),
            $case . '-' . $initNo . '-A actual : ' . trim( $code ) . ' expected : ' . trim( $expected[0] )
        );

        $this->assertTrue(
            ( false !== strpos( $code, $expected[1] )),
            $case . '-' . $initNo . 'B actual : ' . trim( $code ) . ' expected : ' . trim( $expected[1] )
        );

        if( DISPLAYacm2) {
            echo __FUNCTION__ . ' ' . $case . '-' . $initNo . ' : ' . trim( $code ) . PHP_EOL;
        }
    }


}
