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
namespace Kigkonsult\PcGen\Traits;

use InvalidArgumentException;

use function in_array;
use function sprintf;
use function var_export;

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
    public static function getOperators() : array
    {
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
    public function getOperator( $strict = false ) : string
    {
        static $OPERATORfmt = ' %s ';
        return ( $strict ?? false )
            ? $this->operator
            : sprintf( $OPERATORfmt, $this->operator );
    }

    /**
     * @param string $operator
     * @return static
     * @throws InvalidArgumentException
     */
    public function setOperator( string $operator ) : self
    {
        $operator = trim( $operator );
        if( ! in_array( $operator, self::$OPERATORARR )) {
            throw new InvalidArgumentException(
                sprintf( self::$ERRx, var_export( $operator, true ))
            );
        }
        $this->operator = $operator;
        return $this;
    }
}
