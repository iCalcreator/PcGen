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

use PHPUnit\Framework\TestCase;

class BaseATest extends TestCase
{

    /**
     * Testing eol
     *
     * @test
     */
    public function baseATest1() {
        $vm = VariableMgr::init();

        $this->assertEquals(
            "\n\r",
            $vm->setEol( "\n\r" )->getEol()
        );

        $this->assertEquals(
            PHP_EOL,
            $vm->setEol( PHP_EOL )->getEol()
        );

        $this->assertEquals(
            PHP_EOL,
            $vm->setEol( '' )->getEol()
        );

    }

    /**
     * Testing set indent
     *
     * @test
     */
    public function baseATest2() {
        $vm = VariableMgr::init();

        $this->assertEquals(
            '    public $theVariableName11 = null;' . PHP_EOL,
            $vm->setName( 'theVariableName11' )->toString()
        );

        $vm = VariableMgr::init()->setbaseIndent( '  ' );
        $this->assertEquals(
            '  protected $theVariableName12 = null;' . PHP_EOL,
            $vm->setName( 'theVariableName12' )->setVisibility( VariableMgr::PROTECTED_ )->toString()
        );

        $eol    = "\r\n";
        $indent = '   ';
        $vm = VariableMgr::init( $eol, $indent );
        $this->assertEquals( $eol, $vm->getEol());
        $this->assertEquals( $indent, $vm->getIndent());

        $vm->seteol( PHP_EOL ); // reset
        $vm->setIndent( '    ' ); // reset indent
        $vm->setBaseIndent( '    ' ); // reset baseIndent

    }

}
