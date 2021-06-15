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

if( ! in_array( __DIR__ . '/AcmDataProviderTrait.php', get_included_files())) {
    include( __DIR__ . '/AcmDataProviderTrait.php' );
}
if( ! in_array( __DIR__ . '/FimDataProviderTrait.php', get_included_files())) {
    include( __DIR__ . '/FimDataProviderTrait.php' );
}

/**
 * Class TernaryNullCoalesceMgrTest, testing TernaryNullCoalesceMgr
 *
 * @package Kigkonsult\PcGen
 */
class TernaryNullCoalesceMgrTest extends TestCase
{

    use AcmDataProviderTrait; // getTargetArr

    use FimDataProviderTrait; // FcnInvokeMgrTest3ArgumentProvider + FcnInvokeMgrFunctionProvider

    /*
     * sourcePrefix      (scope)   sourceObject (type) = target entity
     * null                         $-prefixed string (opt with sourceIndex) ie variable
     * self                ::       $-prefixed string (opt with sourceIndex) class (static) variable
     * this                ->       string (property, opt with sourceIndex)
     * otherClass (fqcn)   ::       $-prefixed string (opt with sourceIndex) class (static) variable
     * $class              ::       $-prefixed string (opt with sourceIndex) class (static) variable
     * $class              ->       string (opt with sourceIndex), NOT accepted here (class with public property)
     * @return array
     */
    public static function getExprVarArr() : array
    {
        $testData = [];

        $testData[] = [
            //  null + $-prefixed string
            211,
            null,
            '$variable211',
            null,
            '$variable211'
        ];

        $testData[] = [
            //  null + $-prefixed variable[]
            212,
            null,
            '$variable212',
            AssignClauseMgr::ARRAY2_T,
            '$variable212[]'
        ];

        $testData[] = [
            //  null + $-prefixed variable[0]
            213,
            null,
            '$variable213',
            0,
            '$variable213[0]'
        ];

        $testData[] = [
            //  null + $-prefixed variable[14]
            214,
            null,
            '$variable214',
            214,
            '$variable214[214]'
        ];

        $testData[] = [
            //  null + $-prefixed variable[index]
            215,
            null,
            '$variable215',
            'index',
            '$variable215[$index]'
        ];

        $testData[] = [
            //  4x self class + $-prefixed string
            222,
            AssignClauseMgr::SELF_KW,
            '$var222',
            null,
            'self::$var222'
        ];

        $testData[] = [
            //  4x self class + num index
            223,
            AssignClauseMgr::SELF_KW,
            '$var223',
            223,
            'self::$var223[223]'
        ];

        $testData[] = [
            //  4x self class + $-prefixed string (with subjectIndex)
            224,
            AssignClauseMgr::SELF_KW,
            '$var224',
            '$index224',
            'self::$var224[$index224]'
        ];

        $testData[] = [
            //  4x self class + $-prefixed string (with subjectIndex)
            225,
            AssignClauseMgr::SELF_KW,
            '$var225',
            '225',
            'self::$var225[225]'
        ];

        $testData[] = [
            // 2x this   string property
            231,
            AssignClauseMgr::THIS_KW,
            'string231',
            null,
            '$this->string231'
        ];

        $testData[] = [
            // 2x this   string (property with subjectIndex)
            232,
            AssignClauseMgr::THIS_KW,
            'string232',
            0,
            '$this->string232[0]'
        ];

        $testData[] = [
            // 2x this   string (property, with subjectIndex
            233,
            AssignClauseMgr::THIS_KW,
            'string233',
            'pos233',
            '$this->string233[$pos233]'
        ];

        $testData[] = [
            // 2x this   string (property, with subjectIndex
            235,
            AssignClauseMgr::THIS_KW,
            'string235',
            '235',
            '$this->string235[235]'
        ];

        $testData[] = [
            // 3x $class  :: (public) property
            275,
            '$class275',
            '$property275',
            null,
            '$class275->property275'
        ];

        $testData[] = [
            // 3x $class  :: (public) property with subjectIndex
            276,
            '$class276',
            '$property276',
            '276',
            '$class276->property276[276]'
        ];

        $testData[] = [
            // 3x $class  :: (public static) property with subjectIndex
            277,
            '$class277',
            '$property277',
            'twoSevenSeven',
            '$class277->property277[$twoSevenSeven]'
        ];

        return $testData;
    }

    private static function varExport( $variable )
    {
        return str_replace( PHP_EOL, '', var_export( $variable, true ));
    }

    /**
     * TernaryNullCoalesceMgrTest21 dataprovider
     *
     * @return array
     */
    public function TernaryNullCoalesceMgrTest21DataProvider() : array
    {
        $testData = [];

        $exprAs        = $this->getTargetArr1();
        $exprAs[2525][3] = 25;
        $exprAs[3232][3] = 32;
        $exprAs[3535][3] = 35;
        $exprAs[7878][3] = 78;
        $exprAKeys     = array_flip( array_keys( $exprAs ));
        $exprBargs     = self::FcnInvokeMgrTest3ArgumentProvider();
        $exprBargsKeys = array_flip( array_keys( $exprBargs ));
        $exprBs        = self::FcnInvokeMgrFunctionProvider();
        $exprBKeys     = array_flip( array_keys( $exprBs ));

        $exprCs        = self::getExprVarArr();
        $exprCKeys     = array_flip( array_keys( $exprCs ));

        $range         = range( 1, 6 );
        for( $x = 1; $x <= 10; $x++ ) {
            $exprVal   = array_rand( $range );

            $exprIx    = array_rand( $exprAKeys );
            $exprA     = EntityMgr::init()->setClass( $exprAs[$exprIx][1] )
                ->setVariable( $exprAs[$exprIx][2] )
                ->setIndex( $exprAs[$exprIx][3] );
            $caseA     = $exprAs[$exprIx][0];
            $expectedA = $exprAs[$exprIx][4];

            $mthdIx    = array_rand( $exprBKeys );
            $mthd      = $exprBs[$mthdIx];
            $argIx     = array_rand( $exprBargsKeys );
            $argSet    = $exprBargs[$argIx][1];
            $exprB     = FcnInvokeMgr::init();
            try {
                $exprB->setName( $mthd[1], $mthd[2] );
            }
            catch( Exception $e ) {
                $msg = 'Exception B-1 : ' . $e->getMessage();
                error_log( $msg ); // test ###
                die( $msg );
            }
            if( ! empty( $argSet )) {
                try {
                    $exprB->setArguments( (array) $argSet );
                }
                catch( Exception $e ) {
                    $msg = 'Exception B-3 : ' . $e->getMessage(); // test ###
                    error_log( $msg ); // test ###
                    die( $msg );
                }
            }
            $caseB     = $mthd[0];
            $expectedB = $mthd[3];

            $exprIx    = array_rand( $exprCKeys );
            $exprC     = EntityMgr::init()->setClass( $exprCs[$exprIx][1] )
                ->setVariable( $exprCs[$exprIx][2] )
                ->setIndex( $exprCs[$exprIx][3] );
            $caseC     = $exprCs[$exprIx][0];
            $expectedC = $exprCs[$exprIx][4];

            switch( $exprVal ) {
                case 1 :
                    $case     = $caseA . '-' . $caseB . '-' . $caseC;
                    $expr1    = $exprA;
                    $expr2    = $exprB;
                    $expr3    = $exprC;
                    $ternaryOperator = true;
                    $expected = [ $expectedA, $expectedB, $expectedC ];
                    break;
                case 2 :
                    $case     = $caseB . '-' . $caseC . '-' . $caseA;
                    $expr1    = $exprB;
                    $expr2    = $exprC;
                    $expr3    = $exprA;
                    $ternaryOperator = true;
                    $expected = [ $expectedB, $expectedC, $expectedA ];
                    break;
                case 3 :
                    $case     = $caseC . '-' . $caseA . '-' . $caseB;
                    $expr1    = $exprC;
                    $expr2    = $exprA;
                    $expr3    = $exprB;
                    $ternaryOperator = true;
                    $expected = [ $expectedC, $expectedA, $expectedB ];
                    break;
                case 4 :
                    $case     = $caseB . '-----' . $caseA;
                    $expr1    = $exprB;
                    $expr2    = null;
                    $expr3    = $exprA;
                    $ternaryOperator = true;
                    $expected = [ $expectedB, $expectedA ];
                    break;
                case 5 :
                    $var      = 'variable';
                    $case     = $caseC . '-' . $var . '-' . $caseA;
                    $expr1    = $exprC;
                    $expr2    = $var;
                    $expr3    = $exprA;
                    $ternaryOperator = true;
                    $expected = [ $expectedC, $var, $expectedA ];
                    break;
                default :
                    $case     = $caseA . '-----' . $caseB;
                    $expr1    = $exprA;
                    $expr2    = $exprB;
                    $expr3    = null;
                    $ternaryOperator = false;
                    $expected = [ $expectedA, $expectedB ];
                    break;
            } // end switch
            $testData[] = [ $case, $expr1, $expr2, $expr3, $ternaryOperator, $expected ];
        } // end for

        return $testData;
    }

    /**
     * Testing TernaryNullCoalesceMgr
     *
     * Similar as ReturnClauseMgrTest2::ReturnClauseMgrTest21()
     *
     * @test
     * @dataProvider TernaryNullCoalesceMgrTest21DataProvider
     *
     * @param string $case
     * @param mixed  $expr1
     * @param mixed  $expr2
     * @param mixed  $expr3
     * @param bool   $ternaryOperator
     * @param array  $expected
     * @todo if expr1/2/3 is array, always use index !!
     */
    public function TernaryNullCoalesceMgrTest21(
        string $case,
        $expr1,
        $expr2,
        $expr3,
        bool $ternaryOperator,
        array $expected
    )
    {
        $tncMgr = TernaryNullCoalesceMgr::factory( $expr1, $expr2, $expr3 )
            ->setTernaryOperator( $ternaryOperator );
        $code = $tncMgr->toString();

        foreach( $expected as $eIx => $exp ) {
            $this->assertTrue(
                ( false !== strpos( $code, $exp )),
                $case . '-' . $eIx .  ' actual : \'' . trim( $code ) . '\'' . PHP_EOL .
                ' expected : \'' . trim( $exp ) . '\''
            );
        }

        if( DISPLAYtcn) {
            echo __FUNCTION__ . ' ' . $case . ' : ' . trim( $code ) . PHP_EOL;
        }
    }

    /**
     * TernaryNullCoalesceMgr::toArray() Exception test 1
     *
     * @test
     */
    public function TernaryNullCoalesceMgrTest71() {
        try {
            $tnc = new TernaryNullCoalesceMgr();
            $tnc->toArray();
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * TernaryNullCoalesceMgr::toArray() Exception test 2
     *
     * @test
     */
    public function TernaryNullCoalesceMgrTest72() {
        try {
            TernaryNullCoalesceMgr::factory( 'var' )->toArray();
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * TernaryNullCoalesceMgr::toArray() Exception test 3
     *
     * @test
     */
    public function TernaryNullCoalesceMgrTest73() {
        try {
            TernaryNullCoalesceMgr::factory( 'var1', 'var2' )
                ->setTernaryOperator( true )
                ->toArray();
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * TernaryNullCoalesceMgr::toArray() Exception test 4
     *
     * @test
     */
    public function TernaryNullCoalesceMgrTest74() {
        try {
            TernaryNullCoalesceMgr::factory( 'var1' )
                ->setTernaryOperator( false )
                ->toArray();
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }

    /**
     * TernaryNullCoalesceMgr::toArray() Exception test 5
     *
     * @test
     */
    public function TernaryNullCoalesceMgrTest75() {
        try {
            TernaryNullCoalesceMgr::factory( 'var1', 'var2', 'var3' )
                ->setTernaryOperator( false )
                ->toArray();
            $this->assertTrue( false );
        }
        catch( Exception $e ) {
            $this->assertTrue( true );
        }
    }
}
