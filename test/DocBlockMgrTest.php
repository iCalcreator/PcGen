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

class DocBlockMgrTest extends TestCase
{

    const SUMMARY = 'This is a top (short) description';
    const DESCR1  = 'This is a longer description';
    const DESCR2a = 'This is another longer description';
    const DESCR2b = 'with more info on the next row';
    const DESCR2  = [
        '',
        self::DESCR2a,
        self::DESCR2b,
        ''
    ];
    const PARAMETER = 'parameter';
    const QUANTITY  = 'quantity';

    const TEXTS = [
        self::SUMMARY,
        self::DESCR1,
        self::DESCR2b,
        DocBlockMgr::PARAM_T,
        DocBlockMgr::STRING_T,
        DocBlockMgr::INT_T,
        self::PARAMETER,
        self::QUANTITY,
        DocBlockMgr::RETURN_T,
        DocBlockMgr::ARRAY_T,
        DocBlockMgr::PACKAGE_T,
        __NAMESPACE__
    ];

    /**
     * README example
     *
     * @test
     */
    public function docBlockGenDemoTest0() {
        $dbm = DocBlockMgr::init()
                // set indent to 0, default four spaces
                ->setIndent()
                // set eol, default PHP_EOL
                ->setEol( PHP_EOL )

                // set top summary
                // ->setSummary( 'This is a top (shorter) summary' )
                // set longer description (string)
                // ->setDescription( 'This is a longer description' )
                ->setInfo(
                        'This is a top (shorter) summary',
                        'This is a longer description'
                )

                // set another longer description (array)
                ->setDescription(
                    [
                        'This is another longer description',
                        'with more info on the next row'
                    ]
                )
                // set tags using DocBlockGenInterface constants
                ->setTag( DocBlockMgr::RETURN_T, DocBlockMgr::ARRAY_T )
                ->setTag( DocBlockMgr::PACKAGE_T, __NAMESPACE__ );
            // string output (with row trailing eols)
        $codeDocBlock = $dbm->toString();
        $this->assertStringEndsWith( PHP_EOL, $codeDocBlock );
        $this->assertTrue( $dbm->isSummarySet());
        $this->assertTrue( $dbm->isTagSet( DocBlockMgr::PACKAGE_T ));

        if( DISPLAYdbm ) {
            echo __FUNCTION__ . ' : ' . PHP_EOL . $codeDocBlock . PHP_EOL;
        }
    }

    /**
     * @test
     */
    public function docBlockMgrTest1() {
        $code = $this->docBlockGenProcess(
            DocBlockMgr::factory( DocBlockMgr::AUTHOR_T, 'Kjell-Inge Gustafsson' )
                ->setIndent()
                ->setEol( PHP_EOL )
        );
        $this->docBlockMgrTester( $code );
    }

    /**
     * @test
     */
    public function docBlockMgrTest2() {
        $code = $this->docBlockGenProcess(
            DocBlockMgr::init( PHP_EOL, '' )
                ->setTag( DocBlockMgr::AUTHOR_T, 'Kjell-Inge Gustafsson' )
        );
        $this->docBlockMgrTester( $code );

        if( DISPLAYdbm ) {
            echo __FUNCTION__ . ' : ' . PHP_EOL . $code . PHP_EOL;
        }
    }

    /**
     * @param DocBlockMgr $dbg
     * @return string
     */
    public function docBlockGenProcess( DocBlockMgr $dbg ) : string
    {

        return $dbg->setSummary( self::SUMMARY )

            // set longer description (string)
            ->setDescription( self::DESCR1 )

            // set another longer description (array)
            ->setDescription( self::DESCR2 )

            ->setTag(
                DocBlockMgr::PARAM_T,
                [ DocBlockMgr::STRING_T, DocBlockMgr::STRINGARRAY_T ],
                self::PARAMETER
            )

            ->setTag(
                DocBlockMgr::PARAM_T,
                DocBlockMgr::INT_T,
                self::QUANTITY
            )

            ->setTag( DocBlockMgr::RETURN_T, DocBlockMgr::ARRAY_T )

            ->setTag( DocBlockMgr::PACKAGE_T, __NAMESPACE__ )

            ->toString();
    }

    /**
     * @param string $code
     */
    public function docBlockMgrTester( string $code ) {

        $this->assertStringEndsWith( PHP_EOL, $code);

        foreach( self::TEXTS as $text ) {
            $this->assertNotFalse(
                strpos( $code, (string) $text ),
                'text NOT found : ' . $text . ' in ' . PHP_EOL . $code
            );
        }
    }

    /**
     * @test
     */
    public function docBlockMgrTest3() {
        try {
            DocBlockMgr::init( DocBlockMgr::class );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * @test
     */
    public function docBlockMgrTest4() {
        $this->assertTrue( DocBlockMgr::isValidTagName( DocBlockMgr::PARAM_T ));
        $this->assertFalse( DocBlockMgr::isValidTagName( DocBlockMgr::class ) );
    }

    /**
     * @test
     */
    public function docBlockMgrTest5() {
        try {
            DocBlockMgr::init()->setTag( __NAMESPACE__ );
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * @test
     */
    public function docBlockMgrDemoTest() {
        $code = DocBlockMgr::init()
            ->setSummary( 'Summary' )
            ->setDescription( 'Decription 1' )
            ->setDescription( [ 'Description 2', 'some text here...'] )
            ->setTag(
                DocBlockMgr::PARAM_T,
                [ DocBlockMgr::STRING_T, DocBlockMgr::STRINGARRAY_T ],
                self::PARAMETER
            )
            ->setTag(
                DocBlockMgr::PARAM_T,
                DocBlockMgr::INT_T,
                self::QUANTITY
            )
            ->setTag( DocBlockMgr::RETURN_T, DocBlockMgr::ARRAY_T )
            ->toString();

        $this->assertNotFalse(
            strpos( $code, 'Summary' )
        );
        if( DISPLAYdbm ) {
            echo __FUNCTION__ . ' : ' . PHP_EOL . $code . PHP_EOL;
        }
    }

}
