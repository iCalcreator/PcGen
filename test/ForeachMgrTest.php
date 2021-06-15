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

if( ! in_array( __DIR__ . '/FimDataProviderTrait.php', get_included_files())) {
    include( __DIR__ . '/FimDataProviderTrait.php' );
}

class ForeachMgrTest extends TestCase
{
    use FimDataProviderTrait; // FcnInvokeMgrTest3ArgumentProvider + FcnInvokeMgrFunctionProvider

    public static function foreachMgrTest1DataProvider() : array
    {
        $testData   = [];
        $iterators  = [];

        $testData[] = [
            '101',
            'stringSource',
            null,
            'element'
        ];

        $cnt = 200;
        $iterators[++$cnt] = EntityMgr::factory( null, 'property' );
        $iterators[++$cnt] = EntityMgr::factory( EntityMgr::SELF_KW, 'property' );
        $iterators[++$cnt] = EntityMgr::factory( EntityMgr::THIS_KW, 'property' );
        $iterators[++$cnt] = EntityMgr::factory( '$class', 'property' );
        $iterators[++$cnt] = EntityMgr::factory( '$class', 'property' )
            ->setIsStatic( true );
        $iterators[++$cnt] = EntityMgr::factory( 'fqcn', 'property' );

        $cnt = 300;
        foreach( self::FcnInvokeMgrFunctionProvider() as $function ) {
            $iterators[++$cnt] = FcnInvokeMgr::factory( $function[1], $function[2] );
        }

        foreach( $iterators as $sIx => $source ) {
            foreach( [ null, 'key' ] as $k2x => $key ) {
                $testData[] = [
                    $sIx . '_' . ( $k2x + 1 ),
                    $source,
                    $key,
                    (( 1 == array_rand( [ 0, 1 ] )) ? 'element' : null )
                ];
            }
        }

        return $testData;
    }

     /**
      * Testing ForeachMgr
      *
      * @test
      * @dataProvider foreachMgrTest1DataProvider
      *
      * @param string $case
      * @param mixed $iterator    i.e. name
      * @param string $key        i.e. name
      * @param string $value      i.e. name
      */
     public function foreachMgrTest1( string $case, $iterator, $key, $value )
    {
        $source = is_object( $iterator ) ? trim( $iterator->toString()) : ForeachMgr::VARPREFIX . $iterator;

        $fcnFrameMgr = FcnFrameMgr::init()
            ->setName( __FUNCTION__ . '_' . $case )
            ->setBody(
                ForeachMgr::factory( $iterator, $key, $value )
                    ->setBody(
                        ' // this is a foreach body for case ' . __FUNCTION__ . '-' . $case,
                        ' // expects source : ' . $source
                    )
                    ->toArray()
            );
        $code = $fcnFrameMgr->toString();

        $this->assertNotFalse(
            strpos( $code, $source ),
            'Error in ' . __FUNCTION__ . ' ' . $case . '-1' . PHP_EOL . $code
        );
        if( null != $key ) {
            $this->assertNotFalse(
                strpos( $code, $key ),
                'Error in ' . __FUNCTION__ . ' ' . $case . '-2' . PHP_EOL . $code
            );
        }
        $this->assertNotFalse(
            strpos( $code, ( empty( $value) ? 'value' : 'element' )),
            'Error in ' . __FUNCTION__ . ' ' . $case . '-3' . PHP_EOL . $code
        );

        if( ! empty( $argument )) {
            static $EXP3  = '( $';
            static $EXP4  = '()' . PHP_EOL;
            $this->assertTrue(
                (( false !== strpos( $code, $EXP3  )) || ( false !== strpos( $code, $EXP4  ))),
                'Error in ' . __FUNCTION__ . ' ' . $case . '-4' . PHP_EOL . $code
            );
        }

        if( DISPLAYffm ) {
            echo __FUNCTION__ . ' ' . $case . ' ->' . PHP_EOL . $code . '<-' . PHP_EOL;
        }
    }

    /**
     * @test
     */
    public function foreachMgrTest12() {
        try {
            ForeachMgr::factory()->toArray();
            $this->assertTrue( false );
        }
        catch( RuntimeException $e ) {
            $this->assertTrue( true );
        }
        try {
            ForeachMgr::factory('iterator' )->toArray();
            $this->assertTrue( false );
        }
        catch( RuntimeException $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * @test
     */
    public function foreachMgrTest13() {
        try {
            ForeachMgr::factory()->setIterator( false )->toArray();
            $this->assertTrue( false );
        }
        catch( InvalidArgumentException $e ) {
            $this->assertTrue( true );
        }
        try {
            ForeachMgr::factory()->setIterator( ClassMgr::init())->toArray();
            $this->assertTrue( false );
        }
        catch( InvalidArgumentException $e ) {
            $this->assertTrue( true );
        }
    }
}
