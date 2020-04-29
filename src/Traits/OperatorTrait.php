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
namespace Kigkonsult\PcGen\Traits;

use InvalidArgumentException;

/**
 * Trait OperatorTrait
 *
 * Manages assignment operators
 *
 * @package Kigkonsult\PcGen\Traits
 */
trait OperatorTrait
{
    /**
     * @var string[]
     */
    private static $OPERATORARR = [
        '=',                           // default
        '+=', '-=', '*=', '/=', '%/',  // arethmetic
        '.=',                          // string
        '&=', '|=', '^=', '<<=', '>>=' // bitwise
    ];

    /**
     * @return string[]
     */
    public static function getOperators() {
        return self::$OPERATORARR;
    }

    /**
     * $var string
     */
    private $operator = '=';

    /**
     * @param bool $strict
     * @return string
     */
    public function getOperator( $strict = false ) {
        static $OPERATORfmt = ' %s ';
        return $strict ? $this->operator : sprintf( $OPERATORfmt, $this->operator );
    }

    /**
     * @param string $operator
     * @return static
     * @throws InvalidArgumentException
     */
    public function setOperator( $operator ) {
        if( ! in_array( $operator, self::$OPERATORARR )) {
            throw new InvalidArgumentException(
                sprintf( self::$ERRx, var_export( $operator, true ))
            );
        }
        $this->operator = trim( $operator );
        return $this;
    }

}
