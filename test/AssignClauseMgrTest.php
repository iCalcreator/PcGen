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
use Throwable;

if( ! in_array( __DIR__ . '/AcmDataProviderTrait.php', get_included_files())) {
    include( __DIR__ . '/AcmDataProviderTrait.php' );
}

class AssignClauseMgrTest extends TestCase
{
    use AcmDataProviderTrait;

    /**
     * @return array
     * @see AssignClauseMgr::returnClauseMgrTest1DataProvider
     *
     */
    public function getSourceArr1() : array
    {
        $testData = [];

        $testData[] = [
            //  6x    * null class + bool
            11,
            null,
            true,
            null,
            'true;'
        ];

        $testData[] = [
            //  6x    * null class + bool
            12,
            null,
            false,
            null,
            'false;'
        ];

        $testData[] = [
            //  6x    * null class + int
            13,
            null,
            1.3,
            null,
            '1.3;'
        ];

        $testData[] = [
            //  6x    * null class + int
            14,
            null,
            14,
            null,
            '14;'
        ];

        $testData[] = [
            //  6x    * null class + string
            15,
            null,
            '$string15',
            null,
            '$string15;'
        ];

        $testData[] = [
            //  6x    * null class + string
            16,
            null,
            'CONSTANT16',
            null,
            '"CONSTANT16";'
        ];

        $testData[] = [
            //  6x    * null class +  $-prefixed string ie variable
            17,
            null,
            '$var17',
            null,
            '$var17;'
        ];

        $testData[] = [
            //  6x    * null class + $-prefixed string (with subjectIndex) ie variable
            18,
            null,
            '$var18',
            0,
            '$var18[0];'
        ];

        $testData[] = [
            //  6x    * null class + $-prefixed string (with subjectIndex) ie variable
            19,
            null,
            '$var19',
            19,
            '$var19[19];'
        ];

        $testData[] = [
            //  6x    * null class + $-prefixed string (with subjectIndex) ie variable
            20,
            null,
            '$var20',
            'pos20',
            '$var20[$pos20];'
        ];


        $testData[] = [
            //  4x self class + string (constant)
            21,
            AssignClauseMgr::SELF_KW,
            'CONSTANT21',
            null,
            'self::$CONSTANT21;'
        ];

        $testData[] = [
            //  4x self class +  string (constant), (with subjectIndex)
            22,
            AssignClauseMgr::SELF_KW,
            'CONSTANT22',
            0,
            'self::$CONSTANT22[0];'
        ];

        $testData[] = [
            //  4x self class + $-prefixed string
            23,
            AssignClauseMgr::SELF_KW,
            '$var23',
            null,
            'self::$var23;'
        ];

        $testData[] = [
            //  4x self class + $-prefixed string (with subjectIndex)
            24,
            AssignClauseMgr::SELF_KW,
            '$var24',
            '$index24',
            'self::$var24[$index24];'
        ];

        $testData[] = [
            // 2x this                ->       string (property, opt with subjectIndex)
            31,
            AssignClauseMgr::THIS_KW,
            'string31',
            null,
            '$this->string31;'
        ];

        $testData[] = [
            // 2x this                ->       string (property, opt with subjectIndex)
            32,
            AssignClauseMgr::THIS_KW,
            'string32',
            0,
            '$this->string32[0];'
        ];

        $testData[] = [
            // 2x this                ->       string (property, opt with subjectIndex)
            33,
            AssignClauseMgr::THIS_KW,
            'string33',
            'pos33',
            '$this->string33[$pos33];'
        ];
/*
        $testData[] = [
            // 2x this                ->       predefind this->method( arg(s) )
            41,
            AssignClauseMgr::THIS_KW,
            'method41()',
            null,
            '$this->method41();'
        ];
*/
        $testData[] = [
            // 1x this                         none
            51,
            AssignClauseMgr::THIS_KW,
            null,
            null,
            '$this;'
        ];

        $testData[] = [
            // 3x otherClass (fqcn)   ::       string (constant)
            61,
            AssignClauseMgr::class,
            'CONSTANT61',
            null,
            AssignClauseMgr::class . '::$CONSTANT61;'
        ];

        $testData[] = [
            // 3x otherClass (fqcn)   ::       string (constant), with subjectIndex
            62,
            AssignClauseMgr::class,
            'CONSTANT62',
            62,
            AssignClauseMgr::class . '::$CONSTANT62[62];'
        ];

        $testData[] = [
            // 3x otherClass (fqcn)   ::       string (constant), with subjectIndex
            63,
            AssignClauseMgr::class,
            'CONSTANT63',
            'pos63',
            AssignClauseMgr::class . '::$CONSTANT63[$pos63];'
        ];

        $testData[] = [
            // 3x $class
            71,
            '$class71',
            null,
            null,
            '$class71;'
        ];

        $testData[] = [
            // 3x $class :: class with property
            72,
            '$class72',
            '$class72',
            null,
            '$class72->class72;'
        ];

        $testData[] = [
            // 3x $class ::  string (constant)
            73,
            '$class73',
            'CONSTANT73',
            null,
            '$class73->CONSTANT73;'
        ];

        $testData[] = [
            // 3x $class  ::  string (constant) with index
            74,
            '$class74',
            'CONSTANT74',
            'seventyfour',
            '$class74->CONSTANT74[$seventyfour];'
        ];

        $testData[] = [
            // 3x $class  :: (public) property
            75,
            '$class75',
            '$property75',
            null,
            '$class75->property75;'
        ];

        $testData[] = [
            // 3x $class  :: (public) property with subjectIndex
            76,
            '$class76',
            '$property76',
            '76',
            '$class76->property76[76];'
        ];

        $testData[] = [
            // 3x $class  :: (public) property with subjectIndex
            77,
            '$class77',
            '$property77',
            'sevenSeven',
            '$class77->property77[$sevenSeven];'
        ];

        return $testData;
    }

    public function AssignClauseMgrTest1DataProvider() : array
    {
        $testData = [];
        $operands = array_flip( AssignClauseMgr::getOperators());
        foreach( $this->getTargetArr1() as $targetArgSet ) {
            foreach( $this->getSourceArr1() as $sourceArgSet ) {
                $operator = array_rand( $operands );
                $testData[] = [
                    $targetArgSet[0] . '-' . $sourceArgSet[0],
                    [ $targetArgSet[1], $targetArgSet[2], $targetArgSet[3] ],
                    [ $sourceArgSet[1], $sourceArgSet[2], $sourceArgSet[3] ],
                    $operator,
                    $targetArgSet[4] . ' ' .  $operator . ' ' . $sourceArgSet[4]
                ];
            }
        }

        return $testData;
    }

    /**
     * Testing AssignClauseMgr
     *
     * @test
     * @dataProvider AssignClauseMgrTest1DataProvider
     *
     * @param string $case
     * @param array  $targetArgSet
     * @param array  $sourceArgSet
     * @param string $operator
     * @param string $expected
     */
    public function AssignClauseMgrTest1( string $case, array $targetArgSet, array $sourceArgSet, string $operator, string $expected ) {
/*
        echo $case .
            ' target : ' . $targetArgSet[0] . ' - ' . $targetArgSet[1] . ' - ' . $targetArgSet[2] .
            ' source : ' . $sourceArgSet[0] . ' - ' . $sourceArgSet[1] . ' - ' . $sourceArgSet[2] . PHP_EOL; // test ###
*/
        switch( true ) {
            case ( AssignClauseMgr::THIS_KW == $targetArgSet[0] ) :
                $targetArgSet[1] = Util::setVarPrefix( $targetArgSet[1] );
                $acm = AssignClauseMgr::init()
                    ->setThisPropertyTarget( $targetArgSet[1], $targetArgSet[2] )
                    ->setSource( $sourceArgSet[0], $sourceArgSet[1], $sourceArgSet[2] );
                $case .= 'A';
                break;
            case ( empty( $targetArgSet[0] )) :
                $targetArgSet[1] = Util::unSetVarPrefix( $targetArgSet[1] );
                $acm = AssignClauseMgr::init()
                    ->setVariableTarget( $targetArgSet[1], $targetArgSet[2] )
                    ->setSource( $sourceArgSet[0], $sourceArgSet[1], $sourceArgSet[2] );
                $case .= 'B';
                break;

            case (( AssignClauseMgr::THIS_KW == $sourceArgSet[0] ) &&
                is_string( $sourceArgSet[1] ) && ! empty( $sourceArgSet[2] )) :
                $sourceArgSet[1] = Util::setVarPrefix( $sourceArgSet[1] );
                $acm = AssignClauseMgr::init()
                    ->setTarget( $targetArgSet[0], $targetArgSet[1], $targetArgSet[2] )
                    ->setThisPropertySource( $sourceArgSet[1], $sourceArgSet[2] );
                $case .= 'C';
                break;
            case ( empty( $sourceArgSet[0] ) &&
                is_string( $sourceArgSet[1] ) && Util::isVarPrefixed( $sourceArgSet[1] )
                && empty( $sourceArgSet[2] )) :
                $sourceArgSet[1] = Util::unSetVarPrefix( $sourceArgSet[1] );
                $acm = AssignClauseMgr::init()
                    ->setTarget( $targetArgSet[0], $targetArgSet[1], $targetArgSet[2] )
                    ->setVariableSource( $sourceArgSet[1], $sourceArgSet[2] );
                $case .= 'D';
                break;

            default :
                $acm = AssignClauseMgr::factory(
                    $targetArgSet[0], $targetArgSet[1], $targetArgSet[2],
                    $sourceArgSet[0], $sourceArgSet[1], $sourceArgSet[2]
                );
                $case .= 'E';
                break;
        }
        $acm->setOperator( $operator );
        $code = $acm->toString();
        if( $acm->isTargetSet()) {
            $this->assertFalse( $acm->isTargetStatic() );
        }
        if( $acm->isSourceSet()) {
            $this->assertFalse( $acm->isSourceStatic() );
        }
        $this->assertEquals(
            trim( $expected ),
            trim( $code ),
            'case : ' . $case . ' actual : ' . trim( $code ) . ' expected : ' . trim( $expected )
        );
        $this->assertEquals( $operator, $acm->getOperator( true ));
        $this->assertTrue( is_array( $acm->toArray()));
        if( DISPLAYacm ) {
            echo __FUNCTION__ . ' ' . $case . ' : ' . trim( $code ) . PHP_EOL;
        }

    }

    public function getTargetArr2() : array
    {
        $testData = [];

        $testData[] = [
            // 3x $class  :: (public static) property
            75,
            '$class75',
            '$property75',
            null,
            '$class75::$property75'
        ];

        $testData[] = [
            // 3x $class  :: (public static) property with subjectIndex
            76,
            '$class76',
            '$property76',
            '76',
            '$class76::$property76[76]'
        ];

        $testData[] = [
            // 3x $class  :: (public static) property with subjectIndex
            77,
            '$class77',
            '$property77',
            'sevenSeven',
            '$class77::$property77[$sevenSeven]'
        ];

        $testData[] = [
            // 3x $class  :: (public) property with subjectIndex, only possible with operator '='
            78,
            '$class78',
            '$property78',
            '[]',
            '$class78::$property78[]'
        ];
        return $testData;
    }

    /**
     * testing $class with static property
     *
     * @return array
     *
     */
    public function getSourceArr2() : array
    {
        $testData = [];

        $testData[] = [
            // 3x $class  :: (public static) property
            75,
            '$class75',
            '$property75',
            null,
            '$class75::$property75;'
        ];

        $testData[] = [
            // 3x $class  :: (public static) property with subjectIndex
            76,
            '$class76',
            '$property76',
            '76',
            '$class76::$property76[76];'
        ];

        $testData[] = [
            // 3x $class  :: (public static) property with subjectIndex
            77,
            '$class77',
            '$property77',
            'sevenSeven',
            '$class77::$property77[$sevenSeven];'
        ];

        return $testData;
    }

    public function AssignClauseMgrTest2DataProvider() : array
    {
        $testData = [];
        $operands = array_flip( AssignClauseMgr::getOperators());
        foreach( $this->getTargetArr2() as $targetArgSet ) {
            foreach( $this->getSourceArr2() as $sourceArgSet ) {
                $operator = array_rand( $operands );
                $testData[] = [
                    $targetArgSet[0] . '-' . $sourceArgSet[0],
                    [ $targetArgSet[1], $targetArgSet[2], $targetArgSet[3] ],
                    [ $sourceArgSet[1], $sourceArgSet[2], $sourceArgSet[3] ],
                    $operator,
                    $targetArgSet[4] . ' ' .  $operator . ' ' . $sourceArgSet[4]
                ];
            }
        }

        return $testData;
    }

    /**
     * Testing AssignClauseMgr, same as AssignClauseMgrTest2 but testing $class with static vars
     *
     * @test
     * @dataProvider AssignClauseMgrTest2DataProvider
     *
     * @param string $case
     * @param array  $targetArgSet
     * @param array  $sourceArgSet
     * @param string $operator
     * @param string $expected
     */
    public function AssignClauseMgrTest2(
        string $case,
        array $targetArgSet,
        array $sourceArgSet,
        string $operator,
        string $expected
    )
    {
/*
        echo $case .
            ' target : ' . $targetArgSet[0] . ' - ' . $targetArgSet[1] . ' - ' . $targetArgSet[2] .
            ' source : ' . $sourceArgSet[0] . ' - ' . $sourceArgSet[1] . ' - ' . $sourceArgSet[2] . PHP_EOL; // test ###
*/
        $acm = AssignClauseMgr::factory(
            $targetArgSet[0], $targetArgSet[1], $targetArgSet[2],
            $sourceArgSet[0], $sourceArgSet[1], $sourceArgSet[2] )
            ->setTargetIsStatic( true )        //       <---------------------
            ->setSourceIsStatic( true )        //       <---------------------
            ->setOperator( $operator );
        $code = $acm->toString();
        $this->assertEquals(
            trim( $expected ),
            trim( $code ),
            'case : ' . $case . ' actual : ' . trim( $code ) . ' expected : ' . trim( $expected )
        );
        $this->assertTrue( $acm->isTargetStatic());  // hera are both set !!
        $this->assertTrue( $acm->isSourceStatic());
        $this->assertEquals( $operator, $acm->getOperator( true ));
        $this->assertTrue( is_array( $acm->toArray()));
        if( DISPLAYacm ) {
            echo __FUNCTION__ . ' ' . $case . ' : ' . trim( $code ) . PHP_EOL;
        }

    }

    /**
     * Testing isConst (target+source)
     *
     * @test
     */
    public function AssignClauseMgrTest4(){
        $entity = EntityMgr::factory( null, 'target' );
        $this->assertEquals(
            $entity->getVariable(),
            AssignClauseMgr::init()->setTarget( $entity )->getTarget()->getVariable()
        );

        $this->assertTrue( AssignClauseMgr::init()->setTarget( $entity )->setTargetIsConst()->getTarget()->isConst());

        $entity = EntityMgr::factory( null, 'source' );
        $this->assertEquals(
            $entity->getVariable(),
            AssignClauseMgr::init()->setSource( $entity )->getSource()->getVariable()
        );

        $this->assertTrue( AssignClauseMgr::init()->setSource( $entity )->setSourceIsConst()->getSource()->isConst());
    }

    /**
     * @test
     */
    public function AssignClauseMgrTest5() {
        $this->assertTrue( is_array( AssignClauseMgr::getOperators()));
    }

    /**
     * Test setExpression
     *
     * @test
     */
    public function AssignClauseMgrTest6() {
        $code = AssignClauseMgr::init()
            ->setThisPropertyTarget( 'property' )
            ->setExpression( 'trim( $argument )' )
            ->toString();

        $exp = '$this->property = trim( $argument );';
        $got = trim( $code );
        $this->assertEquals(
            $exp,
            $got,
            'exp : ' . $exp . ' got : ' . $got
        );
        if( DISPLAYacm ) {
            echo __FUNCTION__ . ' : ' . trim( $code ) . PHP_EOL;
        }
    }

    /**
     * @test
     */
    public function returnClauseMgrTest7() {
        $acm = AssignClauseMgr::init();
        try {
            $acm->setExpression( 123 );
            $this->assertTrue( false );
        }
        catch( Throwable $e ) {
            $this->assertTrue( true );
        }

        $expression = 'array_rand( [ 1,2 ] )';
        $this->assertEquals( $expression, $acm->setExpression( $expression )->getScalar());

        $acm->setVariableTarget( 'variable' )
            ->setIndent()
            ->setBaseIndent();
        $this->assertEquals(
            '$variable = array_rand( [ 1,2 ] );' . PHP_EOL,
            $acm->toString()
        );
    }

    /**
     * @test
     */
    public function AssignClauseMgrTest8() {
        $acm = AssignClauseMgr::init();
        $this->assertEquals(
            AssignClauseMgr::SELF_KW,
            $acm->setTarget()->setTarget( AssignClauseMgr::SELF_KW, 'variable' )->getTarget()->getClass()
        );
        $this->assertEquals(
            'test',
            $acm->getTarget()->setVariable( 'test' )->getVariable()
        );
        $this->assertEquals(
            43,
            $acm->getTarget()->setIndex( 43 )->getIndex()
        );
        $this->assertEquals(
            AssignClauseMgr::SELF_KW,
            $acm->setSource()->setSource( AssignClauseMgr::SELF_KW, 'variable' )->getSource()->getClass()
        );
        $this->assertEquals(
            'test',
            $acm->getSource()->setVariable( 'test' )->getVariable()
        );
        $this->assertEquals(
            44,
            $acm->getSource()->setIndex( 44 )->getIndex()
        );

        $this->assertTrue(
            is_array( EntityMgr::init()->setVariable( 'test' )->toArray())
        );
    }

    /**
     * @test
     */
    public function AssignClauseMgrTest9() {
        try {
            AssignClauseMgr::init()->toArray();
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }

        try {
            AssignClauseMgr::init()->setOperator( PHP_EOL );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }

        try {
            AssignClauseMgr::init()->setThisPropertyTarget( false );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }

        try {
            AssignClauseMgr::init()->setVariableTarget( false );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }

        try {
            AssignClauseMgr::init()->setThisPropertySource( false );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }

        try {
            AssignClauseMgr::init()->setVariableSource( false );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

}
