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
namespace Kigkonsult\PcGen\Traits;

use InvalidArgumentException;
use Kigkonsult\PcGen\BaseA;
use Kigkonsult\PcGen\Dto\ArgumentDto;
use Kigkonsult\PcGen\Dto\VarDto;
use Kigkonsult\PcGen\Util;

use function array_key_exists;
use function array_keys;
use function count;
use function implode;
use function in_array;
use function is_bool;
use function is_scalar;
use function is_string;
use function reset;
use function strcasecmp;
use function sprintf;
use function var_export;

/**
 * Trait ArgumentTrait
 *
 * Manages argument in Fcn*Mgr
 *
 * @package Kigkonsult\PcGen\Traits
 */
trait ArgumentTrait
{
    /**
     * @var ArgumentDto[]   array of arguments
     */
    private $arguments = [];

    /**
     * @param string $row
     * @return array
     */
    private function renderArguments( string $row ) : array
    {
        static $ARGSTART = '(';
        static $ARGEND   = ')';
        $cntArgs = count( $this->arguments );
        $row    .= $ARGSTART;
        switch( true ) {
            case empty( $cntArgs ) :
                break;
            case ( 1 == $cntArgs ) :
                $row .= BaseA::SP1 . self::renderOneArg( reset( $this->arguments )) . BaseA::SP1;
                break;
            case (( 4 >= $cntArgs ) && self::hasOnlyNames( $this->arguments )) :
                $row .= self::renderArgsInOneRow( $this->arguments );
                break;
            default :
                $code[] = $row;
                $this->renderArgsInRows( $this->arguments, $code );
                $code[] = $this->baseIndent . $ARGEND;
                return $code;
        } // end switch
        return [ $row . $ARGEND ];
    }


    /**
     * @param ArgumentDto $argumentDto
     * @return string
     * @todo fix PHP_VERSION for typeHint
     */
    private static function renderOneArg( ArgumentDto $argumentDto ) : string
    {
        static $REFERENCE = '& ';
        static $SPEQSP    = ' = ';
        static $ARRSTART  = '[ ';
        static $ARREND    = ']';
        $row = BaseA::SP0;
        if( $argumentDto->isNextVarPropIndex()) {
            // add array element
            $argumentDto = clone $argumentDto;
            // need a clone here, without type? and default, and remain original for (opt) later use
            $argumentDto->setVarType(
                ( $argumentDto->hasTypeHintArraySpec( self::getTargetPhpVersion(), $typeHint ) ? $typeHint : null )
            );
            $argumentDto->setDefault(); // null
        } // end if
        if( $argumentDto->isTypeHint( self::getTargetPhpVersion(), $typeHint )) { // varType
            $row .= $typeHint . BaseA::SP1;
        }
        if( $argumentDto->isByReference()) { // by reference
            $row .= $REFERENCE;
        }
        $row .= Util::setVarPrefix( $argumentDto->getName()); // the $-prefixed argument variable
        if( ! $argumentDto->isDefaultSet()) { // value is not set
            return $row;
        }
        $initValue = $argumentDto->getDefault();
        switch( true ) {
            case ( null === $initValue ) :
                $row .= $SPEQSP . BaseA::NULL_T;
                break;
            case ( $argumentDto->isDefaultTypedArray() && ( in_array( $initValue, VarDto::$ARRAYs ))) :
                $row .= $SPEQSP . BaseA::ARRAY2_T;
                break;
            case ( is_string( $initValue ) && ( 0 === strcasecmp( BaseA::NULL_T, $initValue ))) :
                $row .= $SPEQSP . BaseA::NULL_T;
                break;
            case is_scalar( $initValue ) :
                $row .= $SPEQSP . Util::renderScalarValue( $initValue, $argumentDto->getVarType());
                break;
            case $argumentDto->isDefaultArray() :
                $row .= $SPEQSP;
                if( empty( $initValue )) {
                    $row .= BaseA::ARRAY2_T;
                    break;
                }
                $expType = $argumentDto->hasTypeHintArraySpec( null, $typeHint ) ? $typeHint : null;
                foreach( $initValue as & $item ) {
                    $item = Util::renderScalarValue( $item, $expType ) . BaseA::$COMMA . BaseA::SP1;
                }
                $row .= $ARRSTART . implode( BaseA::SP0, $initValue ) . $ARREND;
                break;
            default :
                break;
        } // end switch
        return $row;
    }

    /**
     * @param ArgumentDto[] $arguments
     * @return bool
     */
    private static function hasOnlyNames( array $arguments ) : bool
    {
        foreach( array_keys( $arguments) as $argIx ) {
            if( $arguments[$argIx]->isVarTypeSet()) {
                return false;
            }
            if( $arguments[$argIx]->isDefaultSet()) {
                return false;
            }
        } // end foreach
        return true;
    }

    /**
     * Render arguments in one row
     *
     * @param ArgumentDto[] $arguments
     * @return string
     */
    private static function renderArgsInOneRow( array $arguments ) : string
    {
        $lastIx = count( $arguments ) - 1;
        $row    = BaseA::SP0;
        foreach( array_keys( $arguments) as $argIx ) {
            $row .= BaseA::SP1 . self::renderOneArg( $arguments[$argIx] );
            $row .= ( $lastIx != $argIx ) ? BaseA::$COMMA : BaseA::SP1;
        }
        return $row;
    }

    /**
     * Render arguments in separate rows
     *
     * @param ArgumentDto[] $arguments
     * @param array $code
     * @return void
     */
    private function renderArgsInRows( array $arguments, array & $code )
    {
        $lastIx  = count( $this->arguments ) - 1;
        foreach( array_keys( $arguments) as $argIx ) {
            $row = $this->baseIndent . $this->indent . self::renderOneArg( $arguments[$argIx] );
            if( $lastIx != $argIx ) {
                $row .= BaseA::$COMMA;
            }
            $code[] = $row;
        } // end foreach
    }

    /**
     * Add argument, ArgumentDto/varDto/array($variable,$type,$default,$byReference,$updClassProp,$intoNextVarPropArrItem)
     *
     * @param string|VarDto|ArgumentDto $name
     * @param null|bool|string $type        also varType hint, no validation
     * @param null|mixed    $default        no validation
     * @param null|bool     $byReference
     * @param null|bool|int $updClassProp
     * @param null|bool     $intoNextVarPropArrItem
     * @return static
     * @throws InvalidArgumentException
     */
    public function addArgument(
        $name,
        $type = null,
        $default = null,
        $byReference = false,
        $updClassProp = false,
        $intoNextVarPropArrItem = false
    ) : self
    {
        switch( true ) {
            case ( $name instanceof ArgumentDto ) :
                $this->arguments[] = $name;
                break;
            case ( $name instanceof varDto ) :
                $this->arguments[] = ArgumentDto::factory( $name )
                    ->setByReference( $type ?? false )
                    ->setUpdClassProperty( self::grabUpdClassProperty( ((array) $default), 0 ))
                    ->setNextVarPropIndex(( $byReference ?? false ));
                break;
            default :
                $this->arguments[] = ArgumentDto::factory( $name, $type, $default )
                    ->setByReference( $byReference ?? false )
                    ->setUpdClassProperty( self::grabUpdClassProperty( ((array) $updClassProp), 0 ))
                    ->setNextVarPropIndex( $intoNextVarPropArrItem ?? false );
                break;
        } // end switch
        return $this;
    }

    /**
     * @param int $aIx
     * @return ArgumentDto
     */
    public function getArgument( int $aIx ) : ArgumentDto
    {
        return $this->arguments[$aIx];
    }

    /**
     * @return int
     */
    public function getArgumentCount() : int
    {
        return count( $this->arguments );
    }

    /**
     * @return array
     */
    public function getArgumentIndex() : array
    {
        return array_keys( $this->arguments );
    }

    /**
     * Set of arguments
     *
     * Each array item can be :
     *   ArgumentDto
     *   VarDto
     *   string,
     *   array( VarDto, byReference, updClassProp, nextVarPropIndex )
     *   array( variable, varType, default, byReference, updClassProp, intoNextVarPropArrItem )
     *
     * @param null|array $arguments
     * @return static
     * @throws InvalidArgumentException
     */
    public function setArguments( array $arguments = null ) : self
    {
        static $ARGUMENT = 'argument';
        $this->arguments = [];
        if( empty( $arguments )) {
            return $this;
        }
        foreach( $arguments as $argSet ) {
            switch( true ) {
                case empty( $argSet ) :
                    throw new InvalidArgumentException( sprintf( BaseA::$ERR1, $ARGUMENT ));
                case ( $argSet instanceof ArgumentDto ) :
                    $this->addArgument( $argSet );
                    break;
                case (( $argSet instanceof VarDto ) || is_string( $argSet )) :
                    $this->addArgument( ArgumentDto::factory( $argSet ));
                    break;
                case ! is_array( $argSet ) :
                    throw new InvalidArgumentException( sprintf( BaseA::$ERRx, var_export( $argSet, true )));
                case( $argSet[0] instanceof varDto ) :
                    $this->addArgument(
                        ArgumentDto::factory( $argSet[0] )
                            ->setByReference( $argSet[1] ?? false )
                            ->setUpdClassProperty( self::grabUpdClassProperty( $argSet, 2 ))
                            ->setNextVarPropIndex( $argSet[3] ?? false )
                    );
                    break;
                default :
                    $this->addArgument(
                        ArgumentDto::factory(
                            $argSet[0],             // variable
                            ( $argSet[1] ?? null ), // varType
                            ( $argSet[2] ?? null )  // default
                        )
                            ->setByReference( $argSet[3] ?? false )
                            ->setUpdClassProperty( self::grabUpdClassProperty( $argSet, 4 ))
                            ->setNextVarPropIndex( $argSet[5] ?? false )
                    );
                    break;
            } // end switch
        } // end foreach
        return $this;
    }

    /**
     * @param array $argSet
     * @param int   $index
     * @return int
     */
    private static function grabUpdClassProperty( array $argSet, int $index ) : int
    {
        $updClassProperty = ArgumentDto::NONE;
        switch( true ) {
            case ! array_key_exists( $index, $argSet ) :
                break;
            case is_bool( $argSet[$index] ) :
                $updClassProperty = $argSet[$index] ? ArgumentDto::BEFORE : ArgumentDto::NONE;
                break;
            case empty( $argSet[$index] ) :
                break;
            default : // int
                $updClassProperty = ( 1 != intval( $argSet[$index] )) ? ArgumentDto::AFTER : ArgumentDto::BEFORE;
        } // end switch
        return $updClassProperty;
    }
}
