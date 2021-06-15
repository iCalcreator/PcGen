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
use RuntimeException;

use function array_merge;
use function in_array;
use function sprintf;
use function trim;

/**
 * Class CtrlStructMgr
 *
 * Manages control structures
 *     with
 *         condition ( operand1 comparisonOperator operand2 )
 *             operand : scalar, variable, class property or (class-)function
 *             comparisonOperator : class constants exists
 *         or one of
 *             boolean condition :  variable/property, (class-)function
 *             scalar value
 *     if/elseif/else  (else without condition)
 *     while
 *     do-while
 *     switch
 *     case     inside a switch 'body', will automatically add 'break' after
 *     default  same, but no condition
 * The logic body is set using CtrlStructMgr::setBody()
 *
 * The foreach control structure is managed by ForeachMgr class
 *
 * @package Kigkonsult\PcGen
 */
final class CtrlStructMgr extends BaseB
{
    /**
     * Expression type
     */
    const IFEXPR      = 0;
    const ELSEEXPR    = 1;
    const ELSEIFEXPR  = 2;
    const SWITCHEXPR  = 3;
    const CASEEXPR    = 4;
    const CASEDEFAULT = 5;
    const WHILEEXPR   = 6;
    const DOWHILEEXPR = 7;

    /**
     * @var int
     */
    private $exprType = self::IFEXPR;

    /**
     * The condition expression
     *
     * @var SimpleCondMgr
     */
    private $condition = null;

    /**
     * CtrlStructMgr constructor
     *
     * @param null|string $eol
     * @param null|string $indent
     * @param null|string $baseIndent
     */
    public function __construct( $eol = null, $indent = null, $baseIndent = null )
    {
        parent::__construct( $eol, $indent, $baseIndent );
        $this->condition = SimpleCondMgr::init();
    }

    /*
     * Set comparison operator, default '==' (CtrStructMgr::EQ)
     *
     * @param string $compOp
     */

    /**
     * Class factory method
     *
     * @param mixed        $operand     variable, bool|float|int|string|EntityMgr|FcnInvokeMgr
     * @param null|string  $compOp      default '=='
     * @param null|mixed   $operand2    variable, bool|float|int|string|EntityMgr|FcnInvokeMgr
     * @param null|int     $exprType    default 'if'
     * @return static
     * @throws InvalidArgumentException
     */
    public static function factory(
        $operand,
        $compOp = self::EQ,
        $operand2 = null,
        $exprType = self::IFEXPR
    ) : self
    {
        $instance = new self();
        if( null !== $operand2 ) {
            $instance->setOperand1( $operand );
            $instance->setCompOP( $compOp ?: self::EQ );
            $instance->setOperand2( $operand2 );
        }
        else {
            $instance->setSingleOp( $operand );
        }
        $instance->setExprType( $exprType ?: self::IFEXPR );
        return $instance;
    }

    /**
     * Return array, simpler (if-)condition with body
     *
     * @return array
     * @throws RuntimeException
     */
    public function toArray() : array
    {
        $this->assertCond();
        $indent1 = $this->baseIndent . $this->indent;
        $indent2 = $this->indent . $this->indent;
        if( in_array( $this->exprType, [ self::CASEEXPR, self::CASEDEFAULT ] )) {
            $indent2 .= $this->indent;
        }
        $code = array_merge(
            $this->renderStart( $indent1 ),
            $this->getBody( $indent2 ),
            $this->renderEnd( $indent1 )
        );
        return Util::nullByteCleanArray( $code );
    }

    /**
     * @return void
     * @throws RuntimeException
     */
    private function assertCond()
    {
        static $IFEXPR = [
            self::IFEXPR,
            self::ELSEIFEXPR
        ];
        static $REQEXPR = [
            self::SWITCHEXPR,
            self::CASEEXPR,
            self::WHILEEXPR,
            self::DOWHILEEXPR
        ];
        static $ERR  = 'Missing expression';
        switch( true ) {
            case ( in_array( $this->exprType, $REQEXPR ) && // 3,4,6,7
                ! $this->condition->isAnySet() ) :
                throw new RuntimeException( $ERR );
            case ( ! in_array( $this->exprType, $IFEXPR )) : // all but 0,1
                break;
            case $this->condition->isScalarSingleOpSet() :
                break;
            case ( ! $this->condition->isOperand1Set() ) :
                throw new RuntimeException( $ERR . 1 );
            case ( ! $this->condition->isOperand2Set() ) :
                throw new RuntimeException( $ERR . 2 );
        } // end switch
    }

    /**
     * @param string $indent
     * @return array
     */
    private function renderStart( string $indent ) : array
    {
        static $FMTs = [
            '%sif',
            '%selse { ',
            '%selseif',
            '%sswitch',
            '%scase ',
            '%sdefault :',
            '%swhile',
            '%sdo {'
        ];
        static $FMTX = ' {';
        static $FMTY = ' :';
        static $FMTZ = '( %s )';
        $row         = sprintf( $FMTs[$this->exprType], $indent );
        switch( $this->exprType ) {
            case self::ELSEEXPR : // 1
                break;
            case self::CASEEXPR : // 4
                $row .= $this->condition->isExpression()
                    ? sprintf( $FMTZ, trim( $this->condition->toString()))
                    : trim( $this->condition->toString());
                $row .= $FMTY;
                break;
            case self::CASEDEFAULT : // 5
                break;
            case self::DOWHILEEXPR : // 7
                break;
            default :
                if( $this->condition->isScalarSingleOpSet()) {
                    $row .= sprintf( $FMTZ, trim( $this->condition->toString() ) ) . $FMTX;
                    break;
                }
                $row .= trim( $this->condition->toString()) . $FMTX;
                break;
        } // end switch
        return [ $row ];
    }

    /**
     * @param string $indent
     * @return array
     */
    private function renderEnd( string $indent ) : array
    {
        static $END0  = '} // end if';
        static $END1  = '} // end else';
        static $END2  = '} // end elseif';
        static $END3  = '} // end switch';
        static $END45 = 'break;';
        static $END6  = '} // end while';
        static $END7a = '%s} while %s;';
        static $END7b = '%s} while( %s );';
        switch( $this->exprType ) {
            case self::ELSEEXPR : // 1
                $blockEnd = $END1;
                break;
            case self::SWITCHEXPR : // 3
                $blockEnd = $END3;
                break;
            case self::CASEEXPR : // 4
                // fall through
            case self::CASEDEFAULT : // 5
                $blockEnd = $this->getIndent() . $END45;
                break;
            case self::WHILEEXPR : // 6
                $blockEnd = $END6;
                break;
            case self::DOWHILEEXPR : // 7
                $blockEnd = self::SP0;
                break;
            default : // if- or elseif-expression : 0 - 2
                if( $this->condition->isSingleOpSet()) {
                    $blockEnd = $END2;
                    break;
                }
                $blockEnd = ( self::IFEXPR == $this->exprType ) ? $END0 : $END2;
                break;
        } // end switch
        $rows = [];
        if( self::DOWHILEEXPR == $this->exprType ) { // 7
            $fmt     = $this->condition->isSingleOpSet() ? $END7b : $END7a;
            $rows[0] = sprintf( $fmt, $indent, trim( $this->condition->toString()));
        }
        else {
            $rows[] = $indent . $blockEnd;
        }
        return $rows;
    }

    /**
     * Set exprType
     *
     * @param int $exprType
     * @throws InvalidArgumentException
     */
    public function setExprType( int $exprType )
    {
        static $TYPES = [
            self::IFEXPR,
            self::ELSEEXPR,
            self::ELSEIFEXPR,
            self::SWITCHEXPR,
            self::CASEEXPR,
            self::CASEDEFAULT,
            self::WHILEEXPR,
            self::DOWHILEEXPR,
        ];
        if( ! in_array( $exprType, $TYPES )) {
            throw new InvalidArgumentException( sprintf( self::$ERRx, $exprType ));
        }
        $this->exprType = $exprType;
    }

    /**
     * Set if-expression type
     *
     * @return static
     */
    public function setIfExprType() : self
    {
        $this->exprType = self::IFEXPR;
        return $this;
    }

    /**
     * Set else-expression type
     *
     * @return static
     */
    public function setElseExprType() : self
    {
        $this->exprType = self::ELSEEXPR;
        return $this;
    }

    /**
     * Set elseif-expression type
     *
     * @return static
     */
    public function setElseIfExprType() : self
    {
        $this->exprType = self::ELSEIFEXPR;
        return $this;
    }

    /**
     * Set switch-expression type
     *
     * @return static
     */
    public function setSwitchExprType() : self
    {
        $this->exprType = self::SWITCHEXPR;
        return $this;
    }

    /**
     * Set switch case-expression type
     *
     * @return static
     */
    public function setCaseExprType() : self
    {
        $this->exprType = self::CASEEXPR;
        return $this;
    }

    /**
     * Set switch default-expression type
     *
     * @return static
     */
    public function setDefaultExprType() : self
    {
        $this->exprType = self::CASEDEFAULT;
        return $this;
    }

    /**
     * Set while-expression type
     *
     * @return static
     */
    public function setWhileExprType() : self
    {
        $this->exprType = self::WHILEEXPR;
        return $this;
    }

    /**
     * Set doWhile-expression type
     *
     * @return static
     */
    public function setDoWhileExprType() : self
    {
        $this->exprType = self::DOWHILEEXPR;
        return $this;
    }

    /**
     * @return SimpleCondMgr
     */
    public function getCondition() : SimpleCondMgr
    {
        return $this->condition;
    }

    /**
     * @return bool
     */
    public function isConditionSet() : bool
    {
        return ( null !== $this->condition );
    }

    /**
     * @param SimpleCondMgr $condition
     * @return static
     */
    public function setCondition( SimpleCondMgr $condition ) : self
    {
        $this->condition = $condition;
        return $this;
    }

    /**
     * Set single cond. (boolean) scalar
     *
     * @param bool|float|int|string $scalar
     * @return static
     * @throws InvalidArgumentException
     */
    public function setScalar( $scalar ) : self
    {
        $this->condition->setScalar( $scalar );
        return $this;
    }

    /**
     * Set single cond. (boolean) PHP expression
     *
     * @param string $expression
     * @return static
     * @throws InvalidArgumentException
     */
    public function setExpression( string $expression ) : self
    {
        $this->condition->setExpression( $expression );
        return $this;
    }

    /**
     * Set cond. (boolean) as single variable (string), classVariable (EntityMgr) or function invoke (FcnInvokeMgr)
     *
     * @param string|EntityMgr|FcnInvokeMgr $singleOp
     * @return static
     * @throws InvalidArgumentException
     */
    public function setSingleOp( $singleOp ) : self
    {
        $this->condition->setSingleOp( $singleOp );
        return $this;
    }

    /**
     * Set single operand as this class property
     *
     * Convenient CtrlStructMgr::setSingleOp() alias
     *
     * @param string $boolVar
     * @return static
     * @throws InvalidArgumentException
     */
    public function setThisPropSingleOp( string $boolVar ) : self
    {
        $this->condition->setThisPropSingleOp( $boolVar );
        return $this;
    }

    /**
     * Set single operand as this class function call
     *
     * Convenient CtrlStructMgr::setSingleOp() alias
     *
     * @param string $thisFcn
     * @return static
     * @throws InvalidArgumentException
     */
    public function setThisFcnSingleOP( string $thisFcn ) : self
    {
        $this->condition->setThisFcnSingleOP( $thisFcn );
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
        return $this->condition->getCompOP( $strict );
    }

    /**
     * Set comparison operator, default '==' (CtrStructMgr::EQ)
     *
     * @param string $compOp
     * @return static
     * @throws InvalidArgumentException
     */
    public function setCompOP( string $compOp ) : self
    {
        $this->condition->setCompOP( $compOp );
        return $this;
    }

    /**
     * @param bool|float|int|string|EntityMgr|FcnInvokeMgr $operand1
     * @return static
     * @throws InvalidArgumentException
     */
    public function setOperand1( $operand1 ) : self
    {
        $this->condition->setOperand1( $operand1 );
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
        $this->condition->setThisVarOperand1( $operand1 );
        return $this;
    }

    /**
     * @param bool|float|int|string|EntityMgr|FcnInvokeMgr $operand2
     * @return static
     * @throws InvalidArgumentException
     */
    public function setOperand2( $operand2 ) : self
    {
        $this->condition->setOperand2( $operand2 );
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
        $this->condition->setThisVarOperand2( $operand2 );
        return $this;
    }
}
