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
    public function AssignClauseMgrTest21DataProvider() : array
    {
        $testData = [];

        foreach( self::getTargetArr1() as $target ) {
            foreach( self::FcnInvokeMgrTest3ArgumentProvider() as $argSet ) {
                foreach( self::FcnInvokeMgrFunctionProvider() as $function ) {

                    $testData[] = [
                        $target[0] . '-' . $argSet[0] . '-' . $function[0], // case
                        [ $target[1], $target[2], $target[3], ],            // target
                        [ $function[1], $function[2] ],                     // function
                        $argSet[1],                                         // set of args
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
     * Same intro as in ReturnClauseMgrTest2::ReturnClauseMgrTest21()
     *
     * @test
     * @dataProvider AssignClauseMgrTest21DataProvider
     *
     * @param string $case
     * @param array  $target
     * @param array  $method
     * @param array  $argSet
     * @param array  $expected
     */
    public function AssignClauseMgrTest21(
        string $case,
        array $target,
        array $method,
        $argSet,
        array $expected
    )
    {
        $acm = AssignClauseMgr::init()
            ->setTarget( $target[0], $target[1], $target[2] );
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
                $acm->appendInvoke( FcnInvokeMgr::factory( $method[0], $method[1], $argSet ));
                $initNo = 1;
                break;
            case ( 2 == $initNo ) :
                $acm->appendInvoke( FcnInvokeMgr::factory( $method[0], $method[1], $argSet ));
                $acm->appendInvoke( FcnInvokeMgr::factory( '$testClass1', 'testMethod1', $argSet ));
                $acm->appendInvoke( FcnInvokeMgr::factory( '$testClass2', 'testMethod2' ));
                break;
            default :
                $acm->setFcnInvoke(
                    [
                        FcnInvokeMgr::factory( $method[0], $method[1], $argSet ),
                        FcnInvokeMgr::factory( '$testClass1', 'testMethod1', $argSet ),
                        FcnInvokeMgr::factory( '$testClass2', 'testMethod2' )
                    ]
                );
                break;
        }
        $code = $acm->toString();
        $this->assertTrue(
            ( false !== strpos( $code, $expected[0] )),
            $case . '-' . $initNo . '-A actual : ' . ltrim( $code ) . ' expected : ' . trim( $expected[0] )
        );

        $this->assertTrue(
            ( false !== strpos( $code, $expected[1] )),
            $case . '-' . $initNo . '-B actual : ' . ltrim( $code ) . ' expected : ' . trim( $expected[1] )
        );

        // test NO typed arguments
        $code2 = str_replace( [ PHP_EOL, ' ' ] , '', $code );
        static $EXP3  = '($';
        static $EXP4  = '()';
        $this->assertTrue(
            (( false !== strpos( $code2, $EXP3  )) || ( false !== strpos( $code2, $EXP4  ))),
            $case . '-' . $initNo . '-C actual : ' . ltrim( $code ) . ' expected : ' . trim( $expected[1] )
        );

        if( DISPLAYacm2) {
            echo __FUNCTION__ . ' ' . $case . '-' . $initNo . ' : ' . trim( $code ) . PHP_EOL;
        }
    }


}
