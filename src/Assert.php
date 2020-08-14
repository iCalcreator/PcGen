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

use InvalidArgumentException;

/**
 * Class Assert
 *
 * More general asserts
 *
 * @package Kigkonsult\PcGen
 */
class Assert
{
    /**
     * Assert valid PHP property/variable variable ($-class or leading alpha/digit), return non-$-prefixed variable
     *
     * @param mixed  $fqcn
     * @return void
     * @throws InvalidArgumentException
     */
    public static function assertFqcn( $fqcn )
    {
        static $DS = "\\";
        foreach( explode( $DS, $fqcn ) as $part ) {
            self::assertPhpVar( $part );
        }
    }

    /**
     * @param string $indent
     * @return void
     * @throws InvalidArgumentException
     */
    public static function assertIndent( $indent )
    {
        if( BaseA::SP0 != trim( $indent)) {
            throw new InvalidArgumentException(
                sprintf( BaseA::$ERRx, var_export( $indent, true ))
            );
        }
    }

    /**
     * @var array   PHP reserved words
     */
    private static $RESERVEDWORDS = [
        '__halt_compiler',
        'abstract', 'and', 'array', 'as',
        'break',
        'callable', 'case', 'catch', 'class', 'clone', 'const', 'continue',
        'datetime',
        'declare', 'default', 'die', 'do',
        'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor', 'endforeach',
        'endif', 'endswitch', 'endwhile', 'eval', 'exit', 'extends',
        'final', 'finally', 'for', 'foreach', 'function',
        'global', 'goto',
        'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'isset',
        'list',
        'namespace', 'new',
        'or',
        'print', 'private', 'protected', 'public',
        'require', 'require_once', 'return',
        'static', 'switch',
        'throw', 'trait', 'try',
        'unset', 'use',
        'var',
        'while',
        'xor',
        'yield',
        '__CLASS__', '__DIR__', '__FILE__', '__FUNCTION__', '__LINE__', '__METHOD__', '__NAMESPACE__', '__TRAIT__',
        'Directory', 'stdClass', 'Exception', 'ErrorException', 'Closure', 'Generator',
        'ArithmeticError', 'AssertionError', 'DivisionByZeroError', 'Error', 'Throwable', 'ParseError', 'TypeError',
        'this', 'self', 'parent',
        'int', 'float', 'bool', 'string', 'true', 'false', 'null', 'void', 'iterable', 'object',
        'boolean', 'double', 'integer',
        'resource', 'mixed', 'numeric',
    ];

    /**
     * Assert valid PHP property/variable variable ($-class or leading alpha/digit), return non-$-prefixed variable
     *
     * @param mixed  $value
     * @return string
     * @throws InvalidArgumentException
     * @todo allow variable property names (ex '{$varDto}') ??
     */
    public static function assertPhpVar( $value )
    {
        static $PATTERN = '/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/';
        static $ERR     = 'Invalid PHP value : %s';
        $value2 = Util::unSetVarPrefix( $value );
        if( empty( $value2 ) || ( 1 != preg_match( $PATTERN, $value2 )) ) {
            throw new InvalidArgumentException(
                sprintf( $ERR, var_export( $value, true ))
            );
        }
        $value = trim( (string) $value );
        foreach( self::$RESERVEDWORDS as $reserved ) {
            if( 0 === strcasecmp( $reserved, $value )) {
                throw new InvalidArgumentException(
                    sprintf( $ERR, var_export( $value, true ))
                );
            }
        } // end foreach
        return $value2;
    }
}
