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

use InvalidArgumentException;
use Kigkonsult\PcGen\Dto\ArgumentDto;
use Kigkonsult\PcGen\Dto\VarDto;
use Kigkonsult\PcGen\Traits\ArgumentTrait;
use Kigkonsult\PcGen\Traits\NameTrait;
use RuntimeException;

use function array_merge;
use function count;
use function in_array;
use function is_array;
use function is_scalar;
use function is_string;
use function sprintf;
use function substr;
use function var_export;

/**
 * Class FcnFrameMgr
 *
 * Manages function/methods frame
 *   opt closure (ie anonymous function)
 *   opt arguments
 *   opt class instance property value set from (same name) argument
 *   input of body (logic)
 *   opt function/method return, without value or variable, property, constant or scalar
 *
 * @package Kigkonsult\PcGen
 */
final class FcnFrameMgr extends BaseC
{
    use NameTrait;

    use ArgumentTrait;

    /**
     * @var ArgumentDto[]   array of closure use variables
     */
    private $varUses = [];

    /**
     * @var null|ReturnClauseMgr
     */
    private $returnValue = null;

    /**
     * Function return type, i.e. the type of the value that will be returned from a function, PHP 7+
     *
     * @var string
     */
    private $returnType = null;

    /**
     * @param string $name
     * @param array $arguments
     * @return static
     */
    public static function factory( string $name, array $arguments = null ) : self
    {
        $instance = self::init()->setName( $name );
        if( ! empty( $arguments )) {
            $instance->setArguments( $arguments );
        }
        return $instance;
    }

    /**
     * Return code as array (with NO eol at line endings)
     *
     * @return array
     * @throws RuntimeException
     */
    public function toArray() : array
    {
        static $ERR = 'No function directives';
        if( ! $this->isNameSet() &&
            empty( $this->getArgumentCount()) &&
            ! $this->isBodySet() &&
            ! $this->isReturnValueSet()) {
            throw new RuntimeException( $ERR );
        }
        $leadFound = $trailFound = false;
        $leadCode  = $this->getPropValueSetCode( ArgumentDto::BEFORE, $leadFound  );
        $trailCode = $this->getPropValueSetCode( ArgumentDto::AFTER, $trailFound  );
        $body      = $this->getBody( $this->indent );
        if( ! $leadFound ) {
            $body = Util::trimLeading( $body );
        }
        if( ! $trailFound ) {
            $body = Util::trimTrailing( $body );
        }
        return Util::nullByteCleanArray(
            array_merge(
                $this->initCode(),
                $leadCode,
                $body,
                $trailCode,
                $this->exitCode()
            )
        );
    }

    /**
     * Function start code
     *
     * @return array
     */
    private function initCode() : array
    {
        static $FUNCTION  = 'function';
        static $SP1CLNSP1 = ' : ';
        $row      = $this->baseIndent;
        if( $this->isVisibilitySet()) {
            $row .= $this->visibility . self::SP1;
        }
        if( $this->isStatic()) {
            $row .= self::$STATIC . self::SP1;
        }
        $row     .= $FUNCTION;
        if( $this->isNameSet()) {  // no closure
            $row .= self::SP1 . $this->getName();
        }
        $code   = $this->renderArguments( $row );
        $code   = $this->renderClosureUseVariables( $code );
        if( $this->isReturnTypeSet()) {
            $lastIx = count( $code ) - 1;
            $code[$lastIx] .= $SP1CLNSP1 . $this->getReturnType();
        }
        $code[] = $this->baseIndent . self::$CODEBLOCKSTART;
        return $code;
    }

    /**
     * @param array $code
     * @return array
     */
    private function renderClosureUseVariables( array $code ) : array
    {
        static $USESTART = 'use ';
        static $ARGSTART = '(';
        static $ARGEND   = ')';
        if( empty( $this->varUses )) {
            return $code;
        }
        $lastIx  = count( $code ) - 1;
        $cntUse  = count( $this->varUses );
        if( 4 < $this->getArgumentCount()) {
            $lastIx += 1;
            $code[$lastIx] = self::SP0;
        }
        $code[$lastIx] .= self::SP1 . $USESTART . $ARGSTART;
        if( 4 >= $cntUse ) {
            $code[$lastIx] .= self::renderArgsInOneRow( $this->varUses );
        }
        else {
            $this->renderArgsInRows( $this->varUses, $code );
            $lastIx += 1;
            $code[$lastIx] = self::SP0;
        }
        $code[$lastIx] .= $ARGEND;
        return $code;
    }

    /**
     * Produce code setting class instance property value from (same named) param (placed before/after opt body)
     *
     * Opt ReturnValue after (and after body)
     *
     * @param int  $firstLast   1=before, 9 = after, 0 = none
     * @param bool $found
     * @return array
     */
    private function getPropValueSetCode( int $firstLast, & $found = false ) : array
    {
        $code  = [];
        $found = false;
        foreach( $this->getArgumentIndex() as $argIx ) {
            $argumentDto = $this->getArgument( $argIx );
            if( $firstLast == $argumentDto->getUpdClassProp()) {
                $found = true;
                $code  = array_merge( $code,
                    ClassMethodFactory::renderPropValueSetCode( $argumentDto, $this )
                );
            }
        } // end foreach
        return $code;
    }

    /**
     * Produce method end up code, opt with return (variable/property) value code (placed after any body)
     *
     * @return array
     */
    private function exitCode() : array
    {
        return array_merge(
            (
                $this->isReturnValueSet()
                    ? $this->returnValue->setIndent( $this->getIndent())
                           ->setBaseIndent( $this->getBaseIndent())
                           ->toArray()
                    : []
            ),
            [ $this->baseIndent . self::$CODEBLOCKEND ]
        );
    }

    /**
     * Add closure use variable
     *
     * @param ArgumentDto|VarDto|string $name
     * @param bool   $byReference
     * @return static
     * @throws InvalidArgumentException
     */
    public function addVarUse( $name, $byReference = false ) : self
    {
        switch( true ) {
            case ( $name instanceof ArgumentDto ) :
                $this->varUses[] = $name;
                break;
            case ( $name instanceof VarDto ) :
                $this->varUses[] = ArgumentDto::factory( $name )
                    ->setByReference( $byReference ?? false );
                break;
            case is_string( $name ) :
                $this->varUses[] = ArgumentDto::factory( $name )
                    ->setByReference( $byReference ?? false );
                break;
            default :
                throw new InvalidArgumentException(
                    sprintf( self::$ERRx, var_export( $name, true ))
                );
        } // end switch
        return $this;
    }

    /**
     * String closure use string|array, closure use variable variable(s)
     *
     * Each argument is a variable, an ArgumentDto, VarDto OR an array( VarDto/variable (, byReference ))
     *
     * @param array $varUse
     * @return static
     * @throws InvalidArgumentException
     */
    public function setVarUse( $varUse = null ) : self
    {
        static $CLOSUREUSE = 'closure use';
        $this->varUses = [];
        if( null === $varUse ) {
            return $this;
        }
        foreach( $varUse as $argSet ) {
            switch( true ) {
                case empty( $argSet ) :
                    throw new InvalidArgumentException( sprintf( self::$ERR1, $CLOSUREUSE ));
                case ( $argSet instanceof ArgumentDto ) :
                    $this->addVarUse( $argSet );
                    break;
                case (( $argSet instanceof VarDto ) || is_string( $argSet )) :
                    $this->addVarUse( ArgumentDto::factory( $argSet ));
                    break;
                case ! is_array( $argSet ) :
                    throw new InvalidArgumentException(
                        sprintf( self::$ERRx, var_export( $argSet, true ))
                    );
                case ( $argSet[0] instanceof VarDto ) :
                    $this->addVarUse(
                        ArgumentDto::factory( $argSet[0] )
                            ->setByReference( $argSet[1] ?? false )
                    );
                    break;
                default :
                    $this->addVarUse(
                        ArgumentDto::factory( $argSet[0] )
                            ->setByReference( $argSet[1] ?? false )
                    );
            } // end switch
        } // end foreach
        return $this;
    }

    /**
     * @return null|ReturnClauseMgr
     */
    public function getReturnValue()
    {
        return $this->returnValue;
    }

    /**
     * @return bool
     */
    public function isReturnValueSet() : bool
    {
        return ( null !== $this->returnValue );
    }

    /**
     * @param null|string     $prefix null, 'parent', 'self', 'static', 'this', fqcn, '$class'
     * @param null|mixed      $source int, float, string, '$name', constant
     * @param null|int|string $index  int, string (array index)
     * @return static
     * @throws InvalidArgumentException
     */
    public function setReturnValue( $prefix = null, $source = null, $index = null ) : self
    {
        if( empty( $prefix ) && is_scalar( $source ) && ! is_string( $source )) {
            return $this->setReturnFixedValue( $source );
        }
        $this->returnValue = ReturnClauseMgr::init( $this )
            ->setSource( $prefix, $source, $index );
        return $this;
    }

    /**
     * Set a fixed (scalar) return value, setReturnValue method alias
     *
     * @param bool|int|float|string $value
     * @return static
     * @throws InvalidArgumentException
     */
    public function setReturnFixedValue( $value ) : self
    {
        $this->returnValue =
            ReturnClauseMgr::init( $this )->setScalar( $value );
        $this->setReturnType( gettype( $value ));
        return $this;
    }

    /**
     * Set directive for method end-up return class 'return $this->property;' code, setReturnValue method alias
     *
     * @param null|mixed      $source
     * @param null|int|string $index
     * @return static
     * @throws InvalidArgumentException
     */
    public function setReturnProperty( $source = null, $index = null ) : self
    {
        $this->setReturnValue( self::THIS_KW, $source, $index );
        return $this;
    }

    /**
     * Set directive for method end-up 'return $this;', setReturnValue method alias
     *
     * Also updates function returnType, self
     *
     * @return static
     */
    public function setReturnThis() : self
    {
        $this->setReturnValue( self::THIS_KW );
        return $this;
    }

    /**
     * Set directive for function end-up return non-class 'return $variable;' code, setReturnValue method alias
     *
     * String array subjectIndex not allowed
     *
     * @param mixed      $source
     * @param null|int|string $index
     * @return static
     * @throws InvalidArgumentException
     */
    public function setReturnVariable( $source, $index = null ) : self
    {
        if( is_string( $source )) {
            $source = Util::setVarPrefix( $source );
        }
        $this->setReturnValue( null, $source, $index );
        return $this;
    }

    /**
     * @return static
     */
    public function unsetReturnValue() : self
    {
        $this->returnValue = null;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getReturnType()
    {
        return $this->returnType;
    }

    /**
     * @return bool
     */
    public function isReturnTypeSet() : bool
    {
        return ( null !== $this->returnType );
    }

    /**
     * Set function return value type, PHP 7+ only, accepts type values types + <any>[] as array
     *
     * bool, double, float, int, integer, string, array, callable and interfaces
     * @link https://www.tutorialspoint.com/php7/php7_returntype_declarations.htm
     *
     * @param string $returnType
     * @return static
     * @throws InvalidArgumentException
     */
    public function setReturnType( string $returnType ) : self
    {
        static $ERRTXT = 'Invalid function return value type : ';
        static $TYPES = [
            self::INT_T,
            self::FLOAT_T,
            self::BOOL_T,
            self::STRING_T,
            self::ARRAY_T,
            self::CALLABLE_T,
        ];
        static $TYPES2 = [
            self::BOOLEAN_T => self::BOOL_T,
            self::INTEGER   => self::INT_T,
            self::DOUBLE    => self::FLOAT_T,
        ];
        switch( true ) {
            case ( 7 > substr( self::getTargetPhpVersion(), 0, 1)) :
                return $this;
            case ( ! is_string( $returnType )) :
                throw new InvalidArgumentException(
                    $ERRTXT . var_export( $returnType, true )
                );
            case( self::ARRAY2_T == substr( $returnType, -2 )) :
                $returnType = self::ARRAY_T;
                break;
            case isset( $TYPES2[$returnType] ) :
                $returnType = $TYPES2[$returnType];
                break;
            case( in_array( $returnType, $TYPES )) :
                break;
            case is_string( $returnType ) :  // interfaces...
                break;
            default :
                throw new InvalidArgumentException(
                    $ERRTXT . var_export( $returnType, true )
                );
        } // end switch
        $this->returnType = $returnType;
        return $this;
    }
}
