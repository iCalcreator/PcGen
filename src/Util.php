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

use RuntimeException;

use function chr;
use function ctype_digit;
use function explode;
use function gettype;
use function in_array;
use function is_bool;
use function is_int;
use function is_scalar;
use function is_string;
use function number_format;
use function preg_match;
use function sprintf;
use function str_replace;
use function strcasecmp;
use function strlen;
use function strpos;
use function substr;
use function trim;
use function var_export;

/**
 * Class Util
 *
 * @package Kigkonsult\PcGen
 */
class Util implements PcGenInterface
{
    /**
     * Return type hint, if found, dep. on PHP version
     *
     * @param string $varType
     * @param null|string $phpVersion  expected PHP version, default PHP_VERSION
     * @param null|string $typeHint
     * @return bool
     * @todo https://www.infoq.com/articles/php7-new-type-features/
     */
    public static function evaluateTypeHint(
        string $varType,
        $phpVersion = null,
        & $typeHint = null
    ) : bool
    {
        static $DOT = '.';
        static $MIXEDTypeHints = [ self::MIXED_KW ];
        static $PHP5TypeHints  = [ self::ARRAY_T, self::CALLABLE_T, self::SELF_KW ];
        static $PHP70TypeHints = [ self::BOOL_T, self::FLOAT_T, self::INT_T, self::STRING_T ];
        static $PHP71TypeHints = [ self::ITERABLE_T ];
        static $PHP72TypeHints = [ self::OBJECT_KW ];
        if( null === $phpVersion ) {
            $phpVersion = BaseA::getTargetPhpVersion();
        }
        $std      = explode( $DOT, $phpVersion, 3 );
        $phpMajor = (int) $std[ 0 ];
        $phpMinor = (int) $std[ 1 ];
        $return = true;
        switch( true ) {
            case in_array( $varType, $MIXEDTypeHints ) :
                $return = false;
                break;
            case in_array( $varType, $PHP5TypeHints ) :
                $typeHint = $varType;
                break;
            case ( ( 5 == $phpMajor ) &&
                ( in_array( $varType, $PHP70TypeHints ) ||
                  in_array( $varType, $PHP71TypeHints ) ||
                  in_array( $varType, $PHP72TypeHints ))) :
                $return = false;
                break;
            case (( 7 == $phpMajor ) && in_array( $varType, $PHP70TypeHints )) :
                $typeHint = $varType;
                break;
            case (( 7 == $phpMajor ) && in_array( $varType, $PHP71TypeHints )) :
                if( 1 > $phpMinor ) {
                    $return = false;
                }
                $typeHint = $varType;
                break;
            case (( 7 == $phpMajor ) && in_array( $varType, $PHP72TypeHints )) :
                if( 2 > $phpMinor ) {
                    $return = false;
                }
                $typeHint = $varType;
                break;
            case is_string( $varType ) : // accept (string) fqcn
                $typeHint = $varType;
                break;
            default :
                $return = false;
                break;
        } // end switch
        return $return;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isFloat( $value ) : bool
    {
        static $FLOATS = [ 'double', 'float' ];
        static $PATTERN = "/^\\d+\\.\\d+$/";
        if( ! is_scalar( $value )) {
            return false;
        }
        if( in_array( gettype( $value ), $FLOATS )) {
            return true;
        }
        return ( 1 === preg_match( $PATTERN, (string) $value ));
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isInt( $value ) : bool
    {
        return ( is_int( $value ) || ctype_digit( $value ) );
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isVarPrefixed( $value ) : bool
    {
        return (
            is_string( $value ) &&
            ( self::VARPREFIX == substr( $value, 0, 1 ))
        );
    }

    /**
     * @param string $value
     * @return string
     */
    public static function setVarPrefix( string $value ) : string
    {
        $value = trim( $value );
        return self::isVarPrefixed( $value ) ? $value : self::VARPREFIX . $value;
    }

    /**
     * @param string $value
     * @return string
     */
    public static function unSetVarPrefix( string $value ) : string
    {
        $value = trim( $value );
        return self::isVarPrefixed( $value ) ? substr( $value, 1 )  : $value;
    }

    /**
     * Return string with no null bytes
     *
     * @param string $value
     * @return string
     */
    public static function nullByteCleanString( string $value ) : string
    {
        static $CHR0 = null;
        if( null === $CHR0 ) {
            $CHR0 = chr( 0 );
        }
        return empty( $value ) ? self::SP0 : str_replace( $CHR0, self::SP0, $value );
    }

    /**
     * Return array with no null bytes i array element
     *
     * @param array $array
     * @return array
     */
    public static function nullByteCleanArray( array $array ) : array
    {
        foreach( $array as $rowIx => $line ) {
            $array[ $rowIx ] = self::nullByteCleanString( $line );
        }
        return $array;
    }

    /**
     * @param bool|float|int|string $value
     * @param null|string           $expType  (string?)
     * @return string
     * @throws RuntimeException
     */
    public static function renderScalarValue( $value, $expType = null ) : string
    {
        if( ! is_scalar( $value )) {
            throw new RuntimeException(
                sprintf( BaseA::$ERRx, var_export( $value, true ))
            );
        }
        static $BOOLTYPES    = [ self::BOOL_T, self::BOOLEAN_T ];
        static $BOOLVALUEARR = [ 'true', 'false' ];
        static $TRUE    = 'true';
        static $FALSE   = 'false';
        static $DOT     = '.';
        static $ZERO0   = '0.0';
        static $ZERO    = '0';
        static $Q2      = '"';
        static $QUOTE1  = '\'%s\'';
        static $QUOTE2  = '"%s"';
        switch( true ) {
            case ( ! empty( $expType ) &&
                self::anyCaseStrInArray( $expType, $BOOLTYPES ) &&
                is_string( $value ) &&
                self::anyCaseStrInArray( $value, $BOOLVALUEARR )) :
                return strtolower( $value );
            case is_bool( $value ) :
                return $value ? $TRUE : $FALSE;
            case ( Util::isInt( $value ) && ( self::STRING_T != $expType )) :
                return (string) $value;
            case Util::isFloat( $value ) :
                switch( true ) {
                    case empty( $value ) :
                        $value = $ZERO0;
                        break;
                    case ( 0.0001 > abs( $value )) :
                        // make float to string AND preserve fraction
                        $value *= 1000000;
                        $value  = (string) $value;
                        $precision = strlen( $value ) - strpos( $value, $DOT ) - 1 + 7;
                        $value  = rtrim(
                            number_format( ( $value / 1000000 ), $precision, $DOT, self::SP0 )
                            , $ZERO
                        );
                        break;
                    default :
                        $value2    = (string) $value;
                        $precision = strlen( $value2 ) - strpos( $value2, $DOT ) - 1;
                        $value = number_format( $value, $precision, $DOT, self::SP0 );
                } // end switch
                return ( self::STRING_T != $expType ) ? $value : sprintf( $QUOTE1, $value );
            default :
                $tmpl = ( false !== strpos((string) $value, $Q2 )) ? $QUOTE1 : $QUOTE2;
                return sprintf( $tmpl, $value );
        } // end switch
    }

    /**
     * @param string $value
     * @param array  $hayStack
     * @return bool
     */
    private static function anyCaseStrInArray( string $value, array $hayStack ) : bool
    {
        foreach( $hayStack as $item ) {
            if( 0 == strcasecmp( $value, $item )) {
                return true;
            }
        }
        return false;
    }

    /**
     * Remove leading empty array rows, all but last
     *
     * @param array $array
     * @return array
     */
    public static function trimLeading( array $array ) : array
    {
        if( empty( $array ) ) {
            return [];
        }
        foreach( array_keys( $array ) as $ix ) {
            if( ! empty( trim( $array[$ix] ) ) ) {
                break;
            }
            unset( $array[$ix] );
        }
        return empty( $array ) ? [] : $array;
    }

    /**
     * Remove trailing empty array rows, all but first
     *
     * @param array $array
     * @return array
     */
    public static function trimTrailing( array $array ) : array
    {
        if( empty( $array ) ) {
            return [];
        }
        foreach( array_reverse( array_keys( $array ) ) as $ix ) {
            if( ! empty( trim( $array[$ix] ) ) ) {
                break;
            }
            unset( $array[$ix] );
        }
        return empty( $array ) ? [] : $array;
    }
}
