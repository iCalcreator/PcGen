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
use Kigkonsult\PcGen\Dto\ArgumentDto;
use Kigkonsult\PcGen\Dto\VarDto;
use PHPUnit\Framework\TestCase;

class FcnFrameMgrTest extends TestCase
{

    private static $dblVarPrefix = '$$';

    public static function fcnFrameMgrTest1DataProvider() {
        $testData = [];

        $testData[] = [
            11,
            'arg11',
            null,
            null,
            '$arg11'
        ];


        $testData[] = [
            22,
            'arg22',
            FcnFrameMgr::BOOL_T,
            null,
            '$arg22'
        ];

        $testData[] = [
            23,
            'arg23',
            FcnFrameMgr::BOOL_T,
            true,
            '$arg23 = true'
        ];

        $testData[] = [
            24,
            'arg24',
            FcnFrameMgr::BOOL_T,
            false,
            '$arg24 = false'
        ];


        $testData[] = [
            32,
            'arg32',
            FcnFrameMgr::INT_T,
            null,
            '$arg32'
        ];

        $testData[] = [
            33,
            'arg33',
            FcnFrameMgr::INT_T,
            0,
            '$arg33 = 0'
        ];

        $testData[] = [
            34,
            'arg34',
            FcnFrameMgr::INT_T,
            34,
            '$arg34 = 34'
        ];

        $testData[] = [
            35,
            'arg35',
            FcnFrameMgr::INT_T,
            -1,
            '$arg35 = -1'
        ];


        $testData[] = [
            42,
            'arg42',
            FcnFrameMgr::FLOAT_T,
            null,
            '$arg42'
        ];

        $testData[] = [
            43,
            'arg43',
            FcnFrameMgr::FLOAT_T,
            0.0,
            '$arg43 = 0.0'
        ];

        $testData[] = [
            431,
            'arg431',
            FcnFrameMgr::FLOAT_T,
            0.00,
            '$arg431 = 0.0'
        ];

        $testData[] = [
            432,
            'arg432',
            FcnFrameMgr::FLOAT_T,
            0.01,
            '$arg432 = 0.01'
        ];

        $testData[] = [
            433,
            'arg433',
            FcnFrameMgr::FLOAT_T,
            0.001,
            '$arg433 = 0.001'
        ];

        $testData[] = [
            434,
            'arg434',
            FcnFrameMgr::FLOAT_T,
            0.0001,
            '$arg434 = 0.0001'
        ];

        $testData[] = [
            435,
            'arg435',
            FcnFrameMgr::FLOAT_T,
            0.00001,
            '$arg435 = 0.00001'
        ];

        $testData[] = [
            436,
            'arg436',
            FcnFrameMgr::FLOAT_T,
            0.000001,
            '$arg436 = 0.000001'
        ];

        $testData[] = [
            437,
            'arg437',
            FcnFrameMgr::FLOAT_T,
            0.0000001,
            '$arg437 = 0.0000001'
        ];

        $testData[] = [
            439,
            'arg439',
            FcnFrameMgr::FLOAT_T,
            -0.000000001,
            '$arg439 = -0.000000001'
        ];

        $testData[] = [
            44,
            'arg44',
            FcnFrameMgr::FLOAT_T,
            44.44,
            '$arg44 = 44.44'
        ];

        $testData[] = [
            442,
            'arg442',
            FcnFrameMgr::FLOAT_T,
            44.4444444444,
            '$arg442 = 44.4444444444'
        ];

        $testData[] = [
            45,
            'arg45',
            FcnFrameMgr::FLOAT_T,
            -45.45,
            '$arg45 = -45.45'
        ];

        $testData[] = [
            452,
            'arg452',
            FcnFrameMgr::FLOAT_T,
            -45.4599999999,
            '$arg452 = -45.4599999999'
        ];

        $testData[] = [
            453,
            'arg453',
            FcnFrameMgr::FLOAT_T,
            -45.4545454545,
            '$arg453 = -45.4545454545'
        ];


        $testData[] = [
            52,
            'arg52',
            FcnFrameMgr::STRING_T,
            null,
            '$arg52'
        ];

        $testData[] = [
            53,
            'arg53',
            FcnFrameMgr::STRING_T,
            'arg53',
            '$arg53 = \'arg53\''
        ];

        $testData[] = [
            611,
            'arg611',
            FcnFrameMgr::ARRAY_T,
            null,
            '$arg611'
        ];

        $testData[] = [
            612,
            'arg612',
            FcnFrameMgr::ARRAY_T,
            FcnFrameMgr::ARRAY_T,
            '$arg612 = []'
        ];

        $testData[] = [
            613,
            'arg613',
            FcnFrameMgr::ARRAY_T,
            FcnFrameMgr::ARRAY2_T,
            '$arg613 = []'
        ];

        $testData[] = [
            614,
            'arg614',
            FcnFrameMgr::ARRAY_T,
            [ true, false, 0, 1, -1, 1.1, -1.1, 'value614' ],
            '$arg614 = [ true, false, 0, 1, -1, 1.1, -1.1, \'value614\', ]'
        ];

        $testData[] = [
            615,
            'arg615',
            FcnFrameMgr::ARRAY_T,
            [],
            '$arg615 = []'
        ];

        $testData[] = [
            621,
            'arg621',
            FcnFrameMgr::ARRAY2_T,
            null,
            '$arg621'
        ];

        $testData[] = [
            622,
            'arg622',
            FcnFrameMgr::ARRAY2_T,
            FcnFrameMgr::ARRAY_T,
            '$arg622 = []'
        ];

        $testData[] = [
            623,
            'arg623',
            FcnFrameMgr::ARRAY2_T,
            FcnFrameMgr::ARRAY2_T,
            '$arg623 = []'
        ];

        $testData[] = [
            631,
            'arg631',
            FcnFrameMgr::STRINGARRAY_T,
            null,
            '$arg631'
        ];

        $testData[] = [
            632,
            'arg632',
            FcnFrameMgr::STRINGARRAY_T,
            FcnFrameMgr::ARRAY_T,
            '$arg632 = []'
        ];

        $testData[] = [
            633,
            'arg633',
            FcnFrameMgr::STRINGARRAY_T,
            FcnFrameMgr::ARRAY2_T,
            '$arg633 = []'
        ];

        $testData[] = [
            641,
            'arg641',
            'FcnFrameMgr[]',
            null,
            '$arg641'
        ];

        $testData[] = [
            642,
            'arg642',
            'FcnFrameMgr[]',
            FcnFrameMgr::ARRAY_T,
            '$arg642 = []'
        ];

        $testData[] = [
            643,
            'arg643',
            'FcnFrameMgr[]',
            FcnFrameMgr::ARRAY2_T,
            '$arg643 = []'
        ];

        $testData[] = [
            651,
            'arg651',
            '$class[]',
            null,
            '$arg651'
        ];

        $testData[] = [
            652,
            'arg652',
            '$class[]',
            FcnFrameMgr::ARRAY_T,
            '$arg652 = []'
        ];

        $testData[] = [
            653,
            'arg653',
            '$class[]',
            FcnFrameMgr::ARRAY2_T,
            '$arg653 = []'
        ];

        return $testData;
    }

    /**
     * Testing add of one argument of scalar or array type (also ArgumentTrait::renderOneArg && BaseA::renderScalarValue)
     *
     * @test
     * @dataProvider fcnFrameMgrTest1DataProvider
     * @param int    $case
     * @param string $argName
     * @param string $varType
     * @param mixed  $default
     * @param string $expected
     */
    public function fcnFrameMgrTest1( $case, $argName, $varType, $default, $expected ) {
        static $tmpl = '    public function %1$s( %2$s%3$s ) {%4$s    }%4$s';
        $fcnName  = __FUNCTION__ . '_' . $case;
        $varDto   = VarDto::factory( $argName, $varType, $default );
        $typeHint = ( $varDto->isTypeHint( FcnFrameMgr::getTargetPhpVersion(), $typeHint2 ))
            ? $typeHint2 . ' '
            : '';
        $ffm      = FcnFrameMgr::init()->setName( $fcnName );
        if( 1 == array_rand( [ 1, 2 ] )) {
            $ffm->addArgument( $varDto );
        }
        else {
            $ffm->setArguments( [ $varDto ] );
        }
        $code = $ffm->toString();
        $expected = sprintf( $tmpl, $fcnName, $typeHint, $expected, PHP_EOL );
        $this->assertEquals(
            $expected,
            $code,
            __FUNCTION__ . ' ' . $case . ' ' . $code
        );
        if( DISPLAYffm ) {
            echo str_replace ( PHP_EOL, '', $code ) . PHP_EOL;
        }
    }

    public static function fcnFrameMgrTest2DataProvider() {
            $testData = [];

        $testData[] = [
            11,
            null
        ];

        $testData[] = [
            21,
            'argument21',
        ];

        $testData[] = [
            22,
            '$argument22',
        ];

        $testData[] = [
            31,
            [
                'argument31',
            ]
        ];

        $testData[] = [
            32,
            [
                'argument321',
                'argument322',
            ]
        ];

        $testData[] = [
            41,
            [
                '$argument41',
            ]
        ];

        $testData[] = [
            42,
            [
                '$argument421',
                'argument422',
            ]
        ];

        $testData[] = [
            43,
            [
                'argument431',
                '$argument432',
            ]
        ];

        $testData[] = [
            41,
            [
                [ 'argument41' ]
            ]
        ];

        $testData[] = [
            42,
            [
                [ 'argument42', FcnFrameMgr::ARRAY_T ]
            ]
        ];

        $testData[] = [
            43,
            [
                [ 'argument43', null, FcnFrameMgr::NULL_T ]
            ]
        ];

        $testData[] = [
            44,
            [
                [ 'argument44', FcnFrameMgr::ARRAY_T, FcnFrameMgr::ARRAY2_T ]
            ]
        ];


        $testData[] = [
            45,
            [
                [ 'argument45', null, null, true ]
            ]
        ];

        $testData[] = [
            46,
            [
                [ 'argument46', FcnFrameMgr::ARRAY_T, null, true ]
            ]
        ];

        $testData[] = [
            47,
            [
                [ 'argument47', null, FcnFrameMgr::TRUE_KW, true ]
            ]
        ];

        $testData[] = [
            48,
            [
                [ 'argument48', FcnFrameMgr::ARRAY_T, FcnFrameMgr::ARRAY2_T, true ]
            ]
        ];

        $testData[] = [
            49,
            [
                [ 'argument49', [ FcnFrameMgr::INT_T, FcnFrameMgr::STRING_T ], null, true ]
            ]
        ];

        $testData[] = [
            51,
            [
                [ '$argument511', FcnFrameMgr::ARRAY_T, FcnFrameMgr::ARRAY2_T ],
                [ 'argument512', null, FcnFrameMgr::NULL_T ],
            ]
        ];

        $testData[] = [
            52,
            [
                [ 'argument521', FcnFrameMgr::ARRAY_T, FcnFrameMgr::ARRAY2_T, true ],
                [ '$argument522', null, FcnFrameMgr::NULL_T, true ],
            ]
        ];

        $testData[] = [
            61,
            [
                [ 'argument611', FcnFrameMgr::ARRAY_T, FcnFrameMgr::ARRAY2_T, true ],
                [ 'argument612', FcnFrameMgr::class,   FcnFrameMgr::NULL_T ],
                [ 'argument613', FcnFrameMgr::ARRAY_T, FcnFrameMgr::ARRAY2_T, true ],
            ]
        ];

        $testData[] = [
            71,
            [
                [ 'argument711', FcnFrameMgr::ARRAY_T, FcnFrameMgr::ARRAY2_T, true ],
                [ 'argument712', FcnFrameMgr::class,   FcnFrameMgr::NULL_T ],
                [ 'argument713', FcnFrameMgr::ARRAY_T, FcnFrameMgr::ARRAY2_T, true ],
                [ 'argument714', FcnFrameMgr::class,   FcnFrameMgr::NULL_T ],
            ]
        ];

        $testData[] = [
            81,
            [
                [ 'argument811', FcnFrameMgr::class,   FcnFrameMgr::NULL_T ],
                [ 'argument812', FcnFrameMgr::ARRAY_T, FcnFrameMgr::ARRAY2_T, true ],
                [ 'argument813', FcnFrameMgr::class,   FcnFrameMgr::NULL_T ],
                [ 'argument814', FcnFrameMgr::ARRAY_T, FcnFrameMgr::ARRAY2_T, true ],
                [ 'argument815', FcnFrameMgr::class,   FcnFrameMgr::NULL_T ],
            ]
        ];

        return $testData;
    }

    /**
     * Testing add/set of arguments
     *
     * @test
     * @dataProvider fcnFrameMgrTest2DataProvider
     * @param int $case
     * @param string|array $args
     */
    public function fcnFrameMgrTest2( $case, $args = null ) {
        $case += 200;
        if( empty( $args )) {
            $args = null;
        }
        elseif( ! is_array( $args )) {
            $args = [ $args ];
        }
        if( 1 == array_rand( [ 1, 2 ] )) {
            $ffg = FcnFrameMgr::init(PHP_EOL, '    ')
                ->setName('theFunctionName' )
                ->setArguments( $args );
        }
        else {
            $ffg = FcnFrameMgr::factory( 'theFunctionName', $args )
                ->setVisibility(FcnFrameMgr::PUBLIC_)
                ->setBody(self::compressArgs( $args ));
        }

        $this->assertFalse( strpos( $ffg->toString(), self::$dblVarPrefix ));
        $this->classReturnTester( $case . '-A', $ffg );

        $this->fcnReturnTester( $case . '-B', $ffg );

        $ffg->setVisibility( FcnFrameMgr::PRIVATE_);
        $ffg->setStatic( true );
//        $this->fcnReturnTester( $case . '-C', $ffg );
        $code = $ffg->toString();
        $this->assertNotFalse(
            strpos( $code, 'private static function' ),
            'D' . $case . ' - ' . PHP_EOL . $code );
        $ffg->setStatic( false );
        $ffg->setVisibility();
        $this->fcnReturnTester( $case . '-F', $ffg );

    }

    /**
     * @test
     * @dataProvider fcnFrameMgrTest2DataProvider
     * @param int $case
     * @param string|array $args
     */
    public function fcnFrameMgrTest3( $case, $args = null ) {
        $case += 300;
        if( is_array( $args )) {
            foreach( $args as & $argSet ) {
                if( ! is_array( $argSet )) {
                    $argSet = [ VarDto::factory( $argSet ) ];
                    continue;
                }
                switch( count( $argSet )) {
                    case 1 :
                        $argSet = [ VarDto::factory( $argSet[0] ) ];
                        break;
                    case 2 :
                        $argSet = [ VarDto::factory( $argSet[0], $argSet[1] ) ];
                        break;
                    case 3 :
                        $argSet = [ VarDto::factory( $argSet[0], $argSet[1], $argSet[2] ) ];
                        break;
                    default :
                        $argSet = [ VarDto::factory( $argSet[0], $argSet[1], $argSet[2] ), $argSet[3] ];
                        break;
                } // end switch
            } // end foreach
        }
        else {
            $this->assertTrue( true );
            return;
        }
        $ffg = FcnFrameMgr::init( PHP_EOL, '    ' )
            ->setVisibility( FcnFrameMgr::PUBLIC_ )
            ->setName( 'theFunctionName' )
            ->setArguments( $args )
            ->setBody( self::compressArgs( $args ));
        $this->classReturnTester( $case . '-G', $ffg );
    }

    /**
     * @test
     * @dataProvider fcnFrameMgrTest2DataProvider
     * @param int $case
     * @param string|array $args
     */
    public function fcnFrameMgrTest4( $case, $args = null ) {
        $case += 400;
        if( ! is_array( $args )) {
            $this->assertTrue( true );
            return;
        }
        $ffg = FcnFrameMgr::init( PHP_EOL, '    ' )
                          ->setVisibility( FcnFrameMgr::PUBLIC_ )
                          ->setName( 'theFunctionName' )
                          ->setBody( self::compressArgs( $args ));
        foreach( $args as $aIx => $argSet ) {
            if( ! is_array( $argSet ) ) {
                $args[$aIx] = [ VarDto::factory( $argSet ), null, true, true ];
                continue;
            }
            switch( count( $argSet ) ) {
                case 1 :
                    $args[$aIx] = [ VarDto::factory( $argSet[0] ), null, true, true ];
                    break;
                case 2 :
                    $args[$aIx] = [ VarDto::factory( $argSet[0], $argSet[1] ), null, true, true ];
                    break;
                case 3 :
                    $args[$aIx] = [ VarDto::factory( $argSet[0], $argSet[1], $argSet[2] ), null, true, true ];
                    break;
                default :
                    $args[$aIx] = [ VarDto::factory( $argSet[0], $argSet[1], $argSet[2] ), $argSet[3], true, true ];
                    break;
            } // end switch
        } // end foreach
        foreach( $args as $argSet ) {
            $ffg->addArgument( $argSet[0], $argSet[1], $argSet[2], $argSet[3] );
        }

        $this->classReturnTester( $case . '-H', $ffg );
    }

    /**
     * @test
     * @dataProvider fcnFrameMgrTest2DataProvider
     * @param int $case
     * @param string|array $args
     */
    public function fcnFrameMgrTest5( $case, $args = null ) {
        $case += 500;
        if( is_array( $args )) {
            foreach( $args as & $argSet ) {
                if( ! is_array( $argSet )) {
                    $argSet = ArgumentDto::factory( $argSet );
                    continue;
                }
                switch( count( $argSet )) {
                    case 1 :
                        $argSet = ArgumentDto::factory( $argSet[0] );
                        break;
                    case 2 :
                        $argSet = ArgumentDto::factory( $argSet[0], $argSet[1] );
                        break;
                    case 3 :
                        $argSet = ArgumentDto::factory( $argSet[0], $argSet[1], $argSet[2] );
                        break;
                    default :
                        $argSet = ArgumentDto::factory( $argSet[0], $argSet[1], $argSet[2] )
                            ->setByReference( $argSet[3] );
                        break;
                } // end switch
            } // end foreach
        }
        else {
            $this->assertTrue( true );
            return;
        }
        $ffg = FcnFrameMgr::init( PHP_EOL, '    ' )
            ->setVisibility( FcnFrameMgr::PUBLIC_ )
            ->setName( 'theFunctionName' )
            ->setArguments( $args )
            ->setBody( self::compressArgs( $args ));
        $this->classReturnTester( $case . '-I', $ffg );
        $found = false;
        foreach( $args as $aIx => $argset ) {
            if( $argSet->isByReference()) {
                $found = $aIx;
                break;
            }
        }
        if( false !== $found ) { // test use of by-reference (todo move to ArgumentDtoTest)
            $code     = $ffg->toString();
            $expected = ' & $' . $args[$found]->getName();
            $this->assertTrue(
                ( false !== strpos( $code, $expected )),
                __FUNCTION__ . ' case ' . $case . PHP_EOL . $code
            );

            if( DISPLAYffm ) {
                echo __FUNCTION__ . ' case ' . $case . PHP_EOL . $code . PHP_EOL;
            }
        }
    }

    /**
     * Testing return of a class property value
     *
     * @param string      $case
     * @param FcnFrameMgr $ffg
     */
    public function classReturnTester( $case, FcnFrameMgr $ffg ) {

        $casex = $case . '-A';
        $code = $ffg->toString();
        $this->assertFalse( strpos( $code, 'return' ), $casex . PHP_EOL . $code );

        $casex = $case . '-B';
        $ffg->setReturnProperty();
        $code = $ffg->toString();
        $this->assertNotFalse( strpos( $code, 'return $this' ), $casex . PHP_EOL . $code );

        $casex = $case . '-C';
        $ffg->setReturnProperty( 'argument' );
        $code = $ffg->toString();
        $this->assertNotFalse( strpos( $code, 'return $this->argument' ), $casex . PHP_EOL . $code );

        $casex = $case . '-D';
        $ffg->setReturnProperty( 'argument', 1 );
        $code = $ffg->toString();
        $this->assertNotFalse( strpos( $code, 'return $this->argument[1]' ), $casex . PHP_EOL . $code );

        $casex = $case . '-E';
        $ffg->setReturnProperty( 'argument', 'position' );
        $code = $ffg->toString();
        $this->assertNotFalse( strpos( $code, 'return $this->argument[$position]' ), $casex . PHP_EOL . $code );

        $casex = $case . '-F';
        $ffg->setReturnProperty( 'argument', '$position' );
        $code = $ffg->toString();
        $this->assertFalse( strpos( $code, self::$dblVarPrefix ));
        $this->assertNotFalse( strpos( $code, 'return $this->argument[$position]' ), $casex . PHP_EOL . $code );

        if( DISPLAYffm ) {
            echo __FUNCTION__ . ' : case ' . $casex . ' : ' . PHP_EOL . $code . PHP_EOL . PHP_EOL; // test ###
        }
        $ffg->unsetReturnValue();
    }

    /**
     * Testing return of any value
     *
     * @param string      $case
     * @param FcnFrameMgr $ffg
     */
    public function fcnReturnTester( $case, FcnFrameMgr $ffg ) {

        $casex = $case . '-A';
        $code = $ffg->toString();
        $this->assertFalse( strpos( $code, 'return' ), $casex . PHP_EOL . $code );

        $casex = $case . '-B';
        $ffg->setReturnVariable( false );
        $code = $ffg->toString();
        $this->assertNotFalse( strpos( $code, 'return false' ), $casex . PHP_EOL . $code );

        $casex = $case . '-C';
        $ffg->setReturnVariable( '$argument' );
        $code = $ffg->toString();
        $this->assertNotFalse( strpos( $code, 'return $argument' ), $casex . PHP_EOL . $code );

        $casex = $case . '-D';
        $ffg->setReturnVariable( 'argument', 1 );
        $code = $ffg->toString();
        $this->assertNotFalse( strpos( $code, 'return $argument[1]' ), $casex . PHP_EOL . $code );

        $casex = $case . '-E';
        $ffg->setReturnVariable( 'argument', 'position' );
        $code = $ffg->toString();
        $this->assertNotFalse( strpos( $code, 'return $argument[$position]' ), $casex . PHP_EOL . $code );

        $casex = $case . '-F';
        $ffg->setReturnVariable( 'argument', '$position' );
        $code = $ffg->toString();
        $this->assertFalse( strpos( $code, self::$dblVarPrefix ));
        $this->assertNotFalse( strpos( $code, 'return $argument[$position]' ), $casex . PHP_EOL . $code );

        if( DISPLAYffm ) {
            echo __FUNCTION__ . ' : case ' . $casex . ' : ' . PHP_EOL . $code . PHP_EOL . PHP_EOL; // test ###
        }
        $ffg->unsetReturnValue();
    }

    /**
     * Testing closures
     *
     * @test
     * @dataProvider fcnFrameMgrTest2DataProvider
     * @param int $case
     * @param string|array $args
     */
    public function fcnFrameMgrTest7( $case, $args = null ) {
        $case += 900;
        if( empty( $args )) {
            $args = null;
        }
        elseif( ! is_array( $args )) {
            $args = [ $args ];
        }
        $ffg = FcnFrameMgr::init( PHP_EOL, '' )
            ->setVisibility()
            ->setArguments( $args )
            ->setBody( self::compressArgs( $args ));

        if( empty( $args )) {
            $casex = $case . '-G1';
            $code = $ffg->toString();
            $this->assertFalse( strpos( $code, 'use (' ), $casex . PHP_EOL . $code );
            $ffg->setVarUse();

            if( DISPLAYffm ) {
                echo __FUNCTION__ . ' : case ' . $casex . ' : ' . PHP_EOL . $code . PHP_EOL . PHP_EOL; // test ###
            }
            return;
        }
        if( is_string( $args[0] )) {
            $casex = $case . '-G2';
            $ffg->setVarUse( $args );
            $code = $ffg->toString();
            $this->assertFalse( strpos( $code, self::$dblVarPrefix ));
            $this->assertNOTFalse( strpos( $code, 'use (' ), $casex . PHP_EOL . $code );
            $ffg->setVarUse();

            if( DISPLAYffm ) {
                echo __FUNCTION__ . ' : case ' . $casex . ' : ' . PHP_EOL . $code . PHP_EOL . PHP_EOL; // test ###
            }
            return;
        }
        $casex = $case . '-G3';
        $args2 = [];
        foreach( $args as $aIx => $argSet ) {
            if( is_string( $argSet )) {
                $args2[$aIx][] = $argSet;
                continue;
            }
            $args2[$aIx][] = $argSet[0];
            if( array_key_exists( 3, $argSet )) {
//              $args2[$aIx][] = $argSet[3];
                $args2[$aIx] = ArgumentDto::factory( $argSet[0] )
                    ->setByReference( $argSet[3] );
            }
        }
        $ffg->setVarUse( $args2 );
        $code = $ffg->toString();
        $this->assertFalse( strpos( $code, self::$dblVarPrefix ));
        $this->assertNOTFalse( strpos( $code, 'use (' ), $casex . PHP_EOL . $code );
        $ffg->setVarUse();

        if( DISPLAYffm ) {
            echo __FUNCTION__ . ' : case ' . $casex . ' : ' . PHP_EOL . $code . PHP_EOL . PHP_EOL; // test ###
        }

        // FcnFrameMgr VarDto tests
        $casex   = $case . '-G4';
        $args2   = [];
        $cntArgs = count( $args );
        foreach( $args as $aIx => $argSet ) {
            if( is_string( $argSet )) {
                $args2[$aIx] = varDto::factory( $argSet );
                continue;
            }
            if( array_key_exists( 3, $argSet )) {
                if( 1 == $cntArgs ) {
                    $args2[$aIx] = varDto::factory( $argSet[0] );
                    continue;
                }
                $args2[$aIx][] = varDto::factory( $argSet[0] );
            }
            else {
                return;
            }
        }
        $ffg->setVarUse( $args2 );
        $code = $ffg->toString();
        $this->assertFalse( strpos( $code, self::$dblVarPrefix ));
        $this->assertNOTFalse( strpos( $code, 'use (' ), $casex . PHP_EOL . $code );
        $ffg->setVarUse();

        $casex   = $case . '-G5';
        foreach( $args2 as $arg ) {
            if( ! $arg instanceof VarDto ) {
                return;
            }
            $ffg->addVarUse( $arg );
        }
        $code = $ffg->toString();
        $this->assertFalse( strpos( $code, self::$dblVarPrefix ));
        $this->assertNOTFalse( strpos( $code, 'use (' ), $casex . PHP_EOL . $code );
        $ffg->setVarUse();

        echo __FUNCTION__ . ' : case ' . $casex . ' ok !' . PHP_EOL;
    }

    public static function argumentDtoTest9DataProvider() {
        $testData = [];

        $testData[] = [
            11,
            null,
            ArgumentDto::NONE
        ];

        $testData[] = [
            12,
            true,
            ArgumentDto::BEFORE
        ];

        $testData[] = [
            13,
            false,
            ArgumentDto::NONE
        ];

        $testData[] = [
            14,
            1,
            ArgumentDto::BEFORE
        ];

        $testData[] = [
            15,
            2,
            ArgumentDto::AFTER
        ];

        $testData[] = [
            16,
            9,
            ArgumentDto::AFTER
        ];

        return $testData;
    }

    /**
     * Testing ArgumentsTrait::grabUpdClassProperty()
     *
     * @test
     * @dataProvider argumentDtoTest9DataProvider()
     *
     * @param int   $case
     * @param mixed $arg
     * @param int   $expected
     */
    public function argumentDtoTest9( $case, $arg, $expected  ) {
        $result = FcnFrameMgr::factory( 'test', [  [ 'arg', null, null, null, $arg ] ] )
            ->getArgument( 0 )->getUpdClassProp();
        $this->assertEquals(
            $expected,
            $result,
            __METHOD__ . ' : case ' . $case . ' expected : ' . $expected . ', got : ' . $result
        );
    }

    /**
     * Testing 'empty'
     *
     * @test
     */
    public function fcnFrameMgrTest21() {
        try {
            FcnFrameMgr::init()->toString();
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * Testing invalid visibility
     *
     * @test
     */
    public function fcnFrameMgrTest22() {
        try {
            FcnFrameMgr::init()->setVisibility( FcnFrameMgr::FALSE_KW );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * Testing invalid add argument
     *
     * @test
     */
    public function fcnFrameMgrTest23() {
        try {
            FcnFrameMgr::init()->addArgument( [] );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * Testing invalid set argument
     *
     * @test
     */
    public function fcnFrameMgrTest24() {
        try {
            FcnFrameMgr::init()->setArguments( [ '' ] );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * Testing invalid add function use variable
     *
     * @test
     */
    public function fcnFrameMgrTest25() {
        try {
            FcnFrameMgr::init()->addVarUse( '' );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
        try {
            FcnFrameMgr::init()->addVarUse( false );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * Testing invalid set function use variable
     *
     * @test
     */
    public function fcnFrameMgrTest26() {
        try {
            FcnFrameMgr::init()->setVarUse( [ '' ] );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }

        try {
            FcnFrameMgr::init()->setVarUse( [ true ] );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * Testing invalid function class return code
     *
     * @test
     */
    public function fcnFrameMgrTest27() {
        try {
            FcnFrameMgr::init()->setReturnProperty( '' );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * Testing invalid function return code
     *
     * @test
     */
    public function fcnFrameMgrTest28() {
        try {
            FcnFrameMgr::init()->setReturnVariable( '' );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * testing invalid argument
     *
     * @test
     */
    public function fcnFrameMgrTest29() {
        try {
            FcnFrameMgr::init()->setArguments( [ 123 ] );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }

    }

    /**
     * Testing returnValue
     *
     * @test
     */
    public function fcnFrameMgrTest33() {
        $rvm = FcnFrameMgr::init()->setReturnValue( null, 123 )->getReturnValue();
        $this->assertTrue( $rvm instanceof ReturnClauseMgr );
        $this->assertEquals( 123, $rvm->getFixedSourceValue());

        $rvm = FcnFrameMgr::init()->setReturnFixedValue( 'test' )->getReturnValue();
        $this->assertTrue( $rvm instanceof ReturnClauseMgr );
        $this->assertEquals( 'test', $rvm->getFixedSourceValue());

        $rvm = FcnFrameMgr::init()->setReturnValue( null, '$test' )->getReturnValue();
        $this->assertTrue( $rvm instanceof ReturnClauseMgr );
        $this->assertTrue( ( false !== strpos( $rvm->toString(), '$test')));

        $rvm = FcnFrameMgr::init()->setReturnThis()->getReturnValue();
        $this->assertTrue( $rvm instanceof ReturnClauseMgr );
        $this->assertTrue( ( false !== strpos( $rvm->toString(), '$this')));
    }

    /**
     * Testing empty by-reference
     *
     * @test
     */
    public function argumentDtoTest11() {
        $this->assertTrue(
            ArgumentDto::factory( 'test' )
                       ->setByReference( true )
                       ->isByReference()
        );
    }

    /**
     * Return test body, arguments
     * @param $args
     * @return array
     */
    public static function compressArgs( $args ) {
        if( empty( $args )) {
            $row  = '/*' . PHP_EOL . ' * function body ' . PHP_EOL . ' *' . PHP_EOL;
        }
        else {
            $row  = '/*' . PHP_EOL . ' * function body, input : ' . PHP_EOL . ' * ' .
                str_replace( [ PHP_EOL, ' ' ], '', var_export( (array) $args, true )) . PHP_EOL ;
        }
        $row .= ' *' . PHP_EOL . ' */';
        return [ $row ];

    }
}
