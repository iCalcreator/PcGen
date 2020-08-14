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

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class SimpleCondMgrTest extends TestCase
{
    public static function simpleCondMgrTest1DataProvider() {
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
     * Testing operand1/operand2 condition
     *
     * @test
     * @dataProvider simpleCondMgrTest1DataProvider
     * @param int    $case
     * @param bool|float|int|string|EntityMgr $operand1
     * @param bool|float|int|string|EntityMgr $operand2
     */
    public function simpleCondMgrTest1( $case, $operand1, $operand2 ) {
        $scm = SimpleCondMgr::init()
            ->setCompOP( array_rand( array_flip( SimpleCondMgr::getCondOPs())))
            ->setOperand2( $operand2 );
        if(( $operand1 instanceof EntityMgr ) &&
            ( EntityMgr::THIS_KW == $operand1->getClass())) {
            $scm->setThisVarOperand1( $operand1->getVariable());
        }
        else {
            $scm->setOperand1( $operand1 );
        }
        if(( $operand2 instanceof EntityMgr ) &&
            ( EntityMgr::THIS_KW == $operand2->getClass())) {
            $scm->setThisVarOperand2( $operand2->getVariable());
        }
        else {
            $scm->setOperand2( $operand2 );
        }
        $body = $scm->setBody( ' /* this is a simpler condition body for case . ' . __FUNCTION__ . ' ' . $case . ' */' )
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
        if( $operand1 instanceof EntityMgr ) {
            $this->assertTrue(
                ( $scm->getOperand1() instanceof EntityMgr ),
                'Error in ' . __FUNCTION__ . ' ' . $case . '-2' . PHP_EOL . $code
            );
        }
        $this->assertNotFalse(
            strpos( $code, $exp2 ),
            'Error in ' . __FUNCTION__ . ' ' . $case . '-3' . PHP_EOL . $code
        );
        if( $operand2 instanceof EntityMgr ) {
            $this->assertTrue(
                ( $scm->getOperand2() instanceof EntityMgr ),
                'Error in case ' . $case . '-4' . ' ->' . PHP_EOL . $code . '<-' . PHP_EOL
            );
        }

        if( DISPLAYffm ) {
            echo __FUNCTION__ . ' ' . $case . ' ->' . PHP_EOL . $code . '<-' . PHP_EOL;
        }
    }

    /**
     * Testing bool condition
     *
     * @test
     * @dataProvider simpleCondMgrTest1DataProvider
     * @param int    $case
     * @param bool|float|int|string|EntityMgr $operand1
     * @param bool|float|int|string|EntityMgr $operand2
     */
    public function simpleCondMgrTest2( $case, $operand1, $operand2 ) {
        if( $operand2 instanceof FcnInvokeMgr ) {
            $this->assertTrue( true );
            return;
        }
        $scm =
            SimpleCondMgr::init()
                ->setBody( ' /* this is a simpler condition body for case . ' . __FUNCTION__ . ' ' . $case . ' */' );
        if(( $operand2 instanceof EntityMgr ) &&
            ( EntityMgr::THIS_KW == $operand2->getClass())) {
            $scm->setBoolThisVar( $operand2->getVariable());
        }
        else {
            $scm->setBoolVar( $operand2 );
        }
        $body = $scm->toArray();
        $fcnFrameMgr = FcnFrameMgr::init()
            ->setName( __FUNCTION__ . '_' . $case )
            ->setBody( $body );
        $code = $fcnFrameMgr->toString();

        $this->assertTrue(
            ( $scm->getBoolVar() instanceof EntityMgr ),
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
     * Testing bool condition
     *
     * @test
     * @dataProvider simpleCondMgrTest1DataProvider
     * @param int    $case
     * @param bool|float|int|string|EntityMgr $operand1
     * @param bool|float|int|string|EntityMgr $operand2
     */
    public function simpleCondMgrTest3( $case, $operand1, $operand2 ) {
        if( ! $operand2 instanceof FcnInvokeMgr ) {
            $this->assertTrue( true );
            return;
        }
        $scm = SimpleCondMgr::init()
            ->setBody( ' /* this is a simpler condition body for case . ' . __FUNCTION__ . ' ' . $case . ' */' )
            ->setBoolVar( $operand2 );

        if( EntityMgr::THIS_KW == $operand2->getName()->getClass()) {
            $scm->setBoolThisFcn( $operand2->getName()->getVariable());
        }
        else {
            $scm->setBoolVar( $operand2 );
        }
//      $body = $scm->toString();
        $body = $scm->toArray();

        $fcnFrameMgr = FcnFrameMgr::init()
            ->setName( __FUNCTION__ . '_' . $case . '_1' )
            ->setBody( $body );
        $code = $fcnFrameMgr->toString();

        $exp2 = is_scalar( $operand2 ) ? $operand2 : rtrim( $operand2->toString());
        $this->assertNotFalse(
            strpos( $code, $exp2 ),
            'Error in case ' . __FUNCTION__ . '-2' . PHP_EOL . $code
        );

        if( DISPLAYffm ) {
            echo __FUNCTION__ . ' ' . $case . ' ->' . PHP_EOL . $code . '<-' . PHP_EOL;
        }
    }

    /**
     * Testing without or only one (string) operand
     *
     * @test
     */
    public function simpleCondMgrTest11() {
        $scm =
            SimpleCondMgr::init()
                ->setBody( ' /* this is a simpler condition body for case . ' . __FUNCTION__ . ' */' );
        // no operands
        try {
            $scm->toArray();
            $this->assertTrue( false );
        }
        catch( RuntimeException $e ) {
            $this->assertTrue( true );
        }

        // only operand1
        $scm->setOperand1( 'test' );
        try {
            $scm->toArray();
            $this->assertTrue( false );
        }
        catch( RuntimeException $e ) {
            $this->assertTrue( true );
        }
        // only operand2
        $scm =
            SimpleCondMgr::init()
                ->setBody( ' /* this is a simpler condition body for case . ' . __FUNCTION__ . ' */' );
        $scm->setOperand2( 'test' );
        try {
            $scm->toArray();
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
    public function simpleCondMgrTest12() {
        $scm =
            SimpleCondMgr::init()
                ->setBody( ' /* this is a simpler condition body for case . ' . __FUNCTION__ . ' */' );
        try {
            $scm->setOperand1( ClassMgr::init());
            $this->assertTrue( false );
        }
        catch( InvalidArgumentException $e ) {
            $this->assertTrue( true );
        }

        try {
            $scm->setOperand2( ClassMgr::init());
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
    public function simpleCondMgrTest13() {
        $scm =
            SimpleCondMgr::init()
                ->setBody( ' /* this is a simpler condition body for case . ' . __FUNCTION__ . ' */' );
        try {
            $scm->setCompOP( false );
            $this->assertTrue( false );
        }
        catch( InvalidArgumentException $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * Testing default comparison operator
     *
     * @test
     */
    public function simpleCondMgrTest14() {
        $scm =
            SimpleCondMgr::init()
                ->setBody( ' /* this is a simpler condition body for case . ' . __FUNCTION__ . ' */' );
        $this->assertEquals(
            SimpleCondMgr::EQ,
            $scm->getCompOP( true ),
            'Error in ' . __FUNCTION__
        );
    }

    /**
     * Testing invalid boolean variable, string expected
     *
     * @test
     */
    public function simpleCondMgrTest15() {
        $scm =
            SimpleCondMgr::init()
                ->setBody( ' /* this is a simpler condition body for case . ' . __FUNCTION__ . ' */' );
        try {
            $scm->setBoolVar( false );
            $this->assertTrue( false );
        }
        catch( InvalidArgumentException $e ) {
            $this->assertTrue( true );
        }
    }
}
