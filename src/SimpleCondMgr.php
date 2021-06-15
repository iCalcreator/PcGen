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
use Kigkonsult\PcGen\Traits\ScalarTrait;
use RuntimeException;

use function in_array;
use function is_object;
use function is_scalar;
use function is_string;
use function get_class;
use function rtrim;
use function sprintf;
use function trim;
use function var_export;

/**
 * Class SimpleCondMgr
 *
 * Manages simpler condition,
 *     condition ( operand1 comparisonOperator operand2 )
 *         operand : scalar, variable, class property or (class-)function, NO PHP expression
 *         comparisonOperator : use class constants
 *     boolean condition :  variable/property, (class-)function
 *     PHP expression
 *
 * @package Kigkonsult\PcGen
 */
class SimpleCondMgr extends BaseA
{
    use ScalarTrait;

    /**
     * A single (bool) operand, variable/property or function/method
     *
     * @var EntityMgr|FcnInvokeMgr
     */
    private $singleOp = null;

    /**
     * @var string[]
     */
    private static $CONDOPARR = [
        self::EQ,
        self::NEQ,
        self::EQ3,
        self::NEQ3,
        self::GT,
        self::GTEQ,
        self::LT,
        self::LTEQ,
    ];

    /**
     * All comparison operators
     *
     * @return string[]
     */
    public static function getCondOPs() : array
    {
        return self::$CONDOPARR;
    }

    /**
     * Comparison operator
     *
     * $var string  default '=='
     */
    private $compOp = self::EQ;

    /**
     * Condition operand 1
     *
     * @var bool|float|int|string|EntityMgr|FcnInvokeMgr
     */
    private $operand1 = null;

    /**
     * Condition operand 2
     *
     * @var bool|float|int|string|EntityMgr|FcnInvokeMgr
     */
    private $operand2 = null;

    /**
     * Return array, simpler condition
     *
     * Operand1/operand2 condition enclosed in parenthesis, boolean not
     *
     * @return array
     * @throws RuntimeException
     */
    public function toArray() : array
    {
        static $FMT1 = '( ';
        static $FMT2 = ' )';
        if(  $this->isScalarSet()) {
            return [ $this->getScalar( false ) ];
        }
        if(  $this->isSingleOpSet()) {
            return [ trim( $this->singleOp->toString()) ];
        }
        $row  = $FMT1;
        $row .= self::renderOperand( $this->operand1 );
        $row .= $this->getCompOP();
        $row .= self::renderOperand( $this->operand2 );
        $row .= $FMT2;
        return [ $row ];
    }

    /**
     * @param mixed $operand  bool|float|int|EntityMgr|string
     * @return string
     */
    private static function renderOperand( $operand ) : string
    {
        return is_scalar( $operand )
            ? Util::renderScalarValue( $operand )
            : rtrim( $operand->toString());
    }

    /**
     * Return bool true if scalar, singleOp OR operand1/2 is set
     *
     * @return bool
     */
    public function isAnySet() : bool
    {
        return ( $this->isScalarSingleOpSet() ||
            ( $this->isOperand1Set() && $this->isOperand2Set())
        );
    }

    /**
     * Return bool true if scalar OR singleOp is set
     *
     * @return bool
     */
    public function isScalarSingleOpSet() : bool
    {
        return ( $this->isScalarSet() || $this->isSingleOpSet());
    }

    /**
     * @return EntityMgr|FcnInvokeMgr
     */
    public function getSingleOp()
    {
        return $this->singleOp;
    }

    /**
     * @return bool
     */
    public function isSingleOpSet() : bool
    {
        return ( null !== $this->singleOp );
    }

    /**
     * Set single operand as single variable (string), classVariable (EntityMgr) or function invoke (FcnInvokeMgr)
     *
     * @param string|EntityMgr|FcnInvokeMgr $singleOp
     * @return static
     * @throws InvalidArgumentException
     */
    public function setSingleOp( $singleOp ) : self
    {
        switch( true ) {
            case is_string( $singleOp ) :
                $singleOp = EntityMgr::factory( null, $singleOp );
                break;
            case ( $singleOp instanceof EntityMgr ) :
                break;
            case ( $singleOp instanceof FcnInvokeMgr ) :
                break;
            default :
                throw new InvalidArgumentException(
                    sprintf(
                        self::$ERRx,
                        (string) ( is_object( $singleOp )
                            ? get_class( $singleOp )
                            : $singleOp )
                    )
                );
        } //end switch
        $this->singleOp = $singleOp;
        return $this;
    }

    /**
     * Set single operand as this class property
     *
     * Convenient CtrlStructMgr::setSingleOp() alias
     *
     * @param string $singleOp
     * @return static
     * @throws InvalidArgumentException
     */
    public function setThisPropSingleOp( string $singleOp ) : self
    {
        $this->setSingleOp(
            EntityMgr::init( $this )
                ->setClass( self::THIS_KW )
                ->setVariable( Util::unSetVarPrefix( $singleOp ))
        );
        return $this;
    }

    /**
     * Set single operand as this class function call
     *
     * Convenient CtrlStructMgr::setSingleOp() alias
     *
     * @param string $singleOP
     * @return static
     * @throws InvalidArgumentException
     */
    public function setThisFcnSingleOP( string $singleOP ) : self
    {
        $this->setSingleOp(
            FcnInvokeMgr::init( $this )
                ->setName( self::THIS_KW, $singleOP )
        );
        return $this;
    }

    /**
     * Return comparison operator, 'as is' or rendered
     *
     * @param bool $strict
     * @return string
     */
    public function getCompOP( $strict = false ) : string
    {
        static $OPERATORfmt = ' %s ';
        return ( $strict ?? false ) ? $this->compOp : sprintf( $OPERATORfmt, $this->compOp );
    }

    /**
     * @param string $compOp
     * @return static
     * @throws InvalidArgumentException
     */
    public function setCompOP( string $compOp ) : self
    {
        $compOp = trim( $compOp );
        if( ! in_array( $compOp, self::$CONDOPARR )) {
            throw new InvalidArgumentException(
                sprintf( self::$ERRx, var_export( $compOp, true ))
            );
        }
        $this->compOp = $compOp;
        return $this;
    }

    /**
     * @param mixed $operand
     * @throws InvalidArgumentException
     */
    private static function assertOperand( $operand )
    {
        if( is_scalar( $operand ) ||
            ( $operand instanceof EntityMgr ) ||
            ( $operand instanceof FcnInvokeMgr )) {
            return;
        }
        throw new InvalidArgumentException(
            sprintf(
                self::$ERRx,
                (string) ( is_object( $operand ) ? get_class( $operand ) : $operand )
            )
        );
    }

    /**
     * @return bool|float|int|string|EntityMgr|FcnInvokeMgr
     */
    public function getOperand1()
    {
        return $this->operand1;
    }

    /**
     * @return bool
     */
    public function isOperand1Set() : bool
    {
        return ( null !== $this->operand1 );
    }

    /**
     * @param bool|float|int|string|EntityMgr|FcnInvokeMgr $operand1
     * @return static
     * @throws InvalidArgumentException
     */
    public function setOperand1( $operand1 ) : self
    {
        self::assertOperand( $operand1 );
        $this->operand1 = $operand1;
        return $this;
    }

    /**
     * Set operand1 as a 'this' variable
     *
     * Convenient CtrlStructMgr::setOperand1() alias
     *
     * @param string $operand1
     * @return static
     * @throws InvalidArgumentException
     */
    public function setThisVarOperand1( string $operand1 ) : self
    {
        return $this->setOperand1(
            EntityMgr::init( $this )
                ->setClass( self::THIS_KW )
                ->setVariable( Util::unSetVarPrefix( $operand1 ))
        );
    }

    /**
     * @return bool|float|int|string|EntityMgr|FcnInvokeMgr
     */
    public function getOperand2()
    {
        return $this->operand2;
    }

    /**
     * @return bool
     */
    public function isOperand2Set() : bool
    {
        return ( null !== $this->operand2 );
    }

    /**
     * @param bool|float|int|string|EntityMgr|FcnInvokeMgr $operand2
     * @return static
     * @throws InvalidArgumentException
     */
    public function setOperand2( $operand2 ) : self
    {
        self::assertOperand( $operand2 );
        $this->operand2 = $operand2;
        return $this;
    }

    /**
     * Set operand2 as a 'this' variable
     *
     * Convenient CtrlStructMgr::setOperand2() alias
     *
     * @param string $operand2
     * @return static
     * @throws InvalidArgumentException
     */
    public function setThisVarOperand2( string $operand2 ) : self
    {
        return $this->setOperand2(
            EntityMgr::init( $this )
                ->setClass( self::THIS_KW )
                ->setVariable( Util::unSetVarPrefix( $operand2 ))
        );
    }
}
