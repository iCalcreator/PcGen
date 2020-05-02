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
namespace Kigkonsult\PcGen\Dto;

use Exception;
use PHPUnit\Framework\TestCase;

class VarDtoTest extends TestCase
{
    private static $SUMMARY     = 'summary';
    private static $DESCRIPTION = 'description';
    private static $FMT         = '%d-%d-%d : ';

    /**
     * Testing 'empty'
     *
     * @test
     */
    public function varDtoTest01() {
        $varDto = VarDto::factory( 'test1', VarDto::ARRAY_T );
        $this->assertTrue( $varDto->isTypedArray());

        $varDto = VarDto::factory( 'test2', VarDto::STRINGARRAY_T );
        $this->assertTrue( $varDto->isTypedArray());

        $varDto = VarDto::factory( 'test3', VarDto::STRING_T );
        $this->assertFalse( $varDto->isTypedArray());

        $varDto = VarDto::factory( 'test4', VarDto::STRING_T, null, VarDto::FLOAT_T );
        $this->assertTrue( $varDto->isSummarySet());
        $this->assertEquals( VarDto::FLOAT_T, $varDto->getSummary());

        $varDto = VarDto::factory( 'test5', VarDto::STRING_T, null, null, VarDto::INT_T );
        $this->assertTrue( $varDto->isDescriptionSet());
        $this->assertEquals( [ VarDto::INT_T ], $varDto->getDescription());
    }

    /**
     * @return array
     */
    public function dtoTest1Provider() {
        $testData = [];

        $testData[] = [
            11,
            [ 'varDto1' ]
        ];

        $testData[] = [
            12,
            [ 'varDto2', VarDto::STRING_T ]
        ];

        $testData[] = [
            13,
            [ 'varDto3', VarDto::STRING_T, 'varDto3' ]
        ];

        $testData[] = [
            14,
            [ 'varDto4', VarDto::STRING_T, 'varDto4', self::$SUMMARY ]
        ];

        $testData[] = [
            15,
            [ 'varDto5', VarDto::STRING_T, 'varDto5', self::$SUMMARY, self::$DESCRIPTION ]
        ];

        return $testData;
    }

    /**
     * @test
     * @dataProvider dtoTest1Provider
     *
     * @param int   $case
     * @param array $args
     */
    public function varDtoTest( $case, array $args ) {

        for( $x = 1; $x < 3; $x++ ) {
            if( 2 > $x ) {
                switch( count( $args )) {
                    case 1 :
                        $varDto = VarDto::factory( $args[0] );
                        break;
                    case 2 :
                        $varDto = VarDto::factory( $args[0], $args[1] );
                        break;
                    case 3 :
                        $varDto = VarDto::factory( $args[0], $args[1], $args[2] );
                        break;
                    case 4 :
                        $varDto = VarDto::factory( $args[0], $args[1], $args[2], $args[3] );
                        break;
                    default :
                        $varDto = VarDto::factory( $args[0], $args[1], $args[2], $args[3], $args[4] );
                        break;
                }
            } // end if
            else {
                $varDto = call_user_func_array( [ VarDto::class, 'factory' ], $args );
            }

            $this->assertTrue( $varDto->isNameSet());
            $this->assertEquals( $args[0], $varDto->getName(),
                                 sprintf( self::$FMT, $case, $x, 1 ) . var_export( $args, true ));

            if( isset( $args[1] )) {
                $this->assertTrue( $varDto->isVarTypeSet());
                $this->assertEquals( $args[1], $varDto->getVarType(),
                                     sprintf( self::$FMT, $case, $x, 2 ) . var_export( $args, true ));
                $this->assertTrue( is_string( $varDto->getParamTagVarType()));
            }
            else {
                $this->assertEquals( VarDto::MIXED_KW, $varDto->getParamTagVarType());
            }

            if( isset( $args[2] )) {
                $this->assertTrue( $varDto->isDefaultSet());
                $this->assertEquals( $args[2], $varDto->getDefault(),
                                     sprintf( self::$FMT, $case, $x, 3 ) . var_export( $args, true ));
            }
            $this->assertFalse( $varDto->isDefaultTypedNull());

            if( isset( $args[3] )) {
                $this->assertTrue( $varDto->isSummarySet());
                $this->assertEquals( $args[3], $varDto->getSummary(),
                                     sprintf( self::$FMT, $case, $x, 4 ) . var_export( $args, true ));
            }

            if( isset( $args[4] )) {
                $this->assertTrue( $varDto->isDescriptionSet());
                $this->assertEquals( [ $args[4] ], $varDto->getDescription(),
                                     sprintf( self::$FMT, $case, $x, 5 ) . var_export( $args, true ));
            }

        } // end for

    }

    /**
     * @test
     */
    public function VarDtoTest11() {
        $this->assertEquals( 'test2',
            VarDto::factory( 'test' )->setName( '$test2' )->getName()
        );
    }

    /**
     * @test
     */
    public function VarDtoTest15() {
        $this->assertFalse( VarDto::factory( 'test', [] )->isTypedArray());
        $this->assertTrue( VarDto::factory( 'test', VarDto::ARRAY_T )->isTypedArray());
        $this->assertTrue( VarDto::factory( 'test', VarDto::ARRAY2_T )->isTypedArray());
        $this->assertTrue( VarDto::factory( 'test', VarDto::STRINGARRAY_T )->isTypedArray());
        $this->assertTrue( is_array( VarDto::factory( 'test', [] )->getVarType()));
    }

    /**
     * @test
     */
    public function VarDtoTest16() {
        $this->assertFalse( VarDto::factory( 'test' )->hasTypeHintArraySpec());
        $this->assertFalse( VarDto::factory( 'test', VarDto::FLOAT_T )->hasTypeHintArraySpec());
        $this->assertFalse( VarDto::factory( 'test', VarDto::ARRAY_T )->hasTypeHintArraySpec());
        $this->assertFalse( VarDto::factory( 'test', VarDto::ARRAY2_T )->hasTypeHintArraySpec());
        $this->assertFalse( VarDto::factory( 'test', VarDto::STRING_T )->hasTypeHintArraySpec());

        $this->assertTrue( VarDto::factory( 'test', VarDto::CALLABLEARRAY_T )->hasTypeHintArraySpec());
    }

    /**
     * @test
     */
    public function VarDtoTest17() {
        $this->assertFalse( VarDto::factory( 'test' )->isTypeHint());
        $this->assertTrue( VarDto::factory( 'test', VarDto::ARRAY_T )->isTypeHint());
        $this->assertTrue( VarDto::factory( 'test', VarDto::CALLABLE_T )->isTypeHint());

        $this->assertFalse( VarDto::factory( 'test', VarDto::STRING_T )->isTypeHint( '5.6.0'));
        $this->assertTrue( VarDto::factory( 'test', VarDto::STRING_T )->isTypeHint( '7.0.25'));

        $this->assertFalse( VarDto::factory( 'test', VarDto::ITERABLE_T )->isTypeHint( '5.6.0'));
        $this->assertfalse( VarDto::factory( 'test', VarDto::ITERABLE_T )->isTypeHint( '7.0.25'));
        $this->assertTrue( VarDto::factory( 'test', VarDto::ITERABLE_T )->isTypeHint( '7.1.0'));

        $this->assertFalse( VarDto::factory( 'test', VarDto::OBJECT_KW )->isTypeHint( '5.6.0'));
        $this->assertFalse( VarDto::factory( 'test', VarDto::OBJECT_KW )->isTypeHint( '7.0.25'));
        $this->assertFalse( VarDto::factory( 'test', VarDto::OBJECT_KW )->isTypeHint( '7.1.0'));
        $this->assertTrue( VarDto::factory( 'test', VarDto::OBJECT_KW )->isTypeHint( '7.2.0'));

        $this->assertTrue( VarDto::factory( 'test', 'VarDto' )->isTypeHint());
        $this->assertTrue( VarDto::factory( 'test', 'VarDto[]' )->isTypeHint());

        $this->assertFalse( VarDto::factory( 'test', 1.2345 )->isTypeHint());
    }

    /**
     * @test
     */
    public function VarDtoTest18() {
        $this->assertTrue( VarDto::factory( 'test', VarDto::ARRAY_T, [] )->isDefaultArray());
        $this->assertFalse( VarDto::factory( 'test', VarDto::CALLABLE_T )->isDefaultArray());

        $this->assertFalse( VarDto::factory( 'test', VarDto::CALLABLE_T, null  )->isDefaultTypedArray());
        $this->assertTrue( VarDto::factory( 'test', VarDto::ARRAY_T, VarDto::ARRAY2_T )->isDefaultTypedArray());
        $this->assertTrue( VarDto::factory( 'test', VarDto::ARRAY_T, VarDto::STRINGARRAY_T )->isDefaultTypedArray());
        $this->assertFalse( VarDto::factory( 'test', VarDto::FLOAT_T, 1.2345 )->isDefaultTypedArray());
    }

    /**
     * Testing invalid default
     *
     * @test
     */
    public function VarDtoTest26() {
        try {
            VarDto::factory( 'test' )->setDefault( VarDto::factory( 'test' ));
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
    public function VarDtoTest33() {
        $this->assertTrue(
            VarDto::factory(
                'test',
                VarDto::class . VarDto::ARRAY2_T,
                VarDto::class . VarDto::ARRAY2_T
            )
                  ->isDefaultTypedArray()
        );
    }

}
