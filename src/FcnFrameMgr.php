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
use Kigkonsult\PcGen\Dto\ArgumentDto;
use Kigkonsult\PcGen\Dto\VarDto;
use Kigkonsult\PcGen\Traits\ArgumentTrait;
use RuntimeException;

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
final class FcnFrameMgr extends BaseC implements PcGenInterface
{

   use ArgumentTrait;

    /**
     * @var ArgumentDto[]   array of closure use variables
     */
    private $varUses = [];

    /**
     * @var ReturnClauseMgr
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
    public static function factory( $name, array $arguments = null ) {
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
    public function toArray() {
        static $ERR = 'No function directives';
        static $BEFORE = 1;
        static $AFTER  = 9;
        if( ! $this->isNameSet() &&
            empty( $this->getArgumentCount()) &&
            ! $this->isBodySet() &&
            ! $this->isReturnValueSet()) {
            throw new RuntimeException( $ERR );
        }
        $code = array_merge(
            $this->initCode(),
            $this->setPropSetCode( $BEFORE ),
            $this->getBody( $this->indent ),
            $this->setPropSetCode( $AFTER ),
            $this->exitCode()
        );
        return Util::nullByteClean( $code );
    }

    /**
     * Function start code
     *
     * @return array
     */
    private function initCode() {
        static $FUNCTION  = 'function';
        static $COLONSP1 = ': ';
        $row      = $this->baseIndent;
        if( $this->isVisibilitySet()) {
            $row .= $this->visibility . self::$SP1;
        }
        if( $this->isStatic()) {
            $row .= self::$STATIC . self::$SP1;
        }
        $row     .= $FUNCTION;
        if( $this->isNameSet()) {  // no closure
            $row .= self::$SP1 . $this->getName();
        }
        $code   = $this->renderArguments( $row );
        $code   = $this->renderClosureUseVariables( $code );
        $lastIx = count( $code ) - 1;
        $code[$lastIx] .= self::$SP1;
        if(( 7 <= substr( self::getTargetPhpVersion(), 0, 1)) && $this->isReturnTypeSet()) {
            $code[$lastIx] .= $COLONSP1 . $this->getReturnType() . self::$SP1;
        }
        $code[$lastIx] .= self::$CODEBLOCKSTART;
        return $code;
    }

    /**
     * @param array $code
     * @return array
     */
    private function renderClosureUseVariables( array $code ) {
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
            $code[$lastIx] = self::$SP0;
        }
        $code[$lastIx] .= self::$SP1 . $USESTART . $ARGSTART;
        if( 4 >= $cntUse ) {
            $code[$lastIx] .= self::renderArgsInOneRow( $this->varUses );
        }
        else {
            $this->renderArgsInRows( $this->varUses, $code );
            $lastIx += 1;
            $code[$lastIx] = self::$SP0;
        }
        $code[$lastIx] .= $ARGEND;
        return $code;
    }

    /**
     * Produce (method) code setting property value from param (placed before/after opt body)
     *
     * Opt ReturnValue after body
     *
     * @param int $firstLast   1=before, 9 = after, 0 = none
     * @return array
     */
    private function setPropSetCode( $firstLast ) {
        $code = [];
        foreach( $this->getArgumentIndex() as $argIx ) {
            $argumentDto = $this->getArgument( $argIx );
            if( $firstLast != $argumentDto->getUpdClassProp()) {
                continue;
            }
            $code = array_merge( $code,
                AssignClauseMgr::init()
                    ->setTarget(
                        self::THIS_KW,
                        $argumentDto->getName(),
                        ( $argumentDto->isNextVarPropIndex() ? self::ARRAY2_T : null )
                    )
                    ->setSource( null, self::VARPREFIX . $argumentDto->getName() )
                    ->toArray()
            );
        }
        return $code;
    }

    /**
     * Produce method end up code, opt with return (variable/property) value code (placed after any body)
     *
     * @return array
     */
    private function exitCode() {
        return array_merge(
            ( $this->isReturnValueSet() ? $this->returnValue->toArray() : [] ),
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
    public function addVarUse( $name, $byReference = false ) {
        switch( true ) {
            case ( $name instanceof ArgumentDto ) :
                $this->varUses[] = $name;
                break;
            case ( $name instanceof VarDto ) :
                $this->varUses[] = ArgumentDto::factory( $name )->setByReference( (bool) $byReference );
                break;
            case is_string( $name ) :
                $this->varUses[] = ArgumentDto::factory( $name )->setByReference( (bool) $byReference );
                break;
            default :
                throw new InvalidArgumentException( sprintf( self::$ERRx, var_export( $name, true )));
                break;
        }
        return $this;
    }

    /**
     * String closure use string|array of closure use variable variable(s)
     *
     * Each argument is a variable, an ArgumentDto, VarDto OR an array( VarDto/variable (, byReference ))
     *
     * @param string|array $varUse
     * @return static
     * @throws InvalidArgumentException
     */
    public function setVarUse( $varUse = null ) {
        static $CLOSUREUSE = 'closure use';
        if( null === $varUse ) {
            $this->varUses = [];
            return $this;
        }
        if( ! is_array( $varUse )) {
            $varUse = [ $varUse ];
        }
        foreach( $varUse as $argSet ) {
            switch( true ) {
                case empty( $argSet ) :
                    throw new InvalidArgumentException( sprintf( self::$ERR1, $CLOSUREUSE ));
                    break;
                case ( $argSet instanceof ArgumentDto ) :
                    $this->addVarUse( $argSet );
                    break;
                case ( $argSet instanceof VarDto ) :
                    $this->addVarUse( ArgumentDto::factory( $argSet ));
                    break;
                case is_string( $argSet ) :
                    $this->addVarUse( ArgumentDto::factory( $argSet ));
                    break;
                case ! is_array( $argSet ) :
                    throw new InvalidArgumentException( sprintf( self::$ERRx, var_export( $argSet, true )));
                    break;
                case ( $argSet[0] instanceof VarDto ) :
                    $this->addVarUse(
                        ArgumentDto::factory( $argSet[0] )
                            ->setByReference( Util::getIfSet( $argSet, 1, self::BOOL_T, false ) )
                    );
                    break;
                default :
                    $this->addVarUse(
                        ArgumentDto::factory( $argSet[0] )
                            ->setByReference( Util::getIfSet( $argSet, 1, self::BOOL_T, false ) )
                    );
            } // end switch
        } // end foreach
        return $this;
    }

    /**
     * @return ReturnClauseMgr
     */
    public function getReturnValue() {
        return $this->returnValue;
    }

    /**
     * @return bool
     */
    public function isReturnValueSet() {
        return ( null !== $this->returnValue );
    }

    /**
     * @param string     $prefix null, 'self', 'static', 'this', fqcn, '$class'
     * @param mixed      $source int, float, string, '$name', constant
     * @param int|string $index  int, string (array index)
     * @return static
     * @throws InvalidArgumentException
     */
    public function setReturnValue( $prefix = null, $source = null, $index = null ) {
        if( empty( $prefix ) && is_scalar( $source ) && ! is_string( $source )) {
            return $this->setReturnFixedValue( $source );
        }
        $this->returnValue = ReturnClauseMgr::factory( $prefix, $source, $index );
        return $this;
    }

    /**
     * Set a fixed (scalar) return value, setReturnValue method alias
     *
     * @param bool|int|float|string $value
     * @return static
     * @throws InvalidArgumentException
     */
    public function setReturnFixedValue( $value ) {
        static $DOUBLE = 'double';
        $this->returnValue = ReturnClauseMgr::init()->setFixedSourceValue( $value );
        $valueType = gettype( $value );
        $this->setReturnType((( $DOUBLE == $valueType ) ? self::FLOAT_T : $valueType ));
        return $this;
    }

    /**
     * Set directive for method end-up return class 'return $this->property;' code, setReturnValue method alias
     *
     * @param mixed      $source
     * @param int|string $index
     * @return static
     * @throws InvalidArgumentException
     */
    public function setReturnProperty( $source = null, $index = null ) {
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
    public function setReturnThis() {
        $this->setReturnValue( self::THIS_KW );
        $this->setReturnType( self::SELF_KW );
        return $this;
    }

    /**
     * Set directive for function end-up return non-class 'return $variable;' code, setReturnValue method alias
     *
     * String array subjectIndex not allowed
     *
     * @param mixed      $source
     * @param int|string $index
     * @return static
     * @throws InvalidArgumentException
     */
    public function setReturnVariable( $source, $index = null ) {
        if( is_string( $source ) && ! Util::isVarPrefixed( $source ) ) {
            $source = self::VARPREFIX . $source;
        }
        $this->setReturnValue( null, $source, $index );
        return $this;
    }

    /**
     * @return static
     */
    public function unsetReturnValue() {
        $this->returnValue = null;
        return $this;
    }

    /**
     * @return string
     */
    public function getReturnType() {
        return $this->returnType;
    }

    /**
     * @return bool
     */
    public function isReturnTypeSet() {
        return ( null !== $this->returnType );
    }

    /**
     * @param string $returnType
     * @return static
     */
    public function setReturnType( $returnType ) {
        $this->returnType = $returnType;
        return $this;
    }

}
