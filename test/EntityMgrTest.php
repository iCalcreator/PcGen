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

class EntityMgrTest extends TestCase
{
    /**
     * @test
     */
    public function EntityMgrTest1() {

        $this->assertEquals(
            '$property',
            EntityMgr::factory( null, 'property' )
                ->toString()
        );

        $this->assertEquals(
            'self::$property',
            EntityMgr::factory( EntityMgr::SELF_KW, 'property' )
                ->toString()
        );

        $this->assertEquals(
            '$this->property',
            EntityMgr::factory( EntityMgr::THIS_KW, 'property' )
                ->toString()
        );

        $this->assertEquals(
            '$class->property',
            EntityMgr::factory( '$class', 'property' )
                ->toString()
        );

        $this->assertEquals(
            '$class::$property',
            EntityMgr::factory( '$class', 'property' )
                ->setIsStatic( true )
                ->toString()
        );
        $this->assertEquals(
            'fqcn::$property',
            EntityMgr::factory( 'fqcn', 'property' )
                ->toString()
        );

    }

    /**
     * @test
     */
    public function EntityMgrTest36() {
        foreach( [ null, EntityMgr::PARENT_KW, EntityMgr::SELF_KW, EntityMgr::THIS_KW, '$class' ] as $ix => $prefix ) {
            switch( true ) {
                case empty( $prefix ) :
                    $expected = '$variable';
                    break;
                case ( EntityMgr::THIS_KW == $prefix ) :
                    $expected = '$this->variable';
                    break;
                case ( '$class' == $prefix ) :
                    $expected = '$class->variable';
                    break;
                default :
                    $expected = $prefix . '::$variable';
                    break;
            } // end switch
            $code     = EntityMgr::factory( $prefix, 'variable' )->toString();
            $this->assertEquals(
                $expected,
                $code,
                'case' . ' A-' . $ix . ' expected: ' . $expected . '  result : ' . trim( $code )
            );

            $expected = empty( $prefix ) ? 'VARIABLE' : $prefix . '::VARIABLE';
            $code     = EntityMgr::factory( $prefix, 'variable' )->setIsConst( true )->toString();
            $this->assertEquals(
                $expected,
                $code,
                'case' . ' B-' . $ix . ' expected: ' . $expected . '  result : ' . trim( $code )
            );

        } // end foreach

        if( DISPLAYem ) {
            echo __FUNCTION__ . ' ' . $ix . ' ' . $code . PHP_EOL;
        }

    }

    /**
     * @test
     */
    public function AssignClauseMgrTest72() {
        try {
            EntityMgr::init()->setIndex( 1.2345 );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

}
