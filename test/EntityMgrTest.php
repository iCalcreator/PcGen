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
        $prefixes = [ null, EntityMgr::PARENT_KW, EntityMgr::SELF_KW, EntityMgr::THIS_KW, '$class' ];
        foreach( $prefixes as $ix => $prefix ) {
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
            if( DISPLAYem ) {
                echo __FUNCTION__ . ' ' . $ix . 'A ' . $code . PHP_EOL;
            }

            $expected = empty( $prefix ) ? 'VARIABLE' : $prefix . '::VARIABLE';
            $code     = EntityMgr::factory( $prefix, 'variable' )->setIsConst( true )->toString();
            $this->assertEquals(
                $expected,
                $code,
                'case' . ' B-' . $ix . ' expected: ' . $expected . '  result : ' . trim( $code )
            );

            if( DISPLAYem ) {
                echo __FUNCTION__ . ' ' . $ix . 'A ' . $code . PHP_EOL;
            }
        } // end foreach
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

    /**
     * @test
     */
    public function AssignClauseMgrTest82() {
        $this->assertTrue(
            is_string(
                EntityMgr::factory( 'klass', 'variable', 82 )
                    ->__toString()
            )
        );
    }

    /**
     * @test
     */
    public function AssignClauseMgrTest91() {
        $test = EntityMgr::factory( null, 'boole' )->toString();
        $this->assertEquals(
            '$boole',
            $test,
            '\'@boole\' exp, got : ' . var_export( $test, true )
        );
    }

}
