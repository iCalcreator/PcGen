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
use Kigkonsult\PcGen\Dto\VarDto;
use PHPUnit\Framework\TestCase;

class ClassMgrTest extends TestCase
{

    private static $namespace  = 'AcmeCorp';
    private static $use1       = 'Some\Name\Space\SomeClass';
    private static $alias1     = 'alias1';
    private static $use2       = 'Another\Name\Space\AnotherClass';
    private static $className  = 'HalloWorld';
    private static $extends    = 'alias1';
    private static $interface1 = 'Interface1';
    private static $interface2 = 'Interface2';

    private static $prop       = 'prop';

    public function classMgrTest1DataProvider() {
            static $SUMMARY     = ' summary ';
            static $DESCRIPTION = ' description ';
            $testData = [];

            $testData[] = [
                1,
                [   // property set start
                    //     PropertyMgr
                    //         constant
                    PropertyMgr::factory(
                        VarDto::factory(
                            self::$prop . 1,
                            ClassMgr::STRING_T,
                            '' . self::$prop . '1',
                            1 . $SUMMARY . ClassMgr::CONST_ . 'ant',
                            1 . $DESCRIPTION . ClassMgr::CONST_ . 'ant'
                        )
                    )
                        ->setIsConst(),

                    //     PropertyMgr
                    //         static, int, set with getter/setter BUT NO getter/setter out
                    PropertyMgr::factory(
                        self::$prop . 2,
                        ClassMgr::INT_T,
                        null,
                        2 . $SUMMARY . ClassMgr::STATIC_KW,
                        2 . $DESCRIPTION . ClassMgr::STATIC_KW . ' NO getter/setter/argInFactory'
                    )
                        ->setStatic()
                        ->setVisibility( ClassMgr::PROTECTED_ )
                        ->setMakeSetter( true )
                        ->setMakeGetter( true )
                        ->setArgInFactory( true ),

                //     PropertyMgr
                //         static, int, without getter/setter
                    PropertyMgr::factory(
                        VarDto::factory(
                        self::$prop . 3,
                        ClassMgr::INT_T,
                        3,
                        3 . $SUMMARY . ClassMgr::STATIC_KW,
                        3 . $DESCRIPTION . ClassMgr::STATIC_KW . ' NO getter/setter/argInFactory'
                        )
                    )
                        ->setStatic( true )
                        ->setVisibility( ClassMgr::PROTECTED_ ),

                //     PropertyMgr
                //         'public' : float : with getter/setter AND argInfactory
                    PropertyMgr::factory(
                        self::$prop . 4,
                        ClassMgr::FLOAT_T,
                        PropertyMgr::NULL_T,
                        4 . $SUMMARY . ClassMgr::FLOAT_T,
                        4 . $DESCRIPTION . ClassMgr::FLOAT_T . ' WITH getter/setter/argInFactory'
                    )
                        ->setMakeSetter( true )
                        ->setMakeGetter( true )
                        ->setArgInFactory( true ),

                //     PropertyMgr
                //         'public' : float : with getter/setter AND argInfactory
                    PropertyMgr::factory(
                        VarDto::factory(
                            self::$prop . 5,
                            ClassMgr::FLOAT_T,
                            5.555,
                            5 . $SUMMARY . ClassMgr::FLOAT_T,
                            5 . $DESCRIPTION . ClassMgr::FLOAT_T . ' WITH getter/setter/argInFactory'
                        )
                    )
                        ->setMakeGetter( true )
                        ->setMakeSetter( true )
                        ->setArgInFactory( true ),

                //     PropertyMgr
                //         'public' : array : with getter/setter AND argInfactory
                    PropertyMgr::factory(
                        self::$prop . 6,
                        ClassMgr::ARRAY_T,
                        [],
                        6 . $SUMMARY . ClassMgr::ARRAY_T,
                        6 . $DESCRIPTION . ClassMgr::ARRAY_T . ' WITH getter/setter/argInFactory'
                    )
                        ->setArgInFactory( true ),

                //     PropertyMgr
                //         'public' : array : without getter/setter and argInfactory
                    //     same array test as in fcnFrameMgrTest1, case 614
                    PropertyMgr::factory(
                        VarDto::factory(
                            self::$prop . 7,
                            ClassMgr::ARRAY_T,
                            [ true, false, 0, 1, -1, 1.1, -1.1, "value614" ],
                            7 . $SUMMARY . ClassMgr::ARRAY_T,
                            7 . $DESCRIPTION . ClassMgr::ARRAY_T . ' WITH getter/setter/argInFactory'
                        )
                    )
                        ->setArgInFactory( true ),
            ],  // property set end
            [
                [ 1 ],     // constants
                [ 2, 3 ],  // static
                [ 4, 5, 6, 7 ],  // public with methods
                [ ],  // public without methods
                [ 1, 2, 3, 4, 5, 6, 7 ] // all
            ],
        ]; // test set 1 end

        $testData[] = [
            3,
            [   // property set start
                //     3 : VarDto
                //         'public' : float with getter/setter, NO argInFactory
                VarDto::factory(
                    self::$prop . 4,
                    ClassMgr::FLOAT_T,
                    PropertyMgr::NULL_T,
                    4 . $SUMMARY . ClassMgr::FLOAT_T,
                    4 . $DESCRIPTION . ClassMgr::FLOAT_T . ' NO argInFactory'
                ),

                //     3 : VarDto
                //         'public' string[], array : with getter/setter, NO argInFactory
                VarDto::factory(
                    self::$prop . 6,
                    ClassMgr::CALLABLEARRAY_T,
                    null,
                    6 . $SUMMARY . ClassMgr::CALLABLEARRAY_T,
                    6 . $DESCRIPTION . ClassMgr::CALLABLEARRAY_T . ' NO argInFactory'
                )
            ],
            [
                [],     // constants
                [],  // static
                [ 4, 6 ],  // public with methods
                [],  // public without methods
                [ 4, 6 ] // all
            ],
        ]; // test set 3 end

        $testData[] = [
            4,
            [   // property set start
                //     4 : variable
                //         'public' : ???? : with getter/setter
                self::$prop . 4,

                //     4 : VarDto
                //         'public' : ????  : with getter/setter
                self::$prop . 6,
            ],
            [
                [],     // constants
                [],  // static
                [ 4, 6 ],  // public with methods
                [],  // public without methods
                [ 4, 6 ] // all
            ],
        ]; // test set 4 end

        $testData[] = [
            6,
            [   // property set start
                //     6 :_ array( VarDto, getter, setter )
                //         'public' : float,  : with getter/setter (default)
                [
                    VarDto::factory(
                        self::$prop . 4,
                        ClassMgr::FLOAT_T,
                        PropertyMgr::NULL_T,
                        4 . $SUMMARY . ClassMgr::FLOAT_T,
                        4 . $DESCRIPTION . ClassMgr::FLOAT_T
                    )

                ],
                //     6 :_ array( VarDto, getter, setter )
                //         'public'int : with getter/setter
                [
                    VarDto::factory(
                        self::$prop . 5,
                        ClassMgr::FLOAT_T,
                        5.555,
                        5 . $SUMMARY . ClassMgr::FLOAT_T,
                        5 . $DESCRIPTION . ClassMgr::FLOAT_T
                    ),
                ],
                //     6 :_ array( VarDto, getter, setter )
                //         'public' array : without getter/setter
                [
                    VarDto::factory(
                        self::$prop . 6,
                        ClassMgr::ARRAY_T,
                        [],
                        6 . $SUMMARY . ClassMgr::ARRAY_T,
                        6 . $DESCRIPTION . ClassMgr::ARRAY_T
                    ),
                ],
                //     6 :_ array( VarDto, getter, setter )
                //         'public' : array : without getter/setter
                    PropertyMgr::factory(
                        VarDto::factory(
                            self::$prop . 7,
                            ClassMgr::ARRAY_T,
                            [ 1, 2 ],
                            7 . $SUMMARY . ClassMgr::ARRAY_T,
                            7 . $DESCRIPTION . ClassMgr::ARRAY_T
                        )
                    )
                        ->setMakeGetter( false )
                        ->setMakeSetter( false )
                        ->setArgInFactory( true ),
            ],
            [
                [],     // constants
                [],  // static
                [ 4, 5, 6 ],  // public with methods
                [ 7 ],  // public without methods
                [ 4, 5, 6, 7 ] // all
            ],
        ]; // test set 6 end


        $testData[] = [
            7,
            [   // property set start
                //     7 : array( variable, varType, default, summary, description, getter, setter )
                //         'public' : string, int, float, array : with/without getter/setter
                //     7 : array( variable, varType, default, summary, description, getter, setter )
                //         'public' : float,  : with getter/setter (default)
                [
                    self::$prop . 4,
                    ClassMgr::FLOAT_T,
                    PropertyMgr::NULL_T,
                    4 . $SUMMARY . ClassMgr::FLOAT_T,
                    4 . $DESCRIPTION . ClassMgr::FLOAT_T,
                ],
            ],
            [
                [],        // constants
                [],        // static
                [ 4 ],     // public with methods
                [  ],      // public without methods
                [ 4 ]      // all
            ],
        ]; // test set 6 end

        return $testData;
    }

    /**
     * @test
     * @dataProvider classMgrTest1DataProvider
     *
     *     1 :PropertyMgr
     *         constant, static, 'public' : string, int, float, array : with/without getter/setter/argInfactory
     *     3 : VarDto
     *         constant, static, 'public' : string, int, float, array : with/without getter/setter/argInfactory
     *     4 : variable
     *         constant, static, 'public' : string, int, float, array : with/without getter/setter/argInfactory
     *     6 :_ array( VarDto, getter, setter )
     *         constant, static, 'public' : string, int, float, array : with/without getter/setter/argInfactory
     *     7 : array( variable, varType, default, summary, description, getter, setter )
     *         constant, static, 'public' : string, int, float, array : with/without getter/setter/argInfactory
     *
     * @param int   $case
     * @param array $properties
     * @param array $expected
     */
    public function classMgrTest1( $case, array $properties, array $expected ) {
        $case += 10;

        $cm = ClassMgr::init( PHP_EOL, '    ' )
            ->setInterface()
            ->setTrait()
            ->setClass()
            ->setNamespace( self::$namespace . $case )
            ->setUses(
                [
                    [ self::$use1, self::$alias1 ],
                    [ self::$use2 ]
                ]
            )
            ->setName( self::$className . $case )
            ->setExtend( self::$extends )
            ->setImplements(
                [
                    self::$interface1,
                    self::$interface2
                ]
            )
            ->setConstruct( true )
            ->setFactory( true )
            ->setBody(
                ' /* body row 1 */',
                [
                    ' /* body row 2 */',
                    ' /* body row 3 */',
                    ' /* body row 4 */',
                ]
            );

        $this->classMgrTest1Tester( $case, $cm->setProperties( $properties )->toString(), $expected );
    }

    /**
     * @test
     * @dataProvider classMgrTest1DataProvider
     *
     *     same testdata as classMgrTest1 but testing addProperty
     *
     * @param int   $case
     * @param array $properties
     * @param array $expected
     */
    public function classMgrTest2( $case, array $properties, array $expected ) {
        $case += 20;
        $cm = ClassMgr::init()
            ->setInterface()
            ->setTrait()
            ->setClass()
            ->setNamespace( self::$namespace . $case )
            ->setName( self::$className . $case )
            ->setExtend( self::$extends )
            ->setImplements(
                [
                    self::$interface1,
                    self::$interface2
                ]
            )
            ->setConstruct( true )
            ->setFactory( true )
            ->setBody(
                ' /* body row 1 */',
                [
                    ' /* body row 2 */',
                    ' /* body row 3 */',
                    ' /* body row 4 */',
                ]
            );
        $cm->addUse( self::$use1, self::$alias1 );
        $cm->addUse( self::$use1, self::$alias1 ); // dupls on fcqn+alias
        $cm->AddUse( self::$use2 );
        $cm->addUse( self::$use2 );             // no dupls
        $cm->addImplement( self::$interface2 ); // no dupls

        foreach( $properties as $property ) {
            $cm->addProperty( $property );
        }
        $this->classMgrTest1Tester( $case, $cm->toString(), $expected );
    }

    /**
     * @param int    $case
     * @param string $code
     * @param array  $expected
     */
    public function classMgrTest1Tester( $case, $code, $expected ) {
        $this->assertTrue(
            is_string( $code )
        );
        $this->assertTrue(
            ( false !== strpos( $code, 'namespace ' . self::$namespace . $case . ';' )),
            'Error in case ' . 'A-' . $case . PHP_EOL . $code
        );

        $this->assertTrue(
            ( false !== strpos( $code, self::$use1 . ' as ' . self::$alias1 . ';' )),
            'Error in case ' . 'B-' . $case . PHP_EOL . $code
        );
        $this->assertTrue(
            ( false !== strpos( $code, self::$use2 . ';' )),
            'Error in case ' . 'C-' . $case . PHP_EOL . $code
        );

        $this->assertTrue(
            ( false !== strpos( $code, ' * Class ' . self::$className . $case )),
            'Error in case ' . 'D-' . $case . PHP_EOL . $code
        );
        $this->assertTrue(
            ( false !== strpos( $code, ' * @package ' . self::$namespace . $case )),
            'Error in case ' . 'E-' . $case . PHP_EOL . $code
        );
        $this->assertTrue(
            ( false !== strpos( $code, 'class ' . self::$className . $case )),
            'Error in case ' . 'F-' . $case . PHP_EOL . $code
        );

        $this->assertTrue(
            ( false !== strpos( $code, '    extends ' . self::$extends )),
            'Error in case ' . 'G-' . $case . PHP_EOL . $code
        );

        $this->assertTrue(
            ( false !== strpos( $code, '    implements ' . self::$interface1 )),
            'Error in case ' . 'H-' . $case . PHP_EOL . $code
        );
        $this->assertTrue(
            ( false !== strpos( $code, self::$interface2 )),
            'Error in case ' . 'I-' . $case . PHP_EOL . $code
        );

        $this->assertTrue(
            ( false !== strpos( $code, ClassMgr::CONST_ )),
            'Error in case ' . 'J-' . $case . PHP_EOL . $code
        );

        $this->assertTrue(
            ( false !== strpos( $code, '    public function __construct() ' )),
            'Error in case ' . 'K-' . $case . 'K' . PHP_EOL . $code
        );

        $this->assertTrue(
            ( false !== strpos( $code, '    public static function factory(' )),
            'Error in case ' . 'L-' . $case . PHP_EOL . $code
        );

        foreach( $expected[4] as $expNo ) {  // test all
            $this->assertTrue(
                ( false !== stripos( $code, 'prop' . $expNo )),
                'Error in case ' . 'M-' . $case . '-' . $expNo . PHP_EOL . $code
            );
        }

        foreach( $expected[0] as $expNo ) {  // test constants
            $this->assertTrue(
                ( false !== strpos( $code, 'const PROP' . $expNo . ' = "prop' . $expNo . '"' )),
                'Error in case ' . 'P1-' . $case . '-' . $expNo . PHP_EOL . $code
            );
            $this->assertTrue(
                ( false === strpos( $code, '    public function getProp' . $expNo . '() {' )),
                'Error in case ' . 'P2-' . $case . '-' . $expNo . PHP_EOL . $code
            );
            $this->assertTrue(
                ( false === strpos( $code, '    public function setProp' . $expNo . '(' )),
                'Error in case ' . 'P3-' . $case . '-' . $expNo . PHP_EOL . $code
            );
        }

        foreach( $expected[1] as $expNo ) {  // test static
            $this->assertTrue(
                ( false !== strpos( $code, ' protected static $prop' . $expNo . ' =' )),
                'Error in case ' . 'Q1-' . $case . '-' . $expNo . PHP_EOL . $code
            );
            $this->assertTrue(
                ( false === strpos( $code, '    public function getProp' . $expNo . '() {' )),
                'Error in case ' . 'Q2-' . $case . '-' . $expNo . PHP_EOL . $code
            );
            $this->assertTrue(
                ( false === strpos( $code, '    public function setProp' . $expNo . '(' )),
                'Error in case ' . 'Q3-' . $case . '-' . $expNo . PHP_EOL . $code
            );
        }

        foreach( $expected[2] as $expNo ) {  // private property and public methods
            $this->assertTrue(
                ( false !== strpos( $code, '    private $prop' . $expNo . ' =' )),
                'Error in case ' . 'R1-' . $case . '-' . $expNo . PHP_EOL . $code
            );
            $this->assertTrue(
                ( false !== strpos( $code, '    public function getProp' . $expNo . '() ' )),
                'Error in case ' . 'R2-' . $case . '-' . $expNo . PHP_EOL . $code
            );
            $this->assertTrue(
                ( false !== strpos( $code, '    public function setProp' . $expNo . '(' )),
                'Error in case ' . 'R3-' . $case . '-' . $expNo . PHP_EOL . $code
            );
        }

        foreach( $expected[3] as $expNo ) {  // private property without methods
            $this->assertTrue(
                ( false !== strpos( $code, '    private $prop' . $expNo . ' =' )),
                'Error in case ' . 'S1-' . $case . '-' . $expNo . PHP_EOL . $code
            );
            $this->assertTrue(
                ( false === strpos( $code, '    public function getProp' . $expNo . '() ' )),
                'Error in case ' . 'S2-' . $case . '-' . $expNo . PHP_EOL . $code
            );
            $this->assertTrue(
                ( false === strpos( $code, '    public function setProp' . $expNo . '(' )),
                'Error in case ' . 'S3-' . $case . '-' . $expNo . PHP_EOL . $code
            );
        }

        if( DISPLAYcm ) {
            echo __FUNCTION__ . ' ' . $case . ' : ' . PHP_EOL . $code . PHP_EOL;
        }

    }

    /**
     * Testing factory, arguments with and without set-methods
     *
     * @test
     */
    public function classMgrTest11() {
        $classMgr = ClassMgr::init()
            ->setName( 'case11' )
            ->setFactory( true )
            ->addProperty( VarDto::factory( 'without1' ), false, false, true )
            ->addProperty( VarDto::factory( 'with2' ), false, true, true )
            ->addProperty( VarDto::factory( 'without3' ), false, false, true )
            ->addProperty( VarDto::factory( 'with4' ), false, true, true );
        $code = $classMgr->toString();
        for( $testX = 0; $testX < 2; $testX++ ) {
            $this->assertTrue(
                ( false !== strpos( $code, '        $instance->without1 = $without1;' ) ),
                'Error in case ' . __FUNCTION__ . '-' . 1 . PHP_EOL . $code
            );
            $this->assertTrue(
                ( false !== strpos( $code, '        $instance->setWith2( $with2 );' ) ),
                'Error in case ' . __FUNCTION__ . '-' . 4 . PHP_EOL . $code
            );
            $this->assertTrue(
                ( false !== strpos( $code, '        $instance->without3 = $without3;' ) ),
                'Error in case ' . __FUNCTION__ . '-' . 3 . PHP_EOL . $code
            );
            $this->assertTrue(
                ( false !== strpos( $code, '        $instance->setWith4( $with4 );' ) ),
                'Error in case ' . __FUNCTION__ . '-' . 4 . PHP_EOL . $code
            );
            if( 1 == $testX ) {
                $classMgr->addProperty( VarDto::factory( 'without5' ), false, false, true );
                $code = $classMgr->toString();
                $this->assertTrue(
                    ( false !== strpos( $code, '        $instance->without5 = $without5;' ) ),
                    'Error in case ' . __FUNCTION__ . '-' . 3 . PHP_EOL . $code
                );
            }
            else {
                if( DISPLAYcm ) {
                    echo __FUNCTION__ . ' : ' . PHP_EOL . $code . PHP_EOL;
                }
            }
        } // end for
    }

    /**
     * Testing factory without arguments
     *
     * @test
     */
    public function classMgrTest12() {
        $code = ClassMgr::init()
            ->setName( 'case12' )
            ->setFactory( true )
            ->toString();

        $this->assertTrue(
            ( false !== strpos( $code, '        return new static();')),
            'Error in case ' . __FUNCTION__ . '-' . 1 . PHP_EOL . $code
        );
        if( DISPLAYcm ) {
            echo __FUNCTION__ . ' : ' . PHP_EOL . $code . PHP_EOL;
        }
    }

    /**
     * Testing Iterator
     *
     * @test
     */
    public function classMgrTest301() {
        $case = 301;

        $pm = PropertyMgr::factory(
            self::$prop . $case,
            ClassMgr::ARRAY_T
        )
            ->setMakeGetter( true )
            ->setMakeSetter( true )
            ->setArgInFactory( true );

        // ClassMgr::setTargetPhpVersion( '5.6.0' ); // test ###

        $cm = ClassMgr::init()
            ->setNamespace( self::$namespace . $case )
            ->setName( self::$className . $case )
            ->addProperty( $pm )
            ->setFactory();

        $code = $cm->toString();

        foreach( ClassMethodFactory::$USES as $use ) {
            $this->assertTrue(
                ( false !== strpos( $code, 'use ' . $use . ';' ) ),
                'Error 1 in case ' . $case . PHP_EOL . $code
            );
        }
        foreach( ClassMethodFactory::$IMPLEMENTS as $implement ) {
            $this->assertTrue(
                ( 1 < substr_count( $code, $implement )),
                'Error 1 in case ' . $case . PHP_EOL . $code
            );
        }
        $this->assertTrue(
            ( false !== stripos( $code, 'private $prop' . $case )),
            'Error 3 in case ' . $case . PHP_EOL . $code
        );
        $this->assertTrue(
            ( false !== stripos( $code, 'private $position' )),
            'Error 4 in case ' . $case . PHP_EOL . $code
        );

        $this->assertTrue(
            ( false !== stripos( $code, 'public function current() ' )),
            'Error 5 in case ' . $case . PHP_EOL . $code
        );
        $this->assertTrue(
            ( false !== stripos( $code, 'public function getIterator() ' )),
            'Error 6 in case ' . $case . PHP_EOL . $code
        );
        $this->assertTrue(
            ( false !== stripos( $code, 'public function key() ' )),
            'Error 7 in case ' . $case . PHP_EOL . $code
        );
        $this->assertTrue(
            ( false !== stripos( $code, 'public function last() ' )),
            'Error 8 in case ' . $case . PHP_EOL . $code
        );
        $this->assertTrue(
            ( false !== stripos( $code, 'public function next() ' )),
            'Error 9 in case ' . $case . PHP_EOL . $code
        );
        $this->assertTrue(
            ( false !== stripos( $code, 'public function previous() ' )),
            'Error 10 in case ' . $case . PHP_EOL . $code
        );
        $this->assertTrue(
            ( false !== stripos( $code, 'public function rewind() ' )),
            'Error 11 in case ' . $case . PHP_EOL . $code
        );
        $this->assertTrue(
            ( false !== stripos( $code, 'public function valid() ' )),
            'Error 12 in case ' . $case . PHP_EOL . $code
        );

        if( DISPLAYcm ) {
            echo __FUNCTION__ . ' ' . $case . ' : ' . PHP_EOL . $code . PHP_EOL;
        }
    }

    /**
     * @test
     */
    public function classMgrTest101() {
        try {
            $cm = classMgr::init()->toArray();
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * @test
     */
    public function classMgrTest130() {
        $this->assertTrue( classMgr::init()->setAbstract( true )->isAbstract());

        $this->assertTrue(
            classMgr::init()->setDocBlock( DocBlockMgr::init())->getDocBlock()
            instanceof
            DocBlockMgr
        );

        $extends = __CLASS__;
        $cm      = classMgr::init()->setExtend( $extends );
        $this->assertTrue( $cm->isExtendsSet());
        $this->assertEquals( $extends, $cm->getExtend());
    }

    /**
     * @test
     */
    public function classMgrTest132() {
        try {
            $cm = classMgr::init()->setImplements( [] );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
        try {
            $cm = classMgr::init()->setImplements( [ false ] );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * @test
     */
    public function classMgrTest151() {
        try {
            $cm = classMgr::init()->addProperty( false );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * @test
     */
    public function classMgrTest156() {
        try {
            $cm = classMgr::init()->setProperties( false );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
        try {
            $cm = classMgr::init()->setProperties( true );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * @test
     */
    public function classMgrTest171() {
        try {
            $cm = classMgr::init()->setUses( false );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * @test
     */
    public function classMgrTest172() {
        try {
            $cm = classMgr::init()->setUses( [ false ] );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * @test
     */
    public function propertyMgrTest101() {
        try {
            $pm = propertyMgr::factory( true );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

}

