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

use RuntimeException;

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
     * @param string $phpVersion  expected PHP version, default PHP_MAJOR_VERSION
     * @param string $typeHint
     * @return bool
     */
    public static function evaluateTypeHint( $varType, $phpVersion = null, & $typeHint = null ) {
        static $DOT = '.';
        static $PHP5TypeHints  = [ self::ARRAY_T, self::CALLABLE_T, self::SELF_KW ];
        static $PHP70TypeHints = [ self::BOOL_T, self::FLOAT_T, self::INT_T, self::STRING_T ];
        static $PHP71TypeHints = [ self::ITERABLE_T ];
        static $PHP72TypeHints = [ self::OBJECT_KW ];
        if( null === $phpVersion ) {
            $phpMajor = PHP_MAJOR_VERSION;
            $phpMinor = PHP_MINOR_VERSION;
        }
        else {
            $std      = explode( $DOT, $phpVersion, 3 );
            $phpMajor = (int)$std[ 0 ];
            $phpMinor = (int)$std[ 1 ];
        }
        $return = true;
        switch( true ) {
            case in_array( $varType, $PHP5TypeHints ) :
                $typeHint = $varType;
                break;
            case ( ( 5 == $phpMajor ) &&
                ( in_array( $varType, $PHP70TypeHints ) ||
                  in_array( $varType, $PHP71TypeHints ) ||
                  in_array( $varType, $PHP72TypeHints ))) :
                $return = false;
                break;
            case ( ( 7 == $phpMajor ) && in_array( $varType, $PHP70TypeHints )) :
                $typeHint = $varType;
                break;
            case ( ( 7 == $phpMajor ) && in_array( $varType, $PHP71TypeHints )) :
                if( 1 > $phpMinor ) {
                    $return = false;
                }
                $typeHint = $varType;
                break;
            case ( ( 7 == $phpMajor ) && in_array( $varType, $PHP72TypeHints )) :
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
        }
        return $return;
    }

    /**
     * Return the typed array value or the default one
     *
     * @param array      $array
     * @param string|int $key
     * @param string     $type
     * @param mixed      $default
     * @return mixed
     */
    public static function getIfSet( array $array, $key, $type = null, $default = null ) {
        if( ! array_key_exists( $key, $array )) {
            return $default;
        }
        switch( $type ) {
            case self::BOOL_T :
                return (bool)$array[ $key ];
                break;
            case self::STRING_T :
                return (string)$array[ $key ];
                break;
            case self::ARRAY_T :
                return (array)$array[ $key ];
                break;
            default :
                return $array[ $key ];
                break;
        } // end switch
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isConstant( $value ) {
        if( ! is_string( $value ) || Util::isVarPrefixed( $value ) ||
            Util::isInt( substr( (string)$value, 0, 1 ))) {
            return false;
        }
        return ( strtoupper( $value ) == $value );
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isFloat( $value ) {
        static $FLOATS = [ 'double', 'float' ];
        static $PATTERN = "/^\\d+\\.\\d+$/";
        if( ! is_scalar( $value )) {
            return false;
        }
        if( in_array( gettype( $value ), $FLOATS )) {
            return true;
        }
        return ( 1 === preg_match( $PATTERN, $value ));
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isInt( $value ) {
        return ( ! is_int( $value ) ? ctype_digit( $value ) : true );
    }

    public static function isVarPrefixed( $value ) {
        return ( is_string( $value ) && ( BaseA::VARPREFIX == substr( $value, 0, 1 )));
    }

    /**
     * Clean value of null bytes
     *
     * @param string|array $value
     * @return string|array
     */
    public static function nullByteClean( $value ) {
        static $CHR0 = null;
        static $SP0 = '';
        if( null === $CHR0 ) {
            $CHR0 = chr( 0 );
        }
        if( is_array( $value )) {
            foreach( $value as $rowIx => $line ) {
                $value[ $rowIx ] = self::nullByteClean( $line );
            }
            return $value;
        }
        return empty( $value ) ? $SP0 : str_replace( $CHR0, $SP0, $value );
    }

    /**
     * @param bool|float|int|string
     * @return string
     * @throws RuntimeException
     */
    public static function renderScalarValue( $value ) {
        if( ! is_scalar( $value )) {
            throw new RuntimeException( sprintf( BaseA::$ERRx, var_export( $value, true )));
        }
        static $TRUE  = 'true';
        static $FALSE = 'false';
        static $DOT   = '.';
        static $ZERO0 = '0.0';
        static $SP0   = '';
        static $ZERO  = '0';
        static $QUOTE = '\'%s\'';
        switch( true ) {
            case is_bool( $value ) :
                return $value ? $TRUE : $FALSE;
                break;
            case Util::isInt( $value ) :
                return (string)$value;
                break;
            case Util::isFloat( $value ) :
                switch( true ) {
                    case empty( $value ) :
                        return $ZERO0;
                        break;
                    case ( 0.0001 > abs( $value )) :
                        // make float to string AND preserve fraction
                        $value     *= 1000000;
                        $precision = strlen( $value ) - strpos( $value, $DOT ) - 1 + 7;
                        return rtrim( number_format( ( $value / 1000000 ), $precision, $DOT, $SP0 ), $ZERO );
                        break;
                    default :
                        $precision = strlen( $value ) - strpos( $value, $DOT ) - 1;
                        return number_format( $value, $precision, $DOT, $SP0 );
                }
                break;
            default :
                return sprintf( $QUOTE, $value );
                break;
        }
    }

}
