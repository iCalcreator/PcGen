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
namespace Kigkonsult\PcGen\Dto;

use PHPUnit\Framework\TestCase;

class ArgumentDtoTest extends TestCase
{
    private static $SUMMARY     = 'summary';
    private static $DESCRIPTION = 'description';
    private static $FMT         = '%d-%d-%d : ';

    /**
     * @return array
     */
    public function argumentDtoTest1Provider() : array
    {
        $testData = [];

        $testData[] = [
            11,
            [ 'argumentDto1' ]
        ];

        $testData[] = [
            12,
            [ 'argumentDto2', VarDto::STRING_T ]
        ];

        $testData[] = [
            13,
            [ 'argumentDto3', VarDto::STRING_T, 'argumentDto3' ]
        ];

        $testData[] = [
            14,
            [ 'argumentDto4', VarDto::STRING_T, 'argumentDto4', self::$SUMMARY ]
        ];

        $testData[] = [
            15,
            [ 'argumentDto5', VarDto::STRING_T, 'argumentDto5', self::$SUMMARY, self::$DESCRIPTION ]
        ];

        return $testData;
    }

    /**
     * @test
     * @dataProvider argumentDtoTest1Provider
     *
     * @param int   $case
     * @param array $args
     */
    public function ArgumentDtoTest1( int $case, array $args ) {

        for( $x = 1; $x < 5; $x++ ) {
            if( 1 == $x ) {
                switch( count( $args )) {
                    case 1 :
                        $argumentDto = ArgumentDto::factory( $args[0] );
                        break;
                    case 2 :
                        $argumentDto = ArgumentDto::factory( $args[0], $args[1] );
                        break;
                    case 3 :
                        $argumentDto = ArgumentDto::factory( $args[0], $args[1], $args[2] );
                        break;
                    case 4 :
                        $argumentDto = ArgumentDto::factory( $args[0], $args[1], $args[2], $args[3] );
                        break;
                    default :
                        $argumentDto = ArgumentDto::factory( $args[0], $args[1], $args[2], $args[3], $args[4] );
                        break;
                }
            } // end if
            elseif( 2 == $x ) {
                switch( count( $args )) {
                    case 1 :
                        $argumentDto = ArgumentDto::factory( VarDto::factory( $args[0] ));
                        break;
                    case 2 :
                        $argumentDto = ArgumentDto::factory( VarDto::factory( $args[0], $args[1] ));
                        break;
                    case 3 :
                        $argumentDto = ArgumentDto::factory( VarDto::factory( $args[0], $args[1], $args[2] ));
                        break;
                    case 4 :
                        $argumentDto = ArgumentDto::factory( VarDto::factory( $args[0], $args[1], $args[2], $args[3] ));
                        break;
                    default :
                        $argumentDto = ArgumentDto::factory( VarDto::factory( $args[0], $args[1], $args[2], $args[3], $args[4] ));
                        break;
                }
            } // end if
            elseif( 4 == $x ) {
                $argumentDto = call_user_func_array( [ ArgumentDto::class, 'factory' ], $args );
            } // end if
            else {
                $argumentDto = call_user_func_array(
                    [ ArgumentDto::class, 'factory' ],
                    [ call_user_func_array( [ VarDto::class, 'factory' ], $args ) ]
                );
            }

            $this->assertEquals( $args[0], $argumentDto->getName(),
                sprintf( self::$FMT, $case, $x, 1 ) . var_export( $args, true ));

            if( isset( $args[1] )) {
                $this->assertEquals( $args[1], $argumentDto->getVarType(),
                    sprintf( self::$FMT, $case, $x, 2 ) . var_export( $args, true ));
            }

            if( isset( $args[2] )) {
                $this->assertEquals( $args[2], $argumentDto->getDefault(),
                    sprintf( self::$FMT, $case, $x, 3 ) . var_export( $args, true ));
            }

            if( isset( $args[3] )) {
                $this->assertEquals( $args[3], $argumentDto->getSummary(),
                    sprintf( self::$FMT, $case, $x, 4 ) . var_export( $args, true ));
            }

            if( isset( $args[4] )) {
                $this->assertEquals( [ $args[4] ], $argumentDto->getDescription(),
                    sprintf( self::$FMT, $case, $x, 5 ) . var_export( $args, true ));
            }

        } // end for

    }

    /**
     * @return array
     */
    public function argumentDtoTest2Provider() : array
    {
        $testData = [];

        $testData[] = [
            21,
            [],
            [ false, false, false ]
        ];

        $testData[] = [
            211,
            [ true ],
            [ true, false, false ]
        ];

        $testData[] = [
            212,
            [ false ],
            [ false, false, false ]
        ];

        $testData[] = [
            221,
            [ null, true ],
            [ false, true, false ]
        ];

        $testData[] = [
            222,
            [ null, false ],
            [ false, false, false ]
        ];

        $testData[] = [
            231,
            [ null, null, true ],
            [ false, false, true ]
        ];

        $testData[] = [
            232,
            [ null, null, false ],
            [ false, false, false ]
        ];

        return $testData;
    }

    /**
     * @test
     * @dataProvider argumentDtoTest2Provider
     *
     * @param int   $case
     * @param array $xtra
     * @param array $exp
     */
    public function ArgumentDtoTest2( int $case, array $xtra, array $exp ) {
        $ARGS = [ 'argumentDto5', VarDto::STRING_T, 'argumentDto5', self::$SUMMARY, self::$DESCRIPTION ];

        $argumentDto = call_user_func_array( [ ArgumentDto::class, 'factory' ], $ARGS );

        $case = (string) $case;
        switch( count( $xtra )) {
            case 0 :
                $this->assertFalse( $argumentDto->isByReference(), $case );
                $this->assertTrue( is_int( $argumentDto->getUpdClassProp()), $case );
                $this->assertFalse( $argumentDto->isNextVarPropIndex(), $case );
                break;
            case 1 :
                $argumentDto->setByReference( $xtra[0] );
                $this->assertEquals( $exp[0],  $argumentDto->isByReference(), $case );
                $this->assertTrue(( $exp[1] == $argumentDto->getUpdClassProp()), $case );
                $this->assertEquals( $exp[2],  $argumentDto->isNextVarPropIndex(), $case );
                break;
            case 2 :
                $argumentDto->setUpdClassProperty( $xtra[1] );
                $this->assertEquals( $exp[0],  $argumentDto->isByReference(), $case );
                $this->assertTrue(( $exp[1] == $argumentDto->getUpdClassProp()), $case );
                $this->assertEquals( $exp[2],  $argumentDto->isNextVarPropIndex(), $case );
                break;
            case 3 :
                $argumentDto->setNextVarPropIndex( $xtra[2] );
                $this->assertEquals( $exp[0],  $argumentDto->isByReference(), $case );
                $this->assertTrue(( $exp[1] == $argumentDto->getUpdClassProp()), $case );
                $this->assertEquals( false, $argumentDto->isNextVarPropIndex(), $case );

                $argumentDto->setVarType( ArgumentDto::ARRAY_T );
                $argumentDto->setNextVarPropIndex( $xtra[2] );
                $this->assertEquals( $exp[2], $argumentDto->isNextVarPropIndex(), $case );
                break;
        } // end switch
    }

}
