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
     * Return the typed (arg) value or the default one
     *
     * @param mixed      $arg
     * @param string|int $key
     * @param string     $type
     * @param mixed      $default
     * @return mixed
     */
    public static function getIfSet( $arg, $key = null, $type = null, $default = null ) {
        switch( true ) {
            case (( null === $arg ) && ( null === $key )) :
                return $default;
                break;
            case ( ! is_array( $arg )) :
                $value = $arg;
                break;
            case ( ! array_key_exists( $key, $arg )) :
                return $default;
                break;
            default :
                $value = $arg[ $key ];
                break;
        }
        switch( $type ) {
            case self::BOOL_T :
                return (bool) $value;
                break;
            case self::STRING_T :
                return (string) $value;
                break;
            case self::ARRAY_T :
                return (array) $value;
                break;
            default :
                return $value;
                break;
        } // end switch
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

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isVarPrefixed( $value ) {
        return ( is_string( $value ) && ( self::VARPREFIX == substr( $value, 0, 1 )));
    }

    /**
     * @param string $value
     * @return string
     */
    public static function setVarPrefix( $value ) {
        $value = trim( $value );
        return self::isVarPrefixed( $value ) ? $value : self::VARPREFIX . $value;
    }

    /**
     * @param string $value
     * @return string
     */
    public static function unSetVarPrefix( $value ) {
        $value = trim( $value );
        return self::isVarPrefixed( $value ) ? substr( $value, 1 )  : $value;
    }

    /**
     * Clean value of null bytes
     *
     * @param string|array $value
     * @return string|array
     */
    public static function nullByteClean( $value ) {
        static $CHR0 = null;
        if( null === $CHR0 ) {
            $CHR0 = chr( 0 );
        }
        if( is_array( $value )) {
            foreach( $value as $rowIx => $line ) {
                $value[ $rowIx ] = self::nullByteClean( $line );
            }
            return $value;
        }
        return empty( $value ) ? self::SP0 : str_replace( $CHR0, self::SP0, $value );
    }

    /**
     * @param bool|float|int|string $value
     * @param string                $expType  (string?)
     * @return string
     * @throws RuntimeException
     */
    public static function renderScalarValue( $value, $expType = null ) {
        if( ! is_scalar( $value )) {
            throw new RuntimeException( sprintf( BaseA::$ERRx, var_export( $value, true )));
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
            case ( self::anyCaseStrInArray( $expType, $BOOLTYPES ) &&
                is_string( $value ) && self::anyCaseStrInArray( $value, $BOOLVALUEARR )) :
                return strtolower( $value );
                break;
            case is_bool( $value ) :
                return $value ? $TRUE : $FALSE;
                break;
            case ( Util::isInt( $value ) && ( self::STRING_T != $expType )) :
                return (string) $value;
                break;
            case Util::isFloat( $value ) :
                switch( true ) {
                    case empty( $value ) :
                        $value = $ZERO0;
                        break;
                    case ( 0.0001 > abs( $value )) :
                        // make float to string AND preserve fraction
                        $value     *= 1000000;
                        $precision = strlen( $value ) - strpos( $value, $DOT ) - 1 + 7;
                        $value = rtrim(
                            number_format( ( $value / 1000000 ), $precision, $DOT, self::SP0 )
                            , $ZERO
                        );
                        break;
                    default :
                        $precision = strlen( $value ) - strpos( $value, $DOT ) - 1;
                        $value = number_format( $value, $precision, $DOT, self::SP0 );
                }
                return ( self::STRING_T != $expType ) ? $value : sprintf( $QUOTE1, $value );
                break;
            default :
                $tmpl = ( false !== strpos( $value, $Q2 )) ? $QUOTE1 : $QUOTE2;
                return sprintf( $tmpl, $value );
                break;
        } // end switch
    }

    /**
     * @param string $value
     * @param array  $hayStack
     * @return bool
     */
    private static function anyCaseStrInArray( $value, array $hayStack ) {
        foreach( $hayStack as $item ) {
            if( 0 == strcasecmp( $value, $item )) {
                return true;
            }
        }
        return false;
    }

}
