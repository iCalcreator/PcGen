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
    public function getTargetArr1() {
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

        $testData[] = [
            //  4x self class + $-prefixed string (with subjectIndex)
            25,
            AssignClauseMgr::SELF_KW,
            '$var25',
            '[]',
            'self::$var25[]'
        ];

        $testData[] = [
            // 2x this   string property
            31,
            AssignClauseMgr::THIS_KW,
            'string31',
            null,
            '$this->string31'
        ];

        $testData[] = [
            // 2x this   string (property with subjectIndex)
            32,
            AssignClauseMgr::THIS_KW,
            'string32',
            0,
            '$this->string32[0]'
        ];

        $testData[] = [
            // 2x this   string (property, with subjectIndex
            33,
            AssignClauseMgr::THIS_KW,
            'string33',
            'pos33',
            '$this->string33[$pos33]'
        ];

        $testData[] = [
            // 2x this   string (property, with subjectIndex
            35,
            AssignClauseMgr::THIS_KW,
            'string35',
            '[]',
            '$this->string35[]'
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

        $testData[] = [
            // 3x $class  :: (public) property with subjectIndex, only possible with operator '='
            78,
            '$class78',
            '$property78',
            '[]',
            '$class78->property78[]'
        ];

        return $testData;
    }

}
