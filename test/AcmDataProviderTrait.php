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

trait AcmDataProviderTrait
{

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
    public function getTargetArr1() : array
    {
        $testData = [];

        $testData[] = [
            //  null + $-prefixed string
            11,
            null,
            '$variable11',
            null,
            '$variable11'
        ];

        $testData[] = [
            //  null + $-prefixed variable[]
            12,
            null,
            '$variable12',
            AssignClauseMgr::ARRAY2_T,
            '$variable12[]'
        ];

        $testData[] = [
            //  null + $-prefixed variable[0]
            13,
            null,
            '$variable13',
            0,
            '$variable13[0]'
        ];

        $testData[] = [
            //  null + $-prefixed variable[14]
            14,
            null,
            '$variable14',
            14,
            '$variable14[14]'
        ];

        $testData[] = [
            //  null + $-prefixed variable[index]
            15,
            null,
            '$variable15',
            'index',
            '$variable15[$index]'
        ];

        $testData[] = [
            //  4x self class + $-prefixed string
            22,
            AssignClauseMgr::SELF_KW,
            '$var22',
            null,
            'self::$var22'
        ];

        $testData[] = [
            //  4x self class + num index
            23,
            AssignClauseMgr::SELF_KW,
            '$var23',
            23,
            'self::$var23[23]'
        ];

        $testData[] = [
            //  4x self class + $-prefixed string (with subjectIndex)
            24,
            AssignClauseMgr::SELF_KW,
            '$var24',
            '$index24',
            'self::$var24[$index24]'
        ];

        $testData[2525] = [
            //  4x self class + $-prefixed string (with subjectIndex)
            25,
            AssignClauseMgr::SELF_KW,
            '$var25',
            25,
            'self::$var25[25]'
        ];

        $testData[] = [
            // 2x this   string property
            31,
            AssignClauseMgr::THIS_KW,
            'string31',
            null,
            '$this->string31'
        ];

        $testData[3232] = [
            // 2x this   string (property with subjectIndex)
            32,
            AssignClauseMgr::THIS_KW,
            'string32',
            32,
            '$this->string32[32]'
        ];

        $testData[] = [
            // 2x this   string (property, with subjectIndex
            33,
            AssignClauseMgr::THIS_KW,
            'string33',
            'pos33',
            '$this->string33[$pos33]'
        ];

        $testData[3535] = [
            // 2x this   string (property, with subjectIndex
            35,
            AssignClauseMgr::THIS_KW,
            'string35',
            '35',
            '$this->string35[35]'
        ];

        $testData[] = [
            // 3x $class  :: (public) property
            75,
            '$class75',
            '$property75',
            null,
            '$class75->property75'
        ];

        $testData[] = [
            // 3x $class  :: (public) property with subjectIndex
            76,
            '$class76',
            '$property76',
            '76',
            '$class76->property76[76]'
        ];

        $testData[] = [
            // 3x $class  :: (public static) property with subjectIndex
            77,
            '$class77',
            '$property77',
            'sevenSeven',
            '$class77->property77[$sevenSeven]'
        ];

        $testData[7878] = [
            // 3x $class  :: (public) property with subjectIndex, only possible with operator '='
            78,
            '$class78',
            '$property78',
            '78',
            '$class78->property78[78]'
        ];

        return $testData;
    }
}
