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
namespace Kigkonsult\PcGen\Dto;

/**
 * Class ArgumentDto
 *
 * The class manages function arguments and closures use variables
 *
 * @package Kigkonsult\PcGen
 */
final class ArgumentDto extends VarDto
{
    /**
     * updClassProp arguments
     */
    const AFTER  = 9;
    const BEFORE = 1;
    const NONE   = 0;

    /**
     * @var bool
     */
    private $byReference = false;

    /**
     * @var int
     */
    private $updClassProp = 0;

    /**
     * @var bool
     */
    private $nextVarPropIndex = false;

    /**
     * @param string|VarDto $name
     * @param string $type
     * @param mixed $default
     * @param string $summary
     * @param string|array $description
     * @return static
     */
    public static function factory(
        $name = null,
        $type = null,
        $default = null,
        $summary = null,
        $description = null
    ) {
        if( $name instanceof VarDto ) {
            $type        = $name->getVarType();
            $default     = $name->getDefault();
            $summary     = $name->getSummary();
            $description = $name->getDescription();
            $name        = $name->getName();
        }
        return new static( $name, $type, $default, $summary, $description );
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return parent::__toString() .
            ', byReference: ' . ( $this->byReference ? '+' : '-' ) .
            ', updClassProp: ' . $this->updClassProp .
            ', nextVarPropIndex: ' . ( $this->byReference ? '+' : '-' );
    }

    /**
     * @return bool
     */
    public function isByReference()
    {
        return $this->byReference;
    }

    /**
     * @param bool $byReference
     * @return ArgumentDto
     */
    public function setByReference( $byReference = true )
    {
        $this->byReference = $byReference;
        return $this;
    }

    /**
     * @return int  1 or 9
     */
    public function getUpdClassProp()
    {
        return $this->updClassProp;
    }

    /**
     * @param int $updClassProp  1=before, 9=after function body, before opt. set return(value)
     * @return ArgumentDto
     */
    public function setUpdClassProperty( $updClassProp = self::BEFORE )
    {
        if( in_array( $updClassProp, [ null, true, self::BEFORE ], true )) {
            $this->updClassProp = self::BEFORE;
        }
        elseif( self::NONE == $updClassProp ) {
            $this->updClassProp = self::NONE;
        }
        else {
            $this->updClassProp = self::AFTER;
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isNextVarPropIndex()
    {
        return $this->nextVarPropIndex;
    }

    /**
     * @param bool $nextVarPropIndex
     * @return ArgumentDto
     */
    public function setNextVarPropIndex( $nextVarPropIndex = true ) {
        $this->nextVarPropIndex = ( $this->isTypedArray() || $this->isDefaultArray())
            ? $nextVarPropIndex
            : false;
        return $this;
    }
}
