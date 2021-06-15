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

use InvalidArgumentException;
use Kigkonsult\PcGen\Assert;
use Kigkonsult\PcGen\BaseA;
use Kigkonsult\PcGen\PcGenInterface;
use Kigkonsult\PcGen\Util;

use function in_array;
use function is_array;
use function is_bool;
use function is_scalar;
use function is_string;
use function sprintf;
use function str_replace;
use function strcasecmp;
use function substr;
use function trim;
use function var_export;

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
     * @var string|array  one of the VARTYPELIST members or fqcn
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
     * @param null|string       $name
     * @param null|string       $type
     * @param null|mixed        $default
     * @param null|string       $summary
     * @param null|string|array $description
     * @throws InvalidArgumentException
     */
    public function __construct(
        $name = null ,
        $type = null,
        $default = null,
        $summary = null,
        $description = null
    )
    {
        if( ! empty( $name )) {
            $this->setName( $name );
            if( ! empty( $type ) || is_array( $type )) {
                $this->setVarType( $type );
                if( is_bool( $default ) || ( null !== $default )) {
                    $this->setDefault( $default );
                }
            }
            if( ! empty( $summary )) {
                $this->setSummary( $summary );
            }
            if( ! empty( $description )) {
                $this->setDescription( $description );
            }
        } // end if
    }

    /**
     * @param null|string $name
     * @param null|string $type
     * @param null|mixed $default
     * @param null|string $summary
     * @param null|string|array $description
     * @return static
     * @throws InvalidArgumentException
     */
    public static function factory(
        $name = null,
        $type = null,
        $default = null,
        $summary = null,
        $description = null
    ) : self
    {
        return new static( $name, $type, $default, $summary, $description );
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        static $SEARCH = [ PHP_EOL, ' ' ];
        static $REPL   = '';
        static $P1     = ' (';
        static $P2     = ') : ';
        return $this->name .
            $P1 .
            str_replace( $SEARCH, $REPL, var_export( $this->varType, true )) .
            $P2 .
            str_replace( $SEARCH, $REPL, var_export( $this->default, true ));
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isNameSet() : bool
    {
        return ( null !== $this->name );
    }

    /**
     * @param string $name
     * @return static
     * @throws InvalidArgumentException
     */
    public function setName( string $name ) : self
    {
        if( self::SP0 != trim( $name )) {
            $this->name = Assert::assertPhpVar( Util::unSetVarPrefix( $name ));
        }
        return $this;
    }

    /**
     * @return null|string|array
     */
    public function getVarType()
    {
        return $this->varType;
    }

    /**
     * @return string
     */
    public function getParamTagVarType() : string
    {
        return $this->isVarTypeSet() ? $this->getVarType() : self::MIXED_KW;
    }

    /**
     * @return bool
     */
    public function isTypedArray() : bool
    {
        if( is_array( $this->varType )) { // mixed types
            return false;
        }
        if( ! is_string( $this->varType )) {
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
     * @param null|string $phpVersion  expected PHP version, default PHP_MAJOR_VERSION
     * @param null|string $typeHint
     * @return bool
     */
    public function hasTypeHintArraySpec( $phpVersion = null, & $typeHint = null ) : bool
    {
        if( empty( $this->varType ) ||
//          ! is_string( $this->varType ) ||
            ( self::ARRAY_T == $this->varType ) ||
            ( self::ARRAY2_T == $this->varType ) ||
            ( self::ARRAY2_T != substr( $this->varType, -2 ))) {
            return false;
        }
        return Util::evaluateTypeHint(
            substr( $this->varType, 0, -2 ),
            $phpVersion,
            $typeHint
        );
    }

    /**
     * Return true if typeHint found, false on array(multi|Types)
     *
     * @param null|string $phpVersion  expected PHP version, default PHP_MAJOR_VERSION
     * @param null|string $typeHint
     * @return bool
     */
    public function isTypeHint( $phpVersion = null, & $typeHint = null ) : bool
    {
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
    public function isVarTypeSet() : bool
    {
        return ( null !== $this->varType );
    }

    /**
     * @param null|string|array $varType
     * @return static
     * @todo move to assert::varType? const/fqcn, also in DocBlockMgr
     */
    public function setVarType( $varType = null ) : self
    {
        switch( true ) {
            case is_array( $varType ) :
                $this->varType = $varType;
                break;
            case ( ! is_string( $varType )) :
                $this->varType = $varType;
                break;
            case ( 0 == strcasecmp( self::BOOLEAN_T, $varType )) :
                $this->varType = self::BOOL_T;
                break;
            case ( 0 == strcasecmp( self::BOOLEANARRAY_T, $varType )) :
                $this->varType = self::BOOLARRAY_T;
                break;
            default :
                $this->varType = $varType;
                break;
        } // end switch
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Return bool true if the default value is array
     *
     * @return bool
     */
    public function isDefaultArray() : bool
    {
        return is_array( $this->default );
    }

    /**
     * Return bool true if NOT null, however, it may have been set to null
     *
     * @return bool
     */
    public function isDefaultSet() : bool
    {
        return ( null !== $this->default );
    }

    /**
     * Return bool true if the default value is typed array
     *
     * @return bool
     */
    public function isDefaultTypedArray() : bool
    {
        if( is_array( $this->default )) {
            return true;
        }
        if( ! is_string( $this->default )) {
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
    public function isDefaultTypedNull() : bool
    {
        return ( self::NULL_T === $this->default );
    }

    /**
     * Set default (initValue), only null, scalar or array allowed
     *
     * @param null|mixed $default
     * @return static
     * @throws InvalidArgumentException
     */
    public function setDefault( $default = null ) : self
    {
        if(( null !== $default ) &&
            ! is_scalar( $default ) &&
            ! is_array( $default )) {
            throw new InvalidArgumentException(
                sprintf( BaseA::$ERRx, var_export( $default, true ))
            );
        }
        $this->default = $default;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @return bool
     */
    public function isSummarySet() : bool
    {
        return ( null !==  $this->summary );
    }

    /**
     * @param null|string $summary
     * @return static
     */
    public function setSummary( $summary = null ) : self
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * @return null|array
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return bool
     */
    public function isDescriptionSet() : bool
    {
        return ( ! empty( $this->description ));
    }

    /**
     * @param string|array $description
     * @return static
     */
    public function setDescription( $description = null ) : self
    {
        $this->description = ( null === $description ) ? null : (array) $description;
        return $this;
    }
}
