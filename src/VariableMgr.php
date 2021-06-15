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
use Kigkonsult\PcGen\Dto\VarDto;
use Kigkonsult\PcGen\Traits\OperatorTrait;
use RuntimeException;

use function array_keys;
use function array_reverse;
use function call_user_func_array;
use function implode;
use function is_bool;
use function ltrim;
use function sprintf;
use function strtoupper;
use function var_export;

/**
 * Class VariableMgr
 *
 * Manages variables and init/define of value, 1. body (i.e. closure), 2. callback, 3. PHP primitive value (incl array)
 *
 * @package Kigkonsult\PcGen
 */
class VariableMgr extends BaseC
{
    private static $ARREND   = ']';
    private static $ARRSTART = '[';
    private static $QUOTE    = '\'%s\'';

    /**
     * Variable/property/constant base data, variable and default (initValue)
     *
     * @var VarDto
     */
    protected $varDto = null;

    /**
     * Callable value varType
     *
     * @var string|string[]
     */
    protected $callback = null;

    /**
     * If to generate a constant
     *
     * @var bool
     */
    protected $isConst = false;

    use OperatorTrait;

    /**
     * VariableMgr constructor
     *
     * @param null|string $eol
     * @param null|string $indent
     * @param null|string $baseIndent
     */
    public function __construct( $eol = null, $indent = null, $baseIndent = null )
    {
        parent::__construct( $eol, $indent, $baseIndent );
        $this->varDto = new varDto();
    }

    /**
     * @param mixed $args (args for VarDto )
     * @return static
     * @throws InvalidArgumentException
     */
    public static function factory( ...$args ) : self
    {
        $instance = new static();
        switch ( true ) {
            case ( isset( $args[0] ) && ( $args[0] instanceof VarDto )) :
                $instance->setVarDto( $args[0] );
                break;
            case ! empty( $args ) :
                if( false ===
                    ( $varDto = call_user_func_array( [ VarDto::class, self::FACTORY ], $args ))
                ) {
                    throw new InvalidArgumentException(
                        sprintf( self::$ERRx, var_export( $args, true ))
                    );
                }
                $instance->setVarDto( $varDto );
                break;
            default :
                throw new InvalidArgumentException(
                    sprintf( self::$ERRx, var_export( $args, true ))
                );
        } // end switch
        return $instance;
    }

    /**
     * @return string
     * @todo stringify
     */
    public function __toString() : string
    {
        $string  = empty( $this->varDto ) ? self::SP0 : $this->varDto;
        $string .= ', visibility : \'' . $this->getVisibility() . '\'';
        $string .= ', isStatic : ' . var_export( $this->isStatic(), true );
        $string .= ', isConst : ' . var_export( $this->isConst(), true );
        switch( true ) {
            case $this->isBodySet() :  // closure or expression
                $string .= PHP_EOL . 'closure : ' . var_export( $this->renderClosureBody( self::SP0 ), true );
                break;
            case $this->isCallBackSet() : // callable
                $string .= ', callback' . implode( self::SP0, $this->renderCallBlack( self::SP0 ));
                break;
            default :
                $string .= ', initValue : ' . implode( self::SP0, $this->renderInitValue( self::SP0 ));
                break;
        } // end switch
        return $string;
    }

    /**
     * Return code as array (with NO eol at line endings)
     *
     * Value varType order check : 1. body (i.e. closure), 2. callback, 3. PHP primitive value (incl array)
     *
     * @return array
     * @throws RuntimeException
     */
    public function toArray() : array
    {
        static $CONSTTMPL = '%s %s';
        if( ! $this->varDto->isNameSet()) {
            throw new RuntimeException( self::$ERR1 );
        }
        $row = $this->baseIndent;
        if( $this->isVisibilitySet() &&
            ( ! $this->isConst() ||
                (( 7 <= PHP_MAJOR_VERSION ) && ( 1 <= PHP_MINOR_VERSION )))
        ) {
            $row .= $this->visibility . self::SP1;
        }

        if( $this->isConst()) {
            $row .= sprintf( $CONSTTMPL, self::CONST_, strtoupper( $this->varDto->getName()));
        }
        else {
            if( $this->isStatic()) {
                $row .= self::$STATIC . self::SP1;
            }
            $row .= Util::setVarPrefix( $this->varDto->getName());
        }

        $row .= $this->getOperator( false );
        switch( true ) {
            case $this->isBodySet() :  // closure or expression
                $code = $this->renderClosureBody( $row );
                break;
            case $this->isCallBackSet() : // callable
                $code = $this->renderCallBlack( $row );
                break;
            default :
                $code = $this->renderInitValue( $row );
                break;
        } // end switch
        return Util::nullByteCleanArray( $code );
    }

    /**
     * Return array; logic as closure or expression
     *
     * @param string $row
     * @return array
     */
    private function renderClosureBody( string $row ) : array
    {
        $body          = $this->getBody();
        $body[0]       = $row . ltrim( $body[0] );
        $lastIx        = array_reverse( array_keys( $body ))[0];
        $body[$lastIx] = $this->baseIndent . ltrim( $body[$lastIx] );
        return $body;
    }

    /**
     * Return array, callback (callable)
     *
     * @param string $row
     * @return array
     */
    private function renderCallBlack( string $row ) : array
    {
        if( is_string( $this->callback )) {
            return [ $row . self::renderCallBlackClass( $this->callback ) . self::$CLOSECLAUSE ];
        }
        // array
        $code[]  = $row . self::$ARRSTART;
        $code[] = $this->baseIndent . $this->indent .
            self::renderCallBlackClass( $this->callback[0] ) . self::$COMMA;
        $code[] = $this->baseIndent . $this->indent .
            sprintf( self::$QUOTE, $this->callback[1] );
        $code[] = $this->baseIndent . self::$ARREND . self::$CLOSECLAUSE;
        return $code;
    }

    /**
     * @param string $class
     * @return string
     */
    private static function renderCallBlackClass( string $class ) : string
    {
        return Util::isVarPrefixed( $class ) ? $class : sprintf( self::$QUOTE, $class );
    }

    /**
     * Return rendered fixed value. Assoc array with key, non-assoc without
     *
     * @param string $row
     * @return array
     */
    private function renderInitValue( string $row ) : array
    {
        static $KEYFMT = '"%s" => ';
        $initValue = $this->varDto->getDefault();
        $expType   = $this->varDto->getVarType();
        $code      = [];
        switch( true ) {
            case is_bool( $initValue ) ||
                Util::isFloat( $initValue ) ||
                Util::isInt( $initValue ) :
                $initValue = Util::renderScalarValue( $initValue, $expType );
                break;
            case $this->varDto->isDefaultTypedNull() :
                $initValue = self::NULL_T;
                break;
            case ( $this->varDto->isDefaultTypedArray() &&
                ( ! $this->varDto->isDefaultArray() || empty( $initValue ))) :
                $initValue = self::ARRAY2_T;
                break;
            case $this->varDto->isDefaultArray() :
                $code[]  = $row . self::$ARRSTART;
                $indent  = $this->baseIndent . $this->indent;
                $expType = $this->varDto->hasTypeHintArraySpec( null, $typeHint )
                    ? $typeHint
                    : null;
                foreach( $initValue as $key => $value ) {
                    $row = $indent;
                    if( ! Util::isInt( $key )) {
                        $row .= sprintf( $KEYFMT, $key );
                    }
                    $code[] =
                        $row . Util::renderScalarValue( $value, $expType ) . self::$COMMA;
                } // end foreach
                $code[] = $this->baseIndent . self::$ARREND . self::$CLOSECLAUSE;
                return $code;
            case is_string( $initValue ) :
                $initValue = Util::renderScalarValue( $initValue, $expType );
                break;
            default :
                $initValue = self::NULL_T;
                break;
        } // end switch
        $row .= $initValue . self::$CLOSECLAUSE;
        $code[] = $row;
        return $code;
    }

    /**
     * @return null|VarDto
     */
    public function getVarDto()
    {
        return $this->varDto;
    }

    /**
     * @param VarDto $varDto
     * @return static
     */
    public function setVarDto( VarDto $varDto ) : self
    {
        $this->varDto = $varDto;
        return $this;
    }

    /**
     * Get varable name, override parent
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->varDto->getName();
    }

    /**
     * Set varable name, override parent
     *
     * @param string $name
     * @return static
     * @throws InvalidArgumentException
     */
    public function setName( string $name ) : self
    {
        $this->varDto->setName( $name );
        return $this;
    }

    /**
     * @param mixed $initValue
     * @return static
     */
    public function setInitValue( $initValue = null ) : self
    {
        switch( true ) {
            case (( null === $initValue ) ||
                ( is_string( $initValue ) &&
                    ( 0 === strcasecmp(  self::NULL_T, $initValue )))) :
                $initValue = self::NULL_T;
                break;
            case ( is_string( $initValue ) &&
                in_array( $initValue, VarDto::$ARRAYs, true )) :
                $initValue = self::ARRAY2_T;
                break;
            default :
                break;
        } // end switch
        $this->varDto->setDefault( $initValue );
        return $this;
    }

    /**
     * @return bool
     */
    public function isConst() : bool
    {
        return $this->isConst;
    }

    /**
     * @param bool $isConst
     * @return static
     */
    public function setIsConst( $isConst = true ) : self
    {
        $this->isConst = $isConst ?? true;
        if( $this->isConst ) {
            $this->setStatic( false );
        }
        return $this;
    }

    /**
     * @return null|string|string[]
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @return bool
     */
    public function isCallBackSet() : bool
    {
        return ( ! empty( $this->callback ));
    }

    /**
     * Set callable value varType
     *
     * A (callable) handler can be
     *   simple function     (set using 'setBody')
     *   anonymous function  (set using 'setBody' and, opt, FcnFrameMgr)
     *   instantiated sourceObject+method, passed as an array             : [$sourceObject, methodName]
     *   class variable and static (factory?) method, passed as an array  : [FQCN, methodName]
     *   instantiated sourceObject, class has an (magic) __call method    : $sourceObject
     *   class variable, class has an (magic) __callStatic method         : FQCN
     *   instantiated sourceObject, class has an (magic) __invoke method  : $sourceObject
     *
     * @param string $class         accepts both '$obj' and 'className', ie opt $-class, no check
     * @param null|string $method
     * @return static
     * @throws InvalidArgumentException
     */
    public function setCallback( string $class, $method = null ) : self
    {
        if( Util::isVarPrefixed( $class ) ) {
            Assert::assertPhpVar( $class );
        }
        else {
            Assert::assertFqcn( $class );
        }
        if( null === $method ) {
            $this->callback = $class;
        }
        else {
            Assert::assertPhpVar( $method );
            $this->callback = [ $class, $method ];
        }
        if( ! is_callable( $this->callback, true )) {
            throw new InvalidArgumentException(
                sprintf( self::$ERRx, var_export( $this->callback, true ))
            );
        }
        return $this;
    }

    /**
     * Set static, override parent
     *
     * @param bool $static
     * @return static
     */
    public function setStatic( $static = true ) : BaseC
    {
        $this->static = $static ?? true;
        if( $this->static ) {
            $this->setIsConst( false );
        }
        return $this;
    }
}
