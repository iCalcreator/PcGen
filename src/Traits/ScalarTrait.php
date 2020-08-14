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
use Kigkonsult\PcGen\Util;

trait ScalarTrait
{
    /**
     * A scalar
     *
     * @var bool|float|int|string
     */
    protected $scalar = null;

    /**
     * @var bool   true if scalar is a (string) PHP expression
     */
    protected $isExpression = false;

    /**
     * @param bool $strict  false returns scalar as string
     * @return bool|float|int|string
     */
    public function getScalar( $strict = true )
    {
        return ( $strict || $this->isExpression )
            ? $this->scalar
            : Util::renderScalarValue( $this->scalar );
    }

    /**
     * @return bool
     */
    public function isScalarSet()
    {
        return ( null !== $this->scalar );
    }

    /**
     * @param bool|float|int|string $scalar
     * @return static
     * @throws InvalidArgumentException
     */
    public function setScalar( $scalar )
    {
        if( ! is_scalar( $scalar )) {
            throw new InvalidArgumentException(
                sprintf( self::$ERRx, var_export( $scalar, true  ))
            );
        }
        $this->scalar       = $scalar;
        $this->isExpression = false;
        return $this;
    }

    /**
     * Return bool true if scalar is a (string) PHP expression
     *
     * @return bool
     */
    public function isExpression()
    {
        return $this->isExpression;
    }

    /**
     * @param string $expression  any PHP expression
     * @return static
     * @throws InvalidArgumentException
     */
    public function setExpression( $expression )
    {
        static $END = ';';
        if( ! is_string( $expression )) {
            throw new InvalidArgumentException(
                sprintf( self::$ERRx, var_export( $expression, true  ))
            );
        }
        $this->scalar       = rtrim( trim( $expression ), $END );
        $this->isExpression = true;
        return $this;
    }
}
