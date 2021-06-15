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

class FileMgrTest extends TestCase
{
    /**
     * Testing file docBlock and body
     *
     * @test
     */
    public function fileMgrTest1() {
        static $fileSummary = 'fileSummary';
        static $fileDescr1  = 'fileDescription1';
        static $fileDescr2  = 'fileDescription2';
        $fm = FileMgr::init();
        $fm->setInfo( $fileSummary, [ $fileDescr1, $fileDescr2 ] );
        $docBlocks   = $fm->getDocBlock(); // return array
        $docBlocks[0]->setTag( DocBlockMgr::PACKAGE_T, FileMgr::class );
        $docBlocks[] = DocBlockMgr::init()->setInfo( 'summary 2', 'description 2' );
        $fm->setDocBlock( $docBlocks );
        $fm->setFileBody(
            ClassMgr::init()
                ->setName( 'testClass' )
                ->addProperty( PropertyMgr::factory( 'propertyName', PropertyMgr::STRING_T ))
        );
        $fm->setBody( '/* This is a class post docblock */' );
        $code = $fm->toString();

        $this->assertNotFalse(
            strpos( $code, $fileSummary ),
            'Error 11' . PHP_EOL . $code
        );
        $this->assertNotFalse(
            strpos( $code, $fileDescr1 ),
            'Error 12' . PHP_EOL . $code
        );
        foreach( array_keys( $docBlocks ) as $dbIx ) {
            $this->assertTrue(
                $docBlocks[$dbIx] instanceof DocBlockMgr,
                'Error 13' . PHP_EOL . $code
            );
        }

        $this->assertNotFalse(
            strpos( $code, 'private $propertyName' ),
            'Error 12' . PHP_EOL . $code
        );

        if( DISPLAYcm ) {
            echo __FUNCTION__ . ', starts after eol ->' . PHP_EOL . $code . '<- ends here' . PHP_EOL;
        }
    }

    /**
     * Testing invalid docBlocks
     *
     * @test
     */
    public function fileMgrTest2() {
        $fm = FileMgr::init();
        try {
            $fm->setDocBlock( 'false' );
            $this->assertTrue( false );
        }
        catch( InvalidArgumentException $e ) {
            $this->assertTrue( true );
        }
        try {
            $fm->setDocBlock( ClassMgr::init() );
            $this->assertTrue( false );
        }
        catch( InvalidArgumentException $e ) {
            $this->assertTrue( true );
        }
        try {
            $fm->setDocBlock( [ 'false' ] );
            $this->assertTrue( false );
        }
        catch( InvalidArgumentException $e ) {
            $this->assertTrue( true );
        }
    }
}
