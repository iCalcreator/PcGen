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

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Throwable;

class CtrlStructMgrTest extends TestCase
{
    public static function ctrlStructMgrTest1DataProvider() : array
    {
        $testData = [];

        $testData[] = [
            11,
            'stringValue1',
            'stringValue2',
        ];

        $testData[] = [
            21,
            'stringValue1',
            EntityMgr::factory( null, 'variable2'),
        ];
        $testData[] = [
            22,
            EntityMgr::factory( EntityMgr::THIS_KW, 'variable1' ),
            'stringValue2',
        ];
        $testData[] = [
            23,
            EntityMgr::factory( null, 'variable1'),
            EntityMgr::factory( EntityMgr::THIS_KW, 'variable2' ),
        ];

        $testData[] = [
            31,
            'stringValue1',
            FcnInvokeMgr::factory( FcnInvokeMgr::THIS_KW, 'function31'),
        ];
        $testData[] = [
            32,
            FcnInvokeMgr::factory( FcnInvokeMgr::THIS_KW, 'function32'),
            'stringValue2',
        ];
        $testData[] = [
            33,
            EntityMgr::factory( EntityMgr::THIS_KW, 'variable1' ),
            FcnInvokeMgr::factory( FcnInvokeMgr::THIS_KW, 'function33'),
        ];
        $testData[] = [
            34,
            FcnInvokeMgr::factory( FcnInvokeMgr::THIS_KW, 'function34'),
            EntityMgr::factory( null, 'variable2'),
        ];

        $testData[] = [
            41,
            EntityMgr::factory( EntityMgr::THIS_KW, 'variable1' ),
            FcnInvokeMgr::factory( 'otherClass', 'function41'),
        ];
        $testData[] = [
            42,
            FcnInvokeMgr::factory( '$otherClass', 'function42'),
            EntityMgr::factory( EntityMgr::THIS_KW, 'variable2' ),
        ];

        return $testData;
    }

    /**
     * Set operand1/operand2 condition
     *
     * @param bool|float|int|string|EntityMgr $operand1
     * @param bool|float|int|string|EntityMgr $operand2
     * @return CtrlStructMgr
     */
    public static function getCsm2( $operand1, $operand2 ) : CtrlStructMgr
    {
        $csm = CtrlStructMgr::init()
            ->setCompOP( array_rand( array_flip( SimpleCondMgr::getCondOPs())))
            ->setOperand2( $operand2 );
        if(( $operand1 instanceof EntityMgr ) &&
            ( EntityMgr::THIS_KW == $operand1->getClass())) {
            $csm->setThisVarOperand1( $operand1->getVariable());
        }
        else {
            $csm->setOperand1( $operand1 );
        }
        if(( $operand2 instanceof EntityMgr ) &&
            ( EntityMgr::THIS_KW == $operand2->getClass())) {
            $csm->setThisVarOperand2( $operand2->getVariable());
        }
        else {
            $csm->setOperand2( $operand2 );
        }
        return $csm;
    }

    /**
     * Set boolean condition
     *
     * @param bool|float|int|string|EntityMgr $operand
     * @return CtrlStructMgr
     */
    public static function getCsm1( $operand ) : CtrlStructMgr
    {
        $csm = CtrlStructMgr::init();
        if(( $operand instanceof EntityMgr ) &&
            ( EntityMgr::THIS_KW == $operand->getClass())) {
            $csm->setThisPropSingleOp( $operand->getVariable());
        }
        else {
            $csm->setSingleOp( $operand );
        }
        return $csm;
    }

    /**
     * Set function invoke boolean condition
     *
     * @param FcnInvokeMgr $operand
     * @return CtrlStructMgr
     */
    public static function getCsm3( FcnInvokeMgr $operand ) : CtrlStructMgr
    {
        $csm = CtrlStructMgr::init();
        if( EntityMgr::THIS_KW == $operand->getName()->getClass()) {
            $csm->setThisFcnSingleOP( $operand->getName()->getVariable());
        }
        else {
            $csm->setSingleOp( $operand );
        }
        return $csm;
    }

    /**
     * Testing operand1/operand2 condition if/elseif/else
     *
     * @test
     * @dataProvider ctrlStructMgrTest1DataProvider
     * @param int    $case
     * @param bool|float|int|string|EntityMgr|FcnInvokeMgr $operand1
     * @param bool|float|int|string|EntityMgr|FcnInvokeMgr $operand2
     */
    public function ctrlStructMgrTest11( int $case, $operand1, $operand2 )
    {
        $csm     = self::getCsm2( $operand1, $operand2 );
        $this->assertTrue(
            $csm->isConditionSet()
        );
        $csm->setCondition( $csm->getCondition());

        $csmBody = ' /* this is body for case ' . __FUNCTION__ . ' ' . $case . ' */';
        $body0   = $csm->setBody( $csmBody )
            ->toArray();
        $body1 = $csm->setElseIfExprType()
            ->setCompOP( array_rand( array_flip( SimpleCondMgr::getCondOPs())))
            ->toArray();
        $body2 = CtrlStructMgr::init()
            ->setElseExprType()
            ->setBody( $csmBody )
            ->toArray();

        $code = FcnFrameMgr::init()
            ->setName( __FUNCTION__ . '_' . $case )
            ->setBody( $body0, $body1, $body2 )
            ->toString();

        self::assertCode( $case, $operand1, $operand2, $code, $csm );

        if( DISPLAYffm ) {
            echo __FUNCTION__ . ' ' . $case . ' ->' . PHP_EOL . $code . '<-' . PHP_EOL;
        }
    }

    /**
     * @param int    $case
     * @param bool|float|int|string|EntityMgr|FcnInvokeMgr $operand1
     * @param bool|float|int|string|EntityMgr|FcnInvokeMgr $operand2
     * @param string $code
     * @param CtrlStructMgr $csm
     */
    private function assertCode( int $case, $operand1, $operand2, string $code, CtrlStructMgr $csm )
    {
        $exp1 = is_scalar( $operand1 ) ? $operand1 : rtrim( $operand1->toString());
        $exp2 = is_scalar( $operand2 ) ? $operand2 : rtrim( $operand2->toString());

        $this->assertNotFalse(
            strpos( $code, $exp1 ),
            'Error in ' . __FUNCTION__ . ' ' . $case . '-1' . PHP_EOL . $code
        );
        if( $operand1 instanceof EntityMgr ) {
            $this->assertTrue(
                ( $csm->getCondition()->getOperand1() instanceof EntityMgr ),
                'Error in ' . __FUNCTION__ . ' ' . $case . '-2' . PHP_EOL . $code
            );
        }
        $this->assertNotFalse(
            strpos( $code, $exp2 ),
            'Error in ' . __FUNCTION__ . ' ' . $case . '-3' . PHP_EOL . $code
        );
        if( $operand2 instanceof EntityMgr ) {
            $this->assertTrue(
                ( $csm->getCondition()->getOperand2() instanceof EntityMgr ),
                'Error in case ' . $case . '-4' . ' ->' . PHP_EOL . $code . '<-' . PHP_EOL
            );
        }
    }

    /**
     * Testing bool condition  if/elseif/else
     *
     * @test
     * @dataProvider ctrlStructMgrTest1DataProvider
     * @param int    $case
     * @param bool|float|int|string|EntityMgr|FcnInvokeMgr $operand1
     * @param bool|float|int|string|EntityMgr|FcnInvokeMgr $operand2
     */
    public function ctrlStructMgrTest12( int $case, $operand1, $operand2 )
    {
        if( $operand2 instanceof FcnInvokeMgr ) {
            $this->assertTrue( true );
            return;
        }
        $csm = self::getCsm1( $operand2 )
                ->setBody( ' /* this is body for case ' . __FUNCTION__ . ' ' . $case . ' */' );

        $body = $csm->toArray();
        $fcnFrameMgr = FcnFrameMgr::init()
            ->setName( __FUNCTION__ . '_' . $case )
            ->setBody( $body );
        $code = $fcnFrameMgr->toString();

        $this->assertTrue(
            ( $csm->getCondition()->getSingleOp() instanceof EntityMgr ),
            'Error in ' . __FUNCTION__ . ' ' . $case . '-1' . PHP_EOL . $code
        );

        $exp2 = is_scalar( $operand2 ) ? $operand2 : rtrim( $operand2->toString());
        $this->assertNotFalse(
            strpos( $code, $exp2 ),
            'Error in ' . __FUNCTION__ . ' ' . $case . '-2' . PHP_EOL . $code
        );

        if( DISPLAYffm ) {
            echo __FUNCTION__ . ' ' . $case . ' ->' . PHP_EOL . $code . '<-' . PHP_EOL;
        }
    }

    /**
     * Testing function invoke bool evaluation
     *
     * @test
     * @dataProvider ctrlStructMgrTest1DataProvider
     * @param int    $case
     * @param bool|float|int|string|EntityMgr|FcnInvokeMgr $operand1
     * @param bool|float|int|string|EntityMgr|FcnInvokeMgr $operand2
     */
    public function ctrlStructMgrTest13( int $case, $operand1, $operand2 )
    {
        if( ! $operand2 instanceof FcnInvokeMgr ) {
            $this->assertTrue( true );
            return;
        }
        $csm = self::getCsm3( $operand2 )
            ->setBody( ' /* this is body for case ' . __FUNCTION__ . ' ' . $case . ' */' );
//      $body = $csm->toString();
        $body = $csm->toArray();

        $fcnFrameMgr = FcnFrameMgr::init()
            ->setName( __FUNCTION__ . '_' . $case . '_1' )
            ->setBody( $body );
        $code = $fcnFrameMgr->toString();

        $exp2 = rtrim( $operand2->toString());
        $this->assertNotFalse(
            strpos( $code, $exp2 ),
            'Error in case ' . __FUNCTION__ . '-2' . PHP_EOL . $code
        );

        if( DISPLAYffm ) {
            echo __FUNCTION__ . ' ' . $case . ' ->' . PHP_EOL . $code . '<-' . PHP_EOL;
        }
    }

    /**
     * Testing switch, case, default, operand1/operand2 condition
     *
     * @test
     * @dataProvider ctrlStructMgrTest1DataProvider
     * @param int    $case
     * @param bool|float|int|string|EntityMgr $operand1
     * @param bool|float|int|string|EntityMgr $operand2
     */
    public function ctrlStructMgrTest41( int $case, $operand1, $operand2 )
    {
        $csm     = self::getCsm2( $operand1, $operand2 );
        $csm2    = self::getCsm2( $operand1, $operand2 );
        $csmBody = ' /* this is body for case ' . __FUNCTION__ . ' ' . $case . ' */';

        $code = FcnFrameMgr::init()
            ->setName( __FUNCTION__ . '_' . $case )
            ->setBody(
                $csm
                    ->setSwitchExprType()
                    ->setBody(
                        $csm2->setCaseExprType()
                            ->setBody( $csmBody )
                            ->toArray(),
                        CtrlStructMgr::init()
                            ->setDefaultExprType()
                            ->setBody( $csmBody )
                            ->toArray()
                    )
                    ->toArray()
            )
            ->toString();

        $this->assertCode( $case, $operand1, $operand2, $code, $csm );

        if( DISPLAYffm ) {
            echo __FUNCTION__ . ' ' . $case . ' ->' . PHP_EOL . $code . '<-' . PHP_EOL;
        }
    }

    /**
     * Testing switch, case, default, bool condition
     *
     * @test
     * @dataProvider ctrlStructMgrTest1DataProvider
     * @param int    $case
     * @param bool|float|int|string|EntityMgr|FcnInvokeMgr $operand1
     * @param bool|float|int|string|EntityMgr|FcnInvokeMgr $operand2
     */
    public function ctrlStructMgrTest42( int $case, $operand1, $operand2 )
    {
        if( $operand1 instanceof FcnInvokeMgr ) {
            $this->assertTrue( true );
            return;
        }
        if( $operand2 instanceof FcnInvokeMgr ) {
            $this->assertTrue( true );
            return;
        }
        $csm     = self::getCsm1( $operand1 );
        $csm2    = self::getCsm1( $operand2 );
        $csmBody = ' /* this is body for case ' . __FUNCTION__ . ' ' . $case . ' */';
        $body    = $csm
            ->setBody(
                $csm2->setCaseExprType()
                    ->setBody( $csmBody )
                    ->toArray(),
                CtrlStructMgr::init()
                    ->setDefaultExprType()
                    ->setBody( $csmBody )
                    ->toArray()
            )
            ->setSwitchExprType()
            ->toArray();

        $fcnFrameMgr = FcnFrameMgr::init()
            ->setName( __FUNCTION__ . '_' . $case )
            ->setBody( $body );
        $code = $fcnFrameMgr->toString();

        $exp1 = is_scalar( $operand1 ) ? $operand1 : rtrim( $operand1->toString());
        $exp2 = is_scalar( $operand2 ) ? $operand2 : rtrim( $operand2->toString());
        $this->assertNotFalse(
            strpos( $code, $exp1 ),
            'Error in ' . __FUNCTION__ . ' ' . $case . '-1' . PHP_EOL . $code
        );
        $this->assertNotFalse(
            strpos( $code, $exp2 ),
            'Error in ' . __FUNCTION__ . ' ' . $case . '-2' . PHP_EOL . $code
        );

        if( DISPLAYffm ) {
            echo __FUNCTION__ . ' ' . $case . ' ->' . PHP_EOL . $code . '<-' . PHP_EOL;
        }
    }

    /**
     * Testing switch, case, default, function invoke bool evaluation
     *
     * @test
     * @dataProvider ctrlStructMgrTest1DataProvider
     * @param int    $case
     * @param bool|float|int|string|EntityMgr|FcnInvokeMgr $operand1
     * @param bool|float|int|string|EntityMgr|FcnInvokeMgr $operand2
     */
    public function ctrlStructMgrTest43( int $case, $operand1, $operand2 )
    {
        if( ! $operand2 instanceof FcnInvokeMgr ) {
            $this->assertTrue( true );
            return;
        }
        $csm  = self::getCsm3( $operand2 );
        $csm2 = self::getCsm3( $operand2 );
        $csmBody = ' /* this is body for case ' . __FUNCTION__ . ' ' . $case . ' */';

        $body    = $csm
            ->setBody(
                $csm2->setCaseExprType()
                    ->setBody( $csmBody )
                    ->toArray(),
                CtrlStructMgr::init()
                    ->setDefaultExprType()
                    ->setBody( $csmBody )
                    ->toArray()
            )
            ->setSwitchExprType()
            ->toArray();

        $fcnFrameMgr = FcnFrameMgr::init()
            ->setName( __FUNCTION__ . '_' . $case )
            ->setBody( $body );
        $code = $fcnFrameMgr->toString();

        $exp2 = rtrim( $operand2->toString());
        $this->assertNotFalse(
            strpos( $code, $exp2 ),
            'Error in case ' . __FUNCTION__ . '-2' . PHP_EOL . $code
        );

        if( DISPLAYffm ) {
            echo __FUNCTION__ . ' ' . $case . ' ->' . PHP_EOL . $code . '<-' . PHP_EOL;
        }
    }

    /**
     * Testing switch (variable) and scalar case, default
     *
     * @test
     */
    public function ctrlStructMgrTest44()
    {
        $fcnFrameMgr = FcnFrameMgr::factory( __FUNCTION__, [ 'argument' ] )
            ->setBody(
                CtrlStructMgr::init()
                    ->setSwitchExprType()
                    ->setSingleOp( 'argument' )
                    ->setBody(
                        CtrlStructMgr::init()
                            ->setCaseExprType()
                            ->setScalar( 1 )
                            ->setBody( '/* case-1 body */' )
                            ->toArray(),
                        CtrlStructMgr::init()
                            ->setCaseExprType()
                            ->setScalar( 2 )
                            ->setBody( '/* case-2 body */' )
                            ->toArray(),
                        CtrlStructMgr::init()
                            ->setDefaultExprType()
                            ->setBody( '/* default body */' )
                            ->toArray()
                    )
                    ->toArray()
            );
//                      CtrlStructMgr::init()
//                          ->setCaseExprType()
//                          ->setExpression( '! is_int( $argument )' )
//                          ->setBody( '/* case-1 body */' )
//                          ->toArray(),
        $code = $fcnFrameMgr->toString();

        $this->assertEquals(
            2,
            substr_count( $code, '$argument' ),
            'Error in case ' . __FUNCTION__ . PHP_EOL . $code
        );

        if( DISPLAYffm ) {
            echo __FUNCTION__ . ' ->' . PHP_EOL . $code . '<-' . PHP_EOL;
        }
    }

    /**
     * Testing switch(true) and expression case, default
     *
     * @test
     */
    public function ctrlStructMgrTest45()
    {
        $fcnFrameMgr = FcnFrameMgr::factory( __FUNCTION__, [ 'argument' ] )
            ->setBody(
                CtrlStructMgr::init()
                    ->setSwitchExprType()
                    ->setScalar( true )
                    ->setBody(
                        CtrlStructMgr::init()
                            ->setCaseExprType()
                            ->setExpression( '! is_int( $argument )' )
                            ->setBody( '/* case-1 body */' )
                            ->toArray(),
                        CtrlStructMgr::init()
                            ->setDefaultExprType()
                            ->setBody( '/* default body */' )
                            ->toArray()
                    )
                    ->toArray()
            );
        $code = $fcnFrameMgr->toString();

        $this->assertEquals(
            2,
            substr_count( $code, '$argument' ),
            'Error in case ' . __FUNCTION__ . PHP_EOL . $code
        );

        if( DISPLAYffm ) {
            echo __FUNCTION__ . ' ->' . PHP_EOL . $code . '<-' . PHP_EOL;
        }
    }

    /**
     * Testing while, operand1/operand2 condition
     *
     * @test
     * @dataProvider ctrlStructMgrTest1DataProvider
     * @param int    $case
     * @param bool|float|int|string|EntityMgr $operand1
     * @param bool|float|int|string|EntityMgr $operand2
     */
    public function ctrlStructMgrTest51( int $case, $operand1, $operand2 )
    {
        $csm     = self::getCsm2( $operand1, $operand2 );
        $csmBody = ' /* this is body for case ' . __FUNCTION__ . ' ' . $case . ' */';
        $body0   = $csm->setBody( $csmBody )
            ->setWhileExprType()
            ->toArray();
        $fcnFrameMgr = FcnFrameMgr::init()
            ->setName( __FUNCTION__ . '_' . $case )
            ->setBody( $body0 );
        $code = $fcnFrameMgr->toString();

        $this->assertCode( $case, $operand1, $operand2, $code, $csm );

        if( DISPLAYffm ) {
            echo __FUNCTION__ . ' ' . $case . ' ->' . PHP_EOL . $code . '<-' . PHP_EOL;
        }
    }

    /**
     * Testing while, bool condition
     *
     * @test
     * @dataProvider ctrlStructMgrTest1DataProvider
     * @param int    $case
     * @param bool|float|int|string|EntityMgr|FcnInvokeMgr $operand1
     * @param bool|float|int|string|EntityMgr|FcnInvokeMgr $operand2
     */
    public function ctrlStructMgrTest52( int $case, $operand1, $operand2 ) {
        if( $operand1 instanceof FcnInvokeMgr ) {
            $this->assertTrue( true );
            return;
        }
        $csm  = self::getCsm1( $operand1 );
        $csmBody = ' /* this is body for case ' . __FUNCTION__ . ' ' . $case . ' */';
        $body0 = $csm->setBody( $csmBody )
            ->setWhileExprType()
            ->toArray();
        $fcnFrameMgr = FcnFrameMgr::init()
            ->setName( __FUNCTION__ . '_' . $case )
            ->setBody( $body0 );
        $code = $fcnFrameMgr->toString();

        $exp1 = is_scalar( $operand1 ) ? $operand1 : rtrim( $operand1->toString());
        $this->assertNotFalse(
            strpos( $code, $exp1 ),
            'Error in ' . __FUNCTION__ . ' ' . $case . '-1' . PHP_EOL . $code
        );

        if( DISPLAYffm ) {
            echo __FUNCTION__ . ' ' . $case . ' ->' . PHP_EOL . $code . '<-' . PHP_EOL;
        }
    }

    /**
     * Testing while, function invoke bool evaluation
     *
     * @test
     * @dataProvider ctrlStructMgrTest1DataProvider
     * @param int    $case
     * @param bool|float|int|string|EntityMgr|FcnInvokeMgr $operand1
     * @param bool|float|int|string|EntityMgr|FcnInvokeMgr $operand2
     */
    public function ctrlStructMgrTest53( int $case, $operand1, $operand2 )
    {
        if( ! $operand2 instanceof FcnInvokeMgr ) {
            $this->assertTrue( true );
            return;
        }
        $csm     = self::getCsm3( $operand2 );
        $csmBody = ' /* this is body for case ' . __FUNCTION__ . ' ' . $case . ' */';
        $body0   = $csm->setBody( $csmBody )
            ->setWhileExprType()
            ->toArray();
        $fcnFrameMgr = FcnFrameMgr::init()
            ->setName( __FUNCTION__ . '_' . $case )
            ->setBody( $body0 );
        $code = $fcnFrameMgr->toString();

        $exp2 = rtrim( $operand2->toString());
        $this->assertNotFalse(
            strpos( $code, $exp2 ),
            'Error in ' . __FUNCTION__ . ' ' . $case . '-1' . ' exp: ' . $exp2 . PHP_EOL . $code
        );

        if( DISPLAYffm ) {
            echo __FUNCTION__ . ' ' . $case . ' ->' . PHP_EOL . $code . '<-' . PHP_EOL;
        }
    }

    /**
     * Testing do-while, operand1/operand2 condition
     *
     * @test
     * @dataProvider ctrlStructMgrTest1DataProvider
     * @param int    $case
     * @param bool|float|int|string|EntityMgr $operand1
     * @param bool|float|int|string|EntityMgr $operand2
     */
    public function ctrlStructMgrTest61( int $case, $operand1, $operand2 )
    {
        $csm     = self::getCsm2( $operand1, $operand2 );
        $csmBody = ' /* this is body for case ' . __FUNCTION__ . ' ' . $case . ' */';
        $body0   = $csm->setBody( $csmBody )
            ->setDoWhileExprType()
            ->toArray();
        $fcnFrameMgr = FcnFrameMgr::init()
            ->setName( __FUNCTION__ . '_' . $case )
            ->setBody( $body0 );
        $code = $fcnFrameMgr->toString();

        $this->assertCode( $case, $operand1, $operand2, $code, $csm );

        if( DISPLAYffm ) {
            echo __FUNCTION__ . ' ' . $case . ' ->' . PHP_EOL . $code . '<-' . PHP_EOL;
        }
    }

    /**
     * Testing do-while, bool condition
     *
     * @test
     * @dataProvider ctrlStructMgrTest1DataProvider
     * @param int    $case
     * @param bool|float|int|string|EntityMgr $operand1
     * @param bool|float|int|string|EntityMgr $operand2
     */
    public function ctrlStructMgrTest62( int $case, $operand1, $operand2 )
    {
        if( $operand2 instanceof FcnInvokeMgr ) {
            $this->assertTrue( true );
            return;
        }
        $csm  = self::getCsm1( $operand2 );
        $csmBody = ' /* this is body for case ' . __FUNCTION__ . ' ' . $case . ' */';
        $body0 = $csm->setBody( $csmBody )
            ->setDoWhileExprType()
            ->toArray();
        $fcnFrameMgr = FcnFrameMgr::init()
            ->setName( __FUNCTION__ . '_' . $case )
            ->setBody( $body0 );
        $code = $fcnFrameMgr->toString();

        $exp2 = is_scalar( $operand2 ) ? $operand2 : rtrim( $operand2->toString());
        $this->assertNotFalse(
            strpos( $code, $exp2 ),
            'Error in ' . __FUNCTION__ . ' ' . $case . '-1' . ' exp: ' . $exp2 . PHP_EOL . $code
        );

        if( DISPLAYffm ) {
            echo __FUNCTION__ . ' ' . $case . ' ->' . PHP_EOL . $code . '<-' . PHP_EOL;
        }
    }

    /**
     * Testing do-while, function invoke bool evaluation
     *
     * @test
     * @dataProvider ctrlStructMgrTest1DataProvider
     * @param int    $case
     * @param bool|float|int|string|EntityMgr|FcnInvokeMgr $operand1
     * @param bool|float|int|string|EntityMgr|FcnInvokeMgr $operand2
     */
    public function ctrlStructMgrTest63( int $case, $operand1, $operand2 )
    {
        if( ! $operand2 instanceof FcnInvokeMgr ) {
            $this->assertTrue( true );
            return;
        }
        $csm  = self::getCsm3( $operand2 );
        $csmBody = ' /* this is body for case ' . __FUNCTION__ . ' ' . $case . ' */';
        $body0 = $csm->setBody( $csmBody )
            ->setDoWhileExprType()
            ->toArray();
        $fcnFrameMgr = FcnFrameMgr::init()
            ->setName( __FUNCTION__ . '_' . $case )
            ->setBody( $body0 );
        $code = $fcnFrameMgr->toString();

        $exp2 = is_scalar( $operand2 ) ? $operand2 : rtrim( $operand2->toString());
        $this->assertNotFalse(
            strpos( $code, $exp2 ),
            'Error in ' . __FUNCTION__ . ' ' . $case . '-1' . ' exp: ' . $exp2 . PHP_EOL . $code
        );

        if( DISPLAYffm ) {
            echo __FUNCTION__ . ' ' . $case . ' ->' . PHP_EOL . $code . '<-' . PHP_EOL;
        }
    }

    /**
     * Testing invalid scalar
     *
     * @test
     */
    public function ctrlStructMgrTest70()
    {
        try {
            CtrlStructMgr::init()
                ->setScalar( [ 'test' ] );
            $this->assertTrue( false );
        }
        catch( InvalidArgumentException $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * Testing without or only one (string) operand
     *
     * @test
     */
    public function ctrlStructMgrTest71()
    {
        // no operands, if/elseif
        try {
            CtrlStructMgr::init()
                ->setBody( ' /* this is body for case ' . __FUNCTION__ . ' */' )
                ->toArray();
            $this->assertTrue( false );
        }
        catch( RuntimeException $e ) {
            $this->assertTrue( true );
        }

        // no operands, switch/case/while/do-while
        try {
            CtrlStructMgr::init()
                ->setSwitchExprType()
                ->setBody( ' /* this is body for case ' . __FUNCTION__ . ' */' )
                ->toArray();
            $this->assertTrue( false );
        }
        catch( RuntimeException $e ) {
            $this->assertTrue( true );
        }

        // only operand1
        try {
            CtrlStructMgr::init()
                ->setIfExprType()
                ->setOperand1( 'test' )
                ->setBody( ' /* this is body for case ' . __FUNCTION__ . ' */' )
                ->toArray();
            $this->assertTrue( false );
        }
        catch( RuntimeException $e ) {
            $this->assertTrue( true );
        }
        // only operand2
        try {
            CtrlStructMgr::init()
                ->setElseIfExprType()
                ->setOperand2( 'test' )
                ->toArray();
            $this->assertTrue( false );
        }
        catch( RuntimeException $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * Testing invalid operands, string, EntityMgr or FcnInvokeMgr expected
     *
     * @test
     */
    public function ctrlStructMgrTest72()
    {
        $csm =
            CtrlStructMgr::init()
                ->setBody( ' /* this is body for case ' . __FUNCTION__ . ' */' );
        try {
            $csm->setOperand1( ClassMgr::init());
            $this->assertTrue( false );
        }
        catch( InvalidArgumentException $e ) {
            $this->assertTrue( true );
        }

        try {
            $csm->setOperand2( ClassMgr::init());
            $this->assertTrue( false );
        }
        catch( InvalidArgumentException $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * Testing invalid comparison operator
     *
     * @test
     */
    public function ctrlStructMgrTest73()
    {
        $csm =
            CtrlStructMgr::init()
                ->setBody( ' /* this is body for case ' . __FUNCTION__ . ' */' );
        try {
            $csm->setCompOP( false );
            $this->assertTrue( false );
        }
        catch( Throwable $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * Testing default comparison operator
     *
     * @test
     */
    public function ctrlStructMgrTest74()
    {
        $csm =
            CtrlStructMgr::init()
                ->setBody( ' /* this is body for case ' . __FUNCTION__ . ' */' );
        $this->assertEquals(
            CtrlStructMgr::EQ,
            $csm->getCompOP( true ),
            'Error in ' . __FUNCTION__
        );
    }

    /**
     * Testing invalid boolean variable, string expected
     *
     * @test
     */
    public function ctrlStructMgrTest75()
    {
        $csm =
            CtrlStructMgr::init()
                ->setBody( ' /* this is body for case ' . __FUNCTION__ . ' */' );
        try {
            $csm->setSingleOp( false );
            $this->assertTrue( false );
        }
        catch( InvalidArgumentException $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * Testing demo 1
     *
     * @test
     */
    public function ctrlStructMgrDemoTest1()
    {
        $code = FcnFrameMgr::init()
            ->setName( 'someFunction' )
            ->addArgument( 'value', FcnFrameMgr::STRING_T )
            ->setBody(
                CtrlStructMgr::factory(
                    EntityMgr::factory( null, 'value' ),
                    CtrlStructMgr::EQ,
                    1
                )
                    ->setBody( ' // this is if-body' )
                    ->toArray(),
                CtrlStructMgr::factory(
                    EntityMgr::factory( null, 'value' ),
                    CtrlStructMgr::GT,
                    1
                )
                    ->setElseIfExprType()
                    ->setBody( ' // this is elseIf-body' )
                    ->toArray(),
                CtrlStructMgr::init()
                    ->setElseExprType()
                    ->setBody( ' // this is else-body' )
                    ->toArray()
            )
            ->toString();
        $this->assertEquals(
            3,
            substr_count( $code, '$value' ),
            'Error in case ' . __FUNCTION__ . PHP_EOL . $code
        );
        if( DISPLAYffm ) {
            echo __FUNCTION__ . ' ->' . PHP_EOL . $code . '<-' . PHP_EOL;
        }
    }
    /**
     * Testing demo 2
     *
     * @test
     */
    public function ctrlStructMgrDemoTest2() {
        $code = FcnFrameMgr::init()
            ->setName( 'someFunction' )
            ->addArgument( 'value', FcnFrameMgr::STRING_T )
            ->setBody(
                CtrlStructMgr::init()
                    ->setScalar(true )
                    ->setSwitchExprType()
                    ->setBody(
                        CtrlStructMgr::factory(
                            EntityMgr::factory( null, 'value' ),
                            CtrlStructMgr::EQ,
                            1
                        )
                            ->setCaseExprType()
                            ->setBody( ' // this is case-body 1' )
                            ->toArray(),
                        CtrlStructMgr::factory(
                            EntityMgr::factory( null, 'value' ),
                            CtrlStructMgr::GT,
                            1
                        )
                            ->setCaseExprType()
                            ->setBody( ' // this is case-body 2' )
                            ->toArray(),
                        CtrlStructMgr::init()
                            ->setDefaultExprType()
                            ->setBody( ' // this is default-body' )
                            ->toArray()
                    ) // end setBody
                ->toArray()
            ) // end setBody
            ->toString();
        $this->assertEquals(
            3,
            substr_count( $code, '$value' ),
            'Error in case ' . __FUNCTION__ . PHP_EOL . $code
        );
        if( DISPLAYffm ) {
            echo __FUNCTION__ . ' ->' . PHP_EOL . $code . '<-' . PHP_EOL;
        }
    }
}
