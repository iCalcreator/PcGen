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

class BaseBTest extends TestCase
{

    /**
     * Testing body
     *
     * @test
     */
    public function baseBTest1() {
        $eol = "\r\n";
        $ffg = FcnFrameMgr::init( $eol, '  ' )
            ->setBaseIndent()
            ->setVisibility()
            ->setName( 'halloWorld' )
            ->setArguments( [ 'argument' ] )
            ->setBody(
                ' /* body row 1 */',
                [
                    ' /* body row 2 */',
                    ' /* body row 3 */',
                    ' /* body row 4 */',
                ],
                ' /* body row 5 */',
                [
                    ' /* body row 6 */',
                    ' /* body row 7 */',
                    ' /* body row 8 */',
                ],
                ' /* body row 9 */',
                [
                    ' /* body row 10 */',
                    ' /* body row 11 */',
                    ' /* body row 12 */',
                ]
            );
        $output = $ffg->toString();

        $this->assertEquals(
            'function halloWorld( $argument ) {' . $eol .
            '   /* body row 1 */' . $eol .
            '   /* body row 2 */' . $eol .
            '   /* body row 3 */' . $eol .
            '   /* body row 4 */' . $eol .
            '   /* body row 5 */' . $eol .
            '   /* body row 6 */' . $eol .
            '   /* body row 7 */' . $eol .
            '   /* body row 8 */' . $eol .
            '   /* body row 9 */' . $eol .
            '   /* body row 10 */' . $eol .
            '   /* body row 11 */' . $eol .
            '   /* body row 12 */' . $eol .
            '}' . $eol,
            $output
        );
        $ffg->setEol( PHP_EOL );
        $ffg->setIndent( '    ' );
        $ffg->setBaseIndent( '    ' );
    }

}
