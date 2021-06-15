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

trait FimDataProviderTrait
{
    /**
     * Same as ...
     *
     * @return array
     */
    public static function FcnInvokeMgrTest3ArgumentProvider() : array
    {
        $testData = [];

        $testData[] = [
            11,
            null
        ];

        $testData[] = [
            21,
            'argument21',
        ];

        $testData[] = [
            22,
            '$argument22',
        ];

        $testData[] = [
            31,
            [
                'argument31',
            ]
        ];

        $testData[] = [
            32,
            [
                'argument321',
                'argument322',
            ]
        ];

        $testData[] = [
            41,
            [
                '$argument41',
            ]
        ];

        $testData[] = [
            42,
            [
                '$argument421',
                'argument422',
            ]
        ];

        $testData[] = [
            43,
            [
                'argument431',
                '$argument432',
            ]
        ];

        $testData[] = [
            41,
            [
                [ 'argument41' ]
            ]
        ];

        $testData[] = [
            42,
            [
                [ 'argument42', FcnInvokeMgr::ARRAY_T ]
            ]
        ];

        $testData[] = [
            43,
            [
                [ 'argument43', null, FcnInvokeMgr::NULL_T ]
            ]
        ];

        $testData[] = [
            44,
            [
                [ 'argument44', FcnInvokeMgr::ARRAY_T, FcnInvokeMgr::ARRAY2_T ]
            ]
        ];


        $testData[] = [
            45,
            [
                [ 'argument45', null, null, true ]
            ]
        ];

        $testData[] = [
            46,
            [
                [ 'argument46', FcnInvokeMgr::ARRAY_T, null, true ]
            ]
        ];

        $testData[] = [
            47,
            [
                [ 'argument47', null, FcnInvokeMgr::TRUE_KW, true ]
            ]
        ];

        $testData[] = [
            48,
            [
                [ 'argument48', FcnInvokeMgr::ARRAY_T, FcnInvokeMgr::ARRAY2_T, true ]
            ]
        ];

        $testData[] = [
            49,
            [
                [ 'argument49', [ FcnInvokeMgr::INT_T, FcnInvokeMgr::STRING_T ], null, true ]
            ]
        ];

        $testData[] = [
            51,
            [
                [ '$argument511', FcnInvokeMgr::ARRAY_T, FcnInvokeMgr::ARRAY2_T ],
                [ 'argument512', null, FcnInvokeMgr::NULL_T ],
            ]
        ];

        $testData[] = [
            52,
            [
                [ 'argument521', FcnInvokeMgr::ARRAY_T, FcnInvokeMgr::ARRAY2_T, true ],
                [ '$argument522', null, FcnInvokeMgr::NULL_T, true ],
            ]
        ];

        $testData[] = [
            61,
            [
                [ 'argument611', FcnInvokeMgr::ARRAY_T, FcnInvokeMgr::ARRAY2_T, true ],
                [ 'argument612', FcnInvokeMgr::class,   FcnInvokeMgr::NULL_T ],
                [ 'argument613', FcnInvokeMgr::ARRAY_T, FcnInvokeMgr::ARRAY2_T, true ],
            ]
        ];

        $testData[] = [
            71,
            [
                [ 'argument711', FcnInvokeMgr::ARRAY_T, FcnInvokeMgr::ARRAY2_T, true ],
                [ 'argument712', FcnInvokeMgr::class,   FcnInvokeMgr::NULL_T ],
                [ 'argument713', FcnInvokeMgr::ARRAY_T, FcnInvokeMgr::ARRAY2_T, true ],
                [ 'argument714', FcnInvokeMgr::class,   FcnInvokeMgr::NULL_T ],
            ]
        ];

        $testData[] = [
            81,
            [
                [ 'argument811', FcnInvokeMgr::class,   FcnInvokeMgr::NULL_T ],
                [ 'argument812', FcnInvokeMgr::ARRAY_T, FcnInvokeMgr::ARRAY2_T, true ],
                [ 'argument813', FcnInvokeMgr::class,   FcnInvokeMgr::NULL_T ],
                [ 'argument814', FcnInvokeMgr::ARRAY_T, FcnInvokeMgr::ARRAY2_T, true ],
                [ 'argument815', FcnInvokeMgr::class,   FcnInvokeMgr::NULL_T ],
            ]
        ];

        return $testData;
    }

    /**
     * @return array
     */
    public static function FcnInvokeMgrFunctionProvider() : array
    {
        $testData = [];

        $testData[] = [
            //  null + local function
            11,
            null,
            'function11',
            'function11'
        ];

        $testData[] = [
            //  null + local function
            12,
            null,
            '$function12',
            'function12'
        ];

        $testData[] = [
            //  4x self class + class (static) function
            22,
            FcnInvokeMgr::SELF_KW,
            'function22',
            'self::function22'
        ];

        $testData[] = [
            //  4x self class + class (static) function
            23,
            FcnInvokeMgr::SELF_KW,
            '$function23',
            'self::function23'
        ];

        $testData[] = [
            // 2x this   string property
            31,
            FcnInvokeMgr::THIS_KW,
            'function31',
            '$this->function31'
        ];

        $testData[] = [
            // 2x this   string property
            32,
            FcnInvokeMgr::THIS_KW,
            '$function32',
            '$this->function32'
        ];

        $testData[] = [
            // 3x $class  :: (public) property
            75,
            '$class75',
            'function75',
            '$class75->function75'
        ];

        $testData[] = [
            // 3x $class  :: (public) property
            76,
            '$class76',
            '$function76',
            '$class76->function76'
        ];

        return $testData;
    }


}
