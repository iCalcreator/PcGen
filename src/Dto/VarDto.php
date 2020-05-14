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

use InvalidArgumentException;
use Kigkonsult\PcGen\Assert;
use Kigkonsult\PcGen\BaseA;
use Kigkonsult\PcGen\PcGenInterface;
use Kigkonsult\PcGen\Util;

/**
 * Class VarDto
 *
 * The class manages variable/property base values
 *
 * @package Kigkonsult\PcGen
 */
class VarDto implements PcGenInterface
{
    /**
     * @var array
     */
    public static $ARRAYs = [ 'array()', self::ARRAY_T, self::ARRAY2_T ];

    /**
     * @var string
     */
    private $name = null;

    /**
     * @var string  one of the VARTYPELIST members or fqcn
     */
    private $varType = null;

    /**
     * @var mixed  default value at initialisation or varType hint at function call
     */
    private $default = null;

    /**
     * @var string
     */
    private $summary = null;

    /**
     * @var array
     */
    private $description = [];

    /**
     * Class constructor
     *
     * @param string       $name
     * @param string       $type
     * @param mixed        $default
     * @param string       $summary
     * @param string|array $description
     * @throws InvalidArgumentException
     */
    public function __construct(
        $name = null ,
        $type = null,
        $default = null,
        $summary = null,
        $description = null
    ) {
        $this->setName( $name );
        $this->setVarType( $type );
        $this->setDefault( $default );
        $this->setSummary( $summary );
        $this->setDescription( $description );
    }

    /**
     * @param string $name
     * @param string $type
     * @param mixed $default
     * @param string $summary
     * @param string|array $description
     * @return static
     * @throws InvalidArgumentException
     */
    public static function factory(
        $name = null,
        $type = null,
        $default = null,
        $summary = null,
        $description = null
    ) {
        return new static( $name, $type, $default, $summary, $description );
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->name .
            ' (' . str_replace( [PHP_EOL, ' ' ], '', var_export( $this->varType, true )) . ') : ' .
            str_replace( [PHP_EOL, ' ' ], '', var_export( $this->default, true ));
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isNameSet() {
        return ( null !== $this->name );
    }

    /**
     * @param string $name
     * @return static
     * @throws InvalidArgumentException
     */
    public function setName( $name ) {
        static $SP0 = '';
        if( $SP0 != trim( $name )) {
            if( Util::isVarPrefixed( $name ) ) {
                $name = substr( $name, 1 );
            }
            $this->name = Assert::assertPhpVar( $name );
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getVarType() {
        return $this->varType;
    }

    /**
     * @return string
     */
    public function getParamTagVarType() {
        return $this->isVarTypeSet() ? $this->getVarType() : self::MIXED_KW;
    }

    /**
     * @return bool
     */
    public function isTypedArray() {
        if( is_array( $this->varType )) { // mixed types
            return false;
        }
        if( in_array( $this->varType, self::$ARRAYs, true )) {
            return true;
        }
        if( self::ARRAY2_T == substr( $this->varType, -2 )) {
            return true;
        }
        return false;
    }

    /**
     * Return true if array element typeHint found (ex string[]), false on array(multi|Types)
     *
     * @param string $phpVersion  expected PHP version, default PHP_MAJOR_VERSION
     * @param string $typeHint
     * @return bool
     */
    public function hasTypeHintArraySpec( $phpVersion = null, & $typeHint = null ) {
        if( empty( $this->varType ) ||
//          ! is_string( $this->varType ) ||
            ( self::ARRAY_T == $this->varType ) ||
            ( self::ARRAY2_T == $this->varType ) ||
            ( self::ARRAY2_T != substr( $this->varType, -2 ))) {
            return false;
        }
        return Util::evaluateTypeHint( substr( $this->varType, 0, -2 ), $phpVersion, $typeHint );
    }

    /**
     * Return true if typeHint found, false on array(multi|Types)
     *
     * @param string $phpVersion  expected PHP version, default PHP_MAJOR_VERSION
     * @param string $typeHint
     * @return bool
     */
    public function isTypeHint( $phpVersion = null, & $typeHint = null ) {
        if( empty( $this->varType ) || ! is_string( $this->varType )) {
            return false;
        }
        if(( self::ARRAY_T == $this->varType ) ||
            ( self::ARRAY2_T == $this->varType ) ||
            ( self::ARRAY2_T == substr( $this->varType, -2 ))) {
            $typeHint = self::ARRAY_T;
            return true;
        }
        return Util::evaluateTypeHint( $this->varType, $phpVersion, $typeHint );
    }

    /**
     * @return bool
     */
    public function isVarTypeSet() {
        return ( null !== $this->varType );
    }

    /**
     * @param string $varType
     * @return static
     * @todo validate varType?
     */
    public function setVarType( $varType = null ) {
        $this->varType = $varType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefault() {
        return $this->default;
    }

    /**
     * Return bool true if the default value is array
     *
     * @return bool
     */
    public function isDefaultArray() {
        return is_array( $this->default );
    }

    /**
     * Return bool true if NOT null, however, it may have been set to null
     *
     * @return bool
     */
    public function isDefaultSet() {
        return ( null !== $this->default );
    }

    /**
     * Return bool true if the default value is typed array
     *
     * @return bool
     */
    public function isDefaultTypedArray() {
        if( ! is_scalar( $this->default )) {
            return false;
        }
        if( in_array( $this->default, self::$ARRAYs, true )) {
            return true;
        }
        if( self::ARRAY2_T == substr( $this->default, -2 )) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isDefaultTypedNull() {
        return ( self::NULL_T === $this->default );
    }

    /**
     * Set default (initValue), only null, scalar or array allowed
     * @param mixed $default
     * @return static
     * @throws InvalidArgumentException
     */
    public function setDefault( $default = null ) {
        if(( null !== $default ) && ! is_scalar( $default ) && ! is_array( $default )) {
            throw new InvalidArgumentException( sprintf( BaseA::$ERRx, var_export( $default, true )));
        }
        $this->default = $default;
        return $this;
    }

    /**
     * @return string
     */
    public function getSummary() {
        return $this->summary;
    }

    /**
     * @return bool
     */
    public function isSummarySet() {
        return ( null !==  $this->summary );
    }

    /**
     * @param string $summary
     * @return static
     */
    public function setSummary( $summary = null ) {
        $this->summary = $summary;
        return $this;
    }

    /**
     * @return array
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @return bool
     */
    public function isDescriptionSet() {
        return ( ! empty( $this->description ));
    }

    /**
     * @param string|array $description
     * @return static
     */
    public function setDescription( $description = null ) {
        $this->description = ( null === $description ) ? null : (array) $description;
        return $this;
    }

}
