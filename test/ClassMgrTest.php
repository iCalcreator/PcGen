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

    private static $useGroup   = [
        [ 'fcnA',                 null,      ClassMgr::FUNC_ ],
        [ 'fcnB',                 'bAlias',  ClassMgr::FUNC_ ],
        [ 'fcnC',                 null,      ClassMgr::FUNC_ ],
        [ 'fcnD',                 'dAlias',  ClassMgr::FUNC_ ],
        [ 'E' . ClassMgr::CONST_, null,      ClassMgr::CONST_ ],
        [ 'F' . ClassMgr::CONST_, 'FAlias',  ClassMgr::CONST_ ],
        [ 'Acme\Aclass',          'AAclass', null ],
    ];

    public function classMgrTest1DataProvider() {
        static $SUMMARY     = ' summary ';
        static $DESCRIPTION = ' description ';
        $testData = [];

        $testData[] = [
            1,
            [   // property set start
                //     PropertyMgr
                //         constant
                1  => PropertyMgr::factory(
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
                2  => PropertyMgr::factory(
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

                //     VariableMgr
                //         static, int, without getter/setter
                3  => VariableMgr::factory(
                    VarDto::factory(
                        self::$prop . 3,
                        ClassMgr::INT_T,
                        3,
                        3 . $SUMMARY . ClassMgr::STATIC_KW,
                        3 . $DESCRIPTION . ClassMgr::STATIC_KW .
                        ' NO getter/setter/argInFactory' .
                        ', is static and has protected visibility' .
                        ', origins from a VariableMgr instance'
                    )
                )
                    ->setStatic( true )
                    ->setVisibility( ClassMgr::PROTECTED_ ),

                //     PropertyMgr
                //         'public' : float : with getter/setter AND argInfactory
                4  => PropertyMgr::factory(
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
                5  => PropertyMgr::factory(
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
                6  => PropertyMgr::factory(
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
                7  => PropertyMgr::factory(
                    VarDto::factory(
                        self::$prop . 7,
                        ClassMgr::ARRAY_T,
                        [true, false, 0, 1, -1, 1.1, -1.1, "value614"],
                        7 . $SUMMARY . ClassMgr::ARRAY_T,
                        7 . $DESCRIPTION . ClassMgr::ARRAY_T . ' WITH getter/setter/argInFactory'
                    )
                )
                    ->setArgInFactory( true ),
                //         constant
                8  => PropertyMgr::factory(
                    VarDto::factory(
                        self::$prop . 8,
                        ClassMgr::STRING_T,
                        '' . self::$prop . '8',
                        8 . $SUMMARY . ClassMgr::CONST_ . 'ant',
                        8 . $DESCRIPTION . ClassMgr::CONST_ . 'ant'
                    )
                )
                    ->setIsConst(),
                9  => PropertyMgr::factory(
                    VarDto::factory(
                        self::$prop . 9,
                        ClassMgr::BOOL_T,
                        true,
                        9 . $SUMMARY . ' is type bool, no >isPropertySet> method'
                    )
                ),
                10 => PropertyMgr::factory(
                    VarDto::factory(
                        'isCorrect',
                        ClassMgr::BOOL_T,
                        'true',
                        10 . $SUMMARY . ' is type bool, default bool (string) true'
                    )
                )
                    ->setArgInFactory( true ),
                11 => PropertyMgr::factory(
                    VarDto::factory(
                        'isInCorrect',
                        ClassMgr::BOOLEAN_T,
                        'false',
                        11 . $SUMMARY . ' is type boolean, default bool (string) false'
                    )
                )
                    ->setArgInFactory( true ),
                12 => PropertyMgr::factory(
                    VarDto::factory(
                        self::$prop . 12,
                        ClassMgr::MIXED_KW,
                        true,
                        12 . $SUMMARY . ' is type mixed, default true, has >isPropertySet> method'
                    )
                ),
                13 => // plain array
                    [
                        self::$prop . 13,
                        ClassMgr::INT_T,
                        13,
                        13 . $SUMMARY . self::$prop . 13,
                        13 . $DESCRIPTION . self::$prop . 13 .
                        ' NO getter/setter/argInFactory' .
                        ', origins from a VariableMgr instance',
                        false,
                        false,
                        false,
                    ],
                14 => // plain array
                    [
                        self::$prop . 14,
                        ClassMgr::INT_T,
                        14,
                        14 . $SUMMARY . self::$prop . 14,
                        14 . $DESCRIPTION . self::$prop . 14 .
                        ' NO getter/setter/argInFactory' .
                        ', origins from a VariableMgr instance',
                        false,
                        false,
                        false,
                    ],
                15 => // VarDto
                    varDto::factory(
                        self::$prop . 15,
                        ClassMgr::STRING_T,
                        self::$prop . 15,
                        15 . $SUMMARY . self::$prop . 15,
                        15 . $DESCRIPTION . self::$prop . 15 .
                        ' NO getter/setter/argInFactory' .
                        ', origins from a VarDto instance'
                    ),
                16 => // VariableMgr in array, note, has public class instance property
                    [
                        VariableMgr::factory(
                            self::$prop . 16,
                            ClassMgr::STRING_T,
                            self::$prop . 16,
                            16 . $SUMMARY . self::$prop . 16,
                            16 . $DESCRIPTION . self::$prop . 16 .
                            ' NO getter/setter/argInFactory' .
                            ', origins from a VariableMgr instance in array'
                        ),
                        true,
                        true,
                        true,
                    ],
                17 => // VariableMgr in array, note, has public class instance property
                    [
                        VariableMgr::factory(
                            self::$prop . 17,
                            ClassMgr::MIXED_KW,
                            self::$prop . 17,
                            17 . $SUMMARY . self::$prop . 17,
                            17 . $DESCRIPTION . self::$prop . 17 .
                            ' NO getter/setter/argInFactory' .
                            ', origins from a VariableMgr instance in array'
                        ),
                        true,
                        true,
                        true,
                    ],
            ],  // property set end
            [
                [1, 8 ],                        // constants
                [2, 3 ],                        // static
                [4, 5, 6, 7, 9, 12, 16, 17 ],   // public with methods
                [13, 14, 15 ],                  // props without methods
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 12, 13, 14, 15, 16, 17 ] // all
            ],
        ]; // test set 1 end

        $testData[] = [
            3,
            [   // property set start
                //     3 : VarDto
                //         'public' : float without getter/setter...
                4 => VarDto::factory(
                    self::$prop . 4,
                    ClassMgr::FLOAT_T,
                    PropertyMgr::NULL_T,
                    4 . $SUMMARY . ClassMgr::FLOAT_T,
                    4 . $DESCRIPTION . ClassMgr::FLOAT_T
                ),

                //     3 : VarDto
                //         'public' string[], array : with getter/setter
                6 => VarDto::factory(
                    self::$prop . 6,
                    ClassMgr::CALLABLEARRAY_T,
                    null,
                    6 . $SUMMARY . ClassMgr::CALLABLEARRAY_T,
                    6 . $DESCRIPTION . ClassMgr::CALLABLEARRAY_T
                )
            ],
            [
                [],       // constants
                [],       // static
                [ 4, 6 ], // props with methods
                [],       // props without methods
                [ 4, 6 ]  // all
            ],
        ]; // test set 3 end

        $testData[] = [
            4,
            [   // property set start
                //     4 : variable
                //         'public' : ???? : with getter/setter
                4 => self::$prop . 4,

                //     4 : VarDto
                //         'public' : ????  : with getter/setter
                6 => self::$prop . 6,
            ],
            [
                [],        // constants
                [],        // static
                [ 4, 6 ],  // props with methods
                [],        // props without methods
                [ 4, 6 ]   // all
            ],
        ]; // test set 4 end

        $testData[] = [
            6,
            [   // property set start
                //     6 :_ array( VarDto, getter, setter )
                //         'public' : float,  : with getter/setter (default)
                4 => [
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
                5 => [
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
                6 => [
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
                7 => PropertyMgr::factory(
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
                [ 4, 5, 6 ],  // props with methods
                [ 7 ],  // props without methods
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
                4 => [
                    self::$prop . 4,
                    ClassMgr::FLOAT_T,
                    PropertyMgr::NULL_T,
                    4 . $SUMMARY . ClassMgr::FLOAT_T,
                    4 . $DESCRIPTION . ClassMgr::FLOAT_T,
                    true,
                    true,
                    true
                ],
            ],
            [
                [],        // constants
                [],        // static
                [ 4 ],     // props with methods
                [  ],      // props without methods
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
        foreach( $properties as $pIx => $propArg ) {
            if( ! in_array( $pIx, $expected[2] )) // force private property and public methods
                continue;
            switch( true ) {
                case is_string( $propArg ) :
                    $properties[ $pIx ] = [ $propArg, null, null, null, null, true, true, true ];
                    break;
                case ( $propArg instanceof VarDto ) :
                    $properties[$pIx]    = [ $propArg ];
                    $properties[$pIx][1] = true;
                    $properties[$pIx][2] = true;
                    $properties[$pIx][3] = true;
                    break;
                case ( is_array( $propArg ) && ( $propArg[0] instanceof VariableMgr )) :
                    $properties[$pIx][1] = true;
                    $properties[$pIx][2] = true;
                    $properties[$pIx][3] = true;
                    break;
                case ( is_array( $propArg ) && ( $propArg[0] instanceof VarDto )) :
                    $properties[$pIx][1] = true;
                    $properties[$pIx][2] = true;
                    $properties[$pIx][3] = true;
                    break;
                case is_array( $propArg ) :
                    $properties[$pIx][5] = true;
                    $properties[$pIx][6] = true;
                    $properties[$pIx][7] = true;
                    break;
            }
        }

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
        foreach( self::$useGroup as $useGroup ) {
            $cm->addUse( $useGroup[0], $useGroup[1], $useGroup[2] );
        }

        $code = $cm->setProperties( $properties )->toString();

        $this->classMgrTest1Tester( $case, $code, $expected );

        if( $cm->isExtendsSet()) {
            $this->assertTrue(
                ( false !== strpos( $code, '@todo' )) &&
                ( false !== strpos( $code, 'parent exists' ))
                ,
                'Error in case ' . 'todo-' . $case . PHP_EOL . $code
            );
        }
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

        static $arrTest1 = true; // test argSet1:13/14
        foreach( $properties as $pIx => $propArg ) {
            if( ! in_array( $pIx, $expected[2] )) { // force private property and public methods
                $cm->addProperty( $propArg );
                continue;
            }
            switch( true ) {
                case ( $propArg instanceof VarDto ) :
                    $cm->addProperty( $propArg, true, true, true );
                    break;
                case is_string( $propArg ) :
                    $cm->addProperty( $propArg, null, null, null, null, true, true, true );
                    break;
                case ( is_array( $propArg ) && ( $propArg[0] instanceof VariableMgr )) :
                    $cm->addProperty( $propArg[0], true, true, true  );
                    break;
                case ( is_array( $propArg ) && ( $propArg[0] instanceof VarDto )) :
                    $cm->addProperty( $propArg[0], true, true, true  );
                    break;
                case is_array( $propArg ) :
                    if( $arrTest1 ) {
                        $cm->addProperty( $propArg );
                        $arrTest1 = false;
                        break;
                    }
                    $propArg = array_pad( $propArg, 5, null );
                    $cm->addProperty( $propArg[0], $propArg[1], $propArg[2], $propArg[3], $propArg[4], true, true, true  );
                    break;
                default :
                    $cm->addProperty( $propArg );
            }
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

        $expTxt = self::$use1 . ' as ' . self::$alias1 . ';';
        $this->assertTrue(
            ( false !== strpos( $code, $expTxt )),
            'Error in case ' . 'B-' . $case . ' expected: ' . $expTxt . PHP_EOL . $code
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
            ( false !== strpos( $code, '    public function __construct()' )),
            'Error in case ' . 'K-' . $case . 'K' . PHP_EOL . $code
        );

        $this->assertTrue(
            ( false !== strpos( $code, '    public static function factory(' )),
            'Error in case ' . 'L-' . $case . PHP_EOL . $code
        );

        foreach( $expected[4] as $expNo ) {  // test all
            $this->assertTrue(
                ( false !== stripos( $code, 'prop' . $expNo )),
                'Error in case ' . 'M-' . $case . '-' . $expNo . ' expected: ' . implode( ',', $expected[4] ) . PHP_EOL . $code
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

        foreach( $expected[2] as $expNo ) {  // private/public property and public methods
            if( in_array( $expNo, [ 16, 17 ] )) {
                $this->assertTrue(
                    ( false !== strpos( $code, '    public $prop' . $expNo . ' =' )),
                    'Error in case ' . 'R1-' . $case . '-' . $expNo . PHP_EOL . $code
                );
            }
            else {
                $this->assertTrue(
                    ( false !== strpos( $code, '    private $prop' . $expNo . ' =' )),
                    'Error in case ' . 'R1-' . $case . '-' . $expNo . PHP_EOL . $code
                );
            }
            $this->assertTrue(
                (( false !== strpos( $code, '    public function getProp' . $expNo . '()' )) ||
                 ( false !== strpos( $code, '    public function isProp' . $expNo . '()' ))), // type bool
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
     * Prep Iterator tests
     *
     * @test
     */
    public function classMgrTest300() {
        for( $case = 301; $case <= 302; $case++ ) {
            $properties = [
                PropertyMgr::factory(
                    self::$prop . $case . '_1',
                    ClassMgr::ARRAY_T,
                    ClassMgr::ARRAY2_T,
                    'the array of values'
                )
                    ->setArgInFactory( true ),
            ];
            if( 301 < $case ) {
                $properties[] = PropertyMgr::factory(
                    self::$prop . $case . '_3',
                    ClassMgr::STRING_T,
                    self::$prop . $case . '-3',
                    'A constant'
                )
                    ->setIsConst();
                $properties[] = VariableMgr::factory(
                    self::$prop . $case . '_4',
                    ClassMgr::STRING_T,
                    self::$prop . $case . '-4',
                    'A static property'
                )
                    ->setStatic();
                $properties[] =
                    /*
                    PropertyMgr::init()
                        ->setVarDto( VarDto::factory( ClassMethodFactory::$POSITION, propertyMgr::INT_T, 0, 'A preset array index property' ))
                    */
                    PropertyMgr::factory(
                    ClassMethodFactory::$POSITION,
                    ClassMgr::INT_T,
                    0,
                        'A preset array index property'
                )
                    ->setMakeGetter( false )
                    ->setMakeSetter( false );
            } // end if test set 302

            // ClassMgr::setTargetPhpVersion( '5.6.0' ); // test ###

            $cm = ClassMgr::init()
                ->setNamespace( self::$namespace . $case )
                ->setName( self::$className . $case )
                ->setProperties( $properties )
                ->setFactory();

            $code = $cm->toString();

            $this->classMgrTest30x( $code, $case );
        }
    }

    /**
     * Test Iterator
     *
     * @param string $code
     * @param int    $case
     */
    public function classMgrTest30x( $code, $case ) {
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
            ( false !== stripos( $code, 'public function current()' )),
            'Error 5 in case ' . $case . PHP_EOL . $code
        );
        $this->assertTrue(
            ( false !== stripos( $code, 'public function getIterator()' )),
            'Error 6 in case ' . $case . PHP_EOL . $code
        );
        $this->assertTrue(
            ( false !== stripos( $code, 'public function key()' )),
            'Error 7 in case ' . $case . PHP_EOL . $code
        );
        $this->assertTrue(
            ( false !== stripos( $code, 'public function last()' )),
            'Error 8 in case ' . $case . PHP_EOL . $code
        );
        $this->assertTrue(
            ( false !== stripos( $code, 'public function next()' )),
            'Error 9 in case ' . $case . PHP_EOL . $code
        );
        $this->assertTrue(
            ( false !== stripos( $code, 'public function previous()' )),
            'Error 10 in case ' . $case . PHP_EOL . $code
        );
        $this->assertTrue(
            ( false !== stripos( $code, 'public function rewind()' )),
            'Error 11 in case ' . $case . PHP_EOL . $code
        );
        $this->assertTrue(
            ( false !== stripos( $code, 'public function valid()' )),
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
     * Test use-add-Exceptions
     *
     * @test
     */
    public function classMgrTest141() {
        try {
            $cm = classMgr::init()->addUse( false );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
        try {
            $cm = classMgr::init()->addUse( 'Acme\Aclass', 123 );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
        try {
            $cm = classMgr::init()->addUse( 'Acme\Aclass', 'AcmeAclass', 'grodan boll' );
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
            $pm = PropertyMgr::factory( true );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * @test
     */
    public function propertyMgrTest111() {
        $vm = VariableMgr::factory( 'test' );
        $pm = PropertyMgr::init()->cloneFromParent( $vm );
        $this->assertTrue(
            $pm instanceof PropertyMgr
        );
        $this->assertEquals( 'test', $pm->getName());
        $this->assertEquals( $vm->isStatic(), $pm->isStatic());
        $this->assertEquals( $vm->getVisibility(), $pm->getVisibility());
    }

    /**
     * Prep Iterator tests
     *
     * @test
     */
    public function classMgrDemoTest() {
        $code = ClassMgr::init()
            ->setNamespace( __NAMESPACE__ )
            ->setName( 'TestClass' )
            ->setProperties(
                PropertyMgr::factory(
                    'variable',
                    ClassMgr::ARRAY_T,
                    ClassMgr::ARRAY2_T,
                    'an array of values'
                )
            )
            ->toString();
        $this->assertNotFalse(
            strpos( $code, 'variable' )
        );
        if( DISPLAYcm ) {
            echo __FUNCTION__ . ' : ' . PHP_EOL . $code . PHP_EOL;
        }
    }
}

