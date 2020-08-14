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

class TryCatchMgrTest extends TestCase
{
    /**
     * Test TryCatchMgr::factory
     *
     * @test
     */
    public function tryCatchMgrTest11()
    {
        $bodyCode = ' /* here comes some code.... */';
        $code = TryCatchMgr::factory( $bodyCode, $bodyCode )
            ->toString();
        $this->assertNotFalse(
            strpos( $code, CatchMgr::EXCEPTION ),
            'expects ' . CatchMgr::EXCEPTION . ' but got ' . PHP_EOL . $code
        );
        if( DISPLAYffm ) {
            echo __FUNCTION__ . ' ->' . PHP_EOL . $code . '<-' . PHP_EOL;
        }
    }

    /**
     * Test TryCatchMgr::factory
     *
     * @test
     */
    public function tryCatchMgrTest12()
    {
        $bodyCode = ' /* here comes some code.... */';
        $code = TryCatchMgr::init()
            ->toString();
        $this->assertNotFalse(
            strpos( $code, CatchMgr::EXCEPTION ),
            'expects ' . CatchMgr::EXCEPTION . ' but got ' . PHP_EOL . $code
        );
        if( DISPLAYffm ) {
            echo __FUNCTION__ . ' ->' . PHP_EOL . $code . '<-' . PHP_EOL;
        }
    }

    /**
     * Test TryCatchMgr::setCatch
     *
     * @test
     */
    public function tryCatchMgrTest21()
    {
        $bodyCode = ' /* here comes some code.... */';
        $code = TryCatchMgr::init()
            ->setBody( $bodyCode)
            ->setCatch(
                [
                    [ CatchMgr::INVALIDARGUMENTEXCEPTION, $bodyCode ],
                    CatchMgr::factory( 'LogicException', $bodyCode ),
                    [ CatchMgr::RUNTIMEEXCEPTION, $bodyCode ],
                    CatchMgr::EXCEPTION
                ]
            )
            ->toString();
        $cnt = substr_count( $code, CatchMgr::EXCEPTION );
        $this->assertEquals(
            4,
            $cnt,
            'expects 4 ' . CatchMgr::EXCEPTION . ' but got ' . $cnt
        );
        if( DISPLAYffm ) {
            echo __FUNCTION__ . ' ->' . PHP_EOL . $code . '<-' . PHP_EOL;
        }
    }

    /**
     * Testing appendCatch exception
     *
     * @test
     */
    public function ctrlStructMgrTest75()
    {
        try {
            TryCatchMgr::init()->appendCatch( 1.2345 );
            $this->assertTrue( false );
        }
        catch( InvalidArgumentException $e ) {
            $this->assertTrue( true );
        }
        try {
            TryCatchMgr::init()->appendCatch( classMgr::init() );
            $this->assertTrue( false );
        }
        catch( InvalidArgumentException $e ) {
            $this->assertTrue( true );
        }
    }
    /**
     * Testing setCatch exception
     *
     * @test
     */
    public function ctrlStructMgrTest76()
    {
        try {
            TryCatchMgr::init()->setCatch( [ 1.2345 ] );
            $this->assertTrue( false );
        }
        catch( InvalidArgumentException $e ) {
            $this->assertTrue( true );
        }
        try {
            TryCatchMgr::init()->setCatch( [ classMgr::init() ] );
            $this->assertTrue( false );
        }
        catch( InvalidArgumentException $e ) {
            $this->assertTrue( true );
        }
    }
}
