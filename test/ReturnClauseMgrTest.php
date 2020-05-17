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

class ReturnClauseMgrTest extends TestCase
{

    /**
     * @return array
     */
    public function returnClauseMgrTest1DataProvider() {
        $testData = [];

        $testData[] = [
            //  6x    * null class + bool
            1,
            null,
            null,
            null,
            '        return;'
        ];

        $testData[] = [
            //  6x    * null class + bool
            11,
            null,
            true,
            null,
            '        return true;'
        ];

        $testData[] = [
            //  6x    * null class + bool
            12,
            null,
            false,
            null,
            '        return false;'
        ];

        $testData[] = [
            //  6x    * null class + int
            13,
            null,
            1.3,
            null,
            '        return 1.3;'
        ];

        $testData[] = [
            //  6x    * null class + int
            14,
            null,
            14,
            null,
            '        return 14;'
        ];

        $testData[] = [
            //  6x    * null class + int as string
            142,
            null,
            '008',
            null,
            '        return 008;'
        ];

        $testData[] = [
            //  6x    * null class + string
            15,
            null,
            'string15',
            null,
            '        return "string15";'
        ];

        $testData[] = [
            //  6x    * null class + string
            16,
            null,
            'CONSTANT16',
            null,
            '        return "CONSTANT16";'
        ];

        $testData[] = [
            //  6x    * null class +  $-prefixed string ie variable
            17,
            null,
            '$var17',
            null,
            '        return $var17;'
        ];

        $testData[] = [
            //  6x    * null class + $-prefixed string (with subjectIndex) ie variable
            18,
            null,
            '$var18',
            0,
            '        return $var18[0];'
        ];

        $testData[] = [
            //  6x    * null class + $-prefixed string (with subjectIndex) ie variable
            19,
            null,
            '$var19',
            19,
            '        return $var19[19];'
        ];

        $testData[] = [
            //  6x    * null class + $-prefixed string (with subjectIndex) ie variable
            20,
            null,
            '$var20',
            'pos20',
            '        return $var20[$pos20];'
        ];


        $testData[] = [
            //  4x self class + string (constant)
            21,
            ReturnClauseMgr::SELF_KW,
            'CONSTANT21',
            null,
            '        return self::$CONSTANT21;'
        ];

        $testData[] = [
            //  4x self class +  string (constant), (with subjectIndex)
            22,
            ReturnClauseMgr::SELF_KW,
            'CONSTANT22',
            0,
            '        return self::$CONSTANT22[0];'
        ];

        $testData[] = [
            //  4x self class + $-prefixed string
            23,
            ReturnClauseMgr::SELF_KW,
            '$var23',
            null,
            '        return self::$var23;'
        ];

        $testData[] = [
            //  4x self class + $-prefixed string (with subjectIndex)
            24,
            ReturnClauseMgr::SELF_KW,
            '$var24',
            '$index24',
            '        return self::$var24[$index24];'
        ];

        $testData[] = [
            // 2x this                ->       string (property, opt with subjectIndex)
            31,
            ReturnClauseMgr::THIS_KW,
            'string31',
            null,
            '        return $this->string31;'
        ];

        $testData[] = [
            // 2x this                ->       string (property, opt with subjectIndex)
            32,
            ReturnClauseMgr::THIS_KW,
            'string32',
            0,
            '        return $this->string32[0];'
        ];

        $testData[] = [
            // 2x this                ->       string (property, opt with subjectIndex)
            33,
            ReturnClauseMgr::THIS_KW,
            'string33',
            'pos33',
            '        return $this->string33[$pos33];'
        ];
/*
        $testData[] = [
            // 2x this                ->       predefind this->method( arg(s) )
            41,
            ReturnClauseMgr::THIS_KW,
            'method41()',
            null,
            '        return $this->method41();'
        ];
*/
        $testData[] = [
            // 1x this                         none
            51,
            ReturnClauseMgr::THIS_KW,
            null,
            null,
            '        return $this;'
        ];

        $testData[] = [
            // 3x otherClass (fqcn)   ::       string (constant)
            61,
            ReturnClauseMgr::class,
            'CONSTANT61',
            null,
            '        return ' . ReturnClauseMgr::class . '::$CONSTANT61;'
        ];

        $testData[] = [
            // 3x otherClass (fqcn)   ::       string (constant), with subjectIndex
            62,
            ReturnClauseMgr::class,
            'CONSTANT62',
            62,
            '        return ' . ReturnClauseMgr::class . '::$CONSTANT62[62];'
        ];

        $testData[] = [
            // 3x otherClass (fqcn)   ::       string (constant), with subjectIndex
            63,
            ReturnClauseMgr::class,
            'CONSTANT63',
            'pos63',
            '        return ' . ReturnClauseMgr::class . '::$CONSTANT63[$pos63];'
        ];

        $testData[] = [
            // 3x $class
            71,
            '$class71',
            null,
            null,
            '        return $class71;'
        ];

        $testData[] = [
            // 3x $class :: class with public property
            72,
            '$class72',
            '$class72',
            null,
            '        return $class72->class72;'
        ];

        $testData[] = [
            // 3x $class ::  string (constant)
            73,
            '$class73',
            'CONSTANT73',
            null,
            '        return $class73->CONSTANT73;'
        ];

        $testData[] = [
            // 3x $class  ::  string (constant) with index
            74,
            '$class74',
            'CONSTANT74',
            'seventyfour',
            '        return $class74->CONSTANT74[$seventyfour];'
        ];

        $testData[] = [
            // 3x $class  :: (public) property
            75,
            '$class75',
            '$property75',
            null,
            '        return $class75->property75;'
        ];

        $testData[] = [
            // 3x $class  :: (public) property with subjectIndex
            76,
            '$class76',
            '$property76',
            '76',
            '        return $class76->property76[76];'
        ];

        $testData[] = [
            // 3x $class  :: (public) property with subjectIndex
            77,
            '$class77',
            '$property77',
            'sevenSeven',
            '        return $class77->property77[$sevenSeven];'
        ];

        return $testData;
    }

    /**
     * Testing ReturnClauseMgr
     *
     * @test
     * @dataProvider returnClauseMgrTest1DataProvider
     *
     * class         (scope)  variable (type)
     * null                         bool, int, string, $-prefixed string (opt with subjectIndex) ie variable
     * self                ::       string (constant), $-prefixed string (opt with subjectIndex) class (static) variable
     * this                ->       string (property, opt with subjectIndex)
     * this                ->       predefind this->method( arg(s) )
     * this                         none
     * otherClass (fqcn)   ::       string (constant), $-prefixed string (opt with subjectIndex) class (static) variable
     * $class              ::       string (constant), $-prefixed string (opt with subjectIndex) class (static) variable
     * $class              ->       string (opt with subjectIndex), NOT accepted here (class with public property)
     *
     * @param int        $case
     * @param string     $prefix
     * @param mixed      $subject
     * @param int|string $index
     * @param string     $expected
     */
    public function returnClauseMgrTest1( $case, $prefix = null, $subject = null, $index = null, $expected = null ) {
        $rcm = ReturnClauseMgr::init( PHP_EOL, '    ' )->setBaseIndent( '    ' );
        switch( true ) {
            case ( false !== strpos( $subject, 'CONSTANT' )) :
                $rcm->setSourceIsConst( true )
                    ->setSource( $prefix, $subject, $index );
                $case .= '-1';
                break;
            case ( is_bool( $subject ) || is_int( $subject ) || is_float( $subject ) ||
                ( false !== strpos( $expected, 'return \'' ))) :
                if( 1 == array_rand( [ 1, 2 ] )) {
                    $rcm->setFixedSourceValue( $subject );
                    $case .= '-2';
                    break;
                }
                $rcm = ReturnClauseMgr::factory( $prefix, $subject, $index );
                $case .= '-3';
                break;
            case (( ReturnClauseMgr::THIS_KW == $prefix ) &&
                is_string( $subject ) && ! empty( $index )) :
                $subject = Util::setVarPrefix( $subject );
                $rcm->setThisPropertySource( $subject, $index );
                $case .= '-4';
                break;
            case ( empty( $prefix ) && Util::isVarPrefixed( $subject )) :
                $subject = Util::unSetVarPrefix( $subject );
                $rcm->setVariableSource( $subject, $index );
                $case .= '-5';
                break;
            case Util::isVarPrefixed( $subject ) :
                if( 1 == array_rand( [ 1, 2 ] )) {
                    $rcm->setSource( $prefix, $subject, $index );
                    $case .= '-6';
                    break;
                }
                $rcm->setSource( EntityMgr::factory( $prefix, $subject, $index ));
                $case .= '-7';
                break;
            default :
                $rcm = ReturnClauseMgr::factory( $prefix, $subject, $index );
                $case .= '-8';
                break;
        }
        $code = $rcm->toString();
        $this->assertEquals(
            $expected . PHP_EOL,
            $code,
            __FUNCTION__ . ' ' . $case . '-A ' .
            'expected : ' . trim( $expected ) . ' , actual : ' . trim( $code ) .
            ', class : ' . var_export( $prefix, true ) .
            ', variable : ' . var_export( $subject, true ) .
            ', index : ' . var_export( $index, true )
        );
        $this->assertEquals(
            [ $expected ],
            $rcm->toArray(),
            __FUNCTION__ . ' ' . $case . '-A ' .
            'expected : ' . trim( $expected ) . ' , actual : ' . trim( $code ) .
            ', class : ' . var_export( $prefix, true ) .
            ', variable : ' . var_export( $subject, true ) .
            ', index : ' . var_export( $index, true )
        );

        if( DISPLAYrcm ) {
            echo __FUNCTION__ . ' ' . $case . ' ' . $code;
        }
    }

    /**
     * @return array
     */
    public function returnClauseMgrTest2DataProvider() {
        $testData = [];

        $testData[] = [
            // 3x $class  :: (public static) property
            75,
            '$class75',
            '$property75',
            null,
            '        return $class75::$property75;'
        ];

        $testData[] = [
            // 3x $class  :: (public static) property with subjectIndex
            76,
            '$class76',
            '$property76',
            '76',
            '        return $class76::$property76[76];'
        ];

        $testData[] = [
            // 3x $class  :: (public static) property with subjectIndex
            77,
            '$class77',
            '$property77',
            'sevenSeven',
            '        return $class77::$property77[$sevenSeven];'
        ];
        return $testData;
    }

    /**
     * Testing ReturnClauseMgr, static $class properties
     *
     * @test
     * @dataProvider returnClauseMgrTest2DataProvider
     *
     * @param      $case
     * @param null $prefix
     * @param null $subject
     * @param null $index
     * @param null $expected
     */
    public function returnClauseMgrTest2( $case, $prefix = null, $subject = null, $index = null, $expected = null ) {
        $rcm = ReturnClauseMgr::factory( $prefix, $subject, $index )
                              ->setSourceIsStatic();

        $code = $rcm->toString();
        $this->assertTrue( $rcm->isSourceStatic());
        $this->assertEquals(
            $expected . PHP_EOL,
            $code,
            $case . '-A expected : ' . trim( $expected ) . ' , actual : ' . trim( $code ) .
            ', class : ' . var_export( $prefix, true ) .
            ', variable : ' . var_export( $subject, true ) .
            ', index : ' . var_export( $index, true )
        );
        $this->assertEquals(
            [ $expected ],
            $rcm->toArray(),
            $case . '-B expected : ' . trim( $expected ) . ' , actual : ' . trim( $code ) .
            ', class : ' . var_export( $prefix, true ) .
            ', variable : ' . var_export( $subject, true ) .
            ', index : ' . var_export( $index, true )
        );
        if( DISPLAYrcm ) {
            echo __FUNCTION__ . ' ' . $case . ' ' . $code;
        }
    }

    /**
     * @test
     */
    public function returnClauseMgrTest4() {
        $this->assertEquals( '        return;' , ReturnClauseMgr::factory()->toArray()[0] );
    }

    /**
     * @test
     */
    public function returnClauseMgrTest5() {
        $rcm = ReturnClauseMgr::init();
        $this->assertEquals( '[]', $rcm->setFixedSourceValue( '[]' )->getFixedSourceValue());
    }

    /**
     * @test
     */
    public function returnClauseMgrTest6() {
        $rcm = ReturnClauseMgr::init();
        try {
            $rcm->setSourceExpression( 123 );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }

        $expression = 'array_rand( [ 1,2 ] )';
        $this->assertEquals( $expression, $rcm->setSourceExpression( $expression )->getFixedSourceValue());
    }

    /**
     * @test
     */
    public function returnClauseMgrTest7() {
        try {
            ReturnClauseMgr::init()->toArray();
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }

        try {
            ReturnClauseMgr::init()->setSource( null, '' );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
        try {
            ReturnClauseMgr::init()->setSource( null, [] );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
        try {
            ReturnClauseMgr::init()->setSource( null, null, '[]' );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }

        try {
            ReturnClauseMgr::init()
                ->setThisPropertySource( false );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }

        try {
            ReturnClauseMgr::init()
                ->setVariableSource( false );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }

        try {
            ReturnClauseMgr::init()
                ->setFixedSourceValue( null );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }

    }
}
