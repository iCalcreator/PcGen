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
namespace Kigkonsult\PcGen\Dto;

use function in_array;

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
     * @param null|string|VarDto $name
     * @param null|string $type
     * @param null|mixed $default
     * @param null|string $summary
     * @param null|string|array $description
     * @return static
     */
    public static function factory(
        $name = null,
        $type = null,
        $default = null,
        $summary = null,
        $description = null
    ) : VarDto
    {
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
    public function __toString() : string
    {
        return parent::__toString() .
            ', byReference: ' . ( $this->byReference ? '+' : '-' ) .
            ', updClassProp: ' . $this->updClassProp .
            ', nextVarPropIndex: ' . ( $this->byReference ? '+' : '-' );
    }

    /**
     * @return bool
     */
    public function isByReference() : bool
    {
        return $this->byReference;
    }

    /**
     * @param bool $byReference
     * @return ArgumentDto
     */
    public function setByReference( $byReference = true ) : self
    {
        $this->byReference = $byReference ?? true;
        return $this;
    }

    /**
     * @return int  1 or 9
     */
    public function getUpdClassProp() : int
    {
        return $this->updClassProp;
    }

    /**
     * @param int $updClassProp  1=before, 9=after function body, before opt. set return(value)
     * @return ArgumentDto
     */
    public function setUpdClassProperty( $updClassProp = self::BEFORE ) : self
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
    public function isNextVarPropIndex() : bool
    {
        return $this->nextVarPropIndex;
    }

    /**
     * @param bool $nextVarPropIndex
     * @return ArgumentDto
     */
    public function setNextVarPropIndex( $nextVarPropIndex = true ) : self
    {
        $this->nextVarPropIndex =
            ( $this->isTypedArray() || $this->isDefaultArray())
                ? ( $nextVarPropIndex ?? true )
                : false;
        return $this;
    }
}
