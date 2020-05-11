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

class BaseATest extends TestCase
{

    /**
     * Testing eol
     *
     * @test
     */
    public function baseATest1() {
        VariableMgr::setDefaultEol( "\n\r" );
        $this->assertEquals(
            "\n\r",
            VariableMgr::init()->getEol()
        );

        $this->assertEquals(
            PHP_EOL,
            VariableMgr::init()->setEol( PHP_EOL )->getEol()
        );
        $this->assertEquals(
            "\n\r",
            VariableMgr::init()->getEol()
        );
        VariableMgr::setDefaultEol( PHP_EOL );

        $this->assertEquals(
            PHP_EOL,
            VariableMgr::init()->setEol( '' )->getEol()
        );

        try {
            $vm = VariableMgr::init()->setEol( 'error' );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * Testing indent
     *
     * @test
     */
    public function baseATest2() {

        VariableMgr::setDefaultIndent( '  ' );
        $this->assertEquals(
            '  ',
            VariableMgr::init()->getIndent()
        );

        $this->assertEquals(
            '    ',
            VariableMgr::init()->setIndent( '    ' )->getIndent()
        );
        $this->assertEquals(
            '  ',
            VariableMgr::init()->getIndent()
        );
        VariableMgr::setDefaultIndent( '    ' );

        $this->assertEquals(
            '    ',
            VariableMgr::init()->getIndent()
        );

        $this->assertEquals(
            '',
            VariableMgr::init()->setIndent()->getIndent()
        );
        $this->assertEquals(
            '',
            VariableMgr::init()->setIndent( null )->getIndent()
        );
        try {
            $vm = VariableMgr::init()->setIndent( 'error' );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
        try {
            $vm = VariableMgr::init()->setIndent( "\t" );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * Testing baseIndent
     *
     * @test
     */
    public function baseATest3() {
        VariableMgr::setDefaultBaseIndent( '  ' );
        $this->assertEquals(
            '  ',
            VariableMgr::init()->getBaseIndent()
        );

        $this->assertEquals(
            '    ',
            VariableMgr::init()->setBaseIndent( '    ' )->getBaseIndent()
        );
        $this->assertEquals(
            '  ',
            VariableMgr::init()->getBaseIndent()
        );
        VariableMgr::setDefaultBaseIndent( '    ' );

        $this->assertEquals(
            '    ',
            VariableMgr::init()->getBaseIndent()
        );
        $this->assertEquals(
            '',
            VariableMgr::init()->setBaseIndent()->getBaseIndent()
        );
        $this->assertEquals(
            '',
            VariableMgr::init()->setBaseIndent( null )->getBaseIndent()
        );
        try {
            $vm = VariableMgr::init()->setBaseIndent( 'error' );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
        try {
            $vm = VariableMgr::init()->setBaseIndent( "\t" );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * Testing PHP version
     *
     * @test
     */
    public function baseATest4() {
        VariableMgr::setTargetPhpVersion( PHP_VERSION );
        $this->assertEquals(
            PHP_VERSION,
            VariableMgr::getTargetPhpVersion()
        );
    }

    /**
     * Testing baseA __construct
     *
     * @test
     */
    public function baseATest5() {
        $eol = "\n\r";
        $ind = '   ';
        $vm1 = new VariableMgr( $eol, $ind, $ind );
        $this->assertEquals(
            $eol,
            $vm1->getEol()
        );
        $this->assertEquals(
            $ind,
            $vm1->getIndent()
        );
        $this->assertEquals(
            $ind,
            $vm1->getBaseIndent()
        );
        $vm2 = VariableMgr::init( $vm1 );
        $this->assertEquals(
            $eol,
            $vm2->getEol()
        );
        $this->assertEquals(
            $ind,
            $vm2->getIndent()
        );
        $this->assertEquals(
            $ind,
            $vm2->getBaseIndent()
        );
        $this->assertEquals(
            get_class( $vm2 ) . ' baseIndent =>   <-  indent =>   <-',
            $vm2->showIndents()
        );
    }

}
