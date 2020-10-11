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
use RuntimeException;

/**
 * Class TernaryNullCoalesceMgr
 *
 * Manages ternary (default) and null coalescing operator expressions
 *
 * The ternary operator expression 'expr1 ? expr2 : expr3'
 *   evaluates to expr2 if
 *     expr1 evaluates to TRUE
 *     and expr3 if expr1 evaluates to FALSE.
 * The ternary operator expression 'expr1 ?: expr3'
 *   returns expr1 if
 *     expr1 evaluates to TRUE
 *     and expr3 otherwise.
 *
 * The (PHP7+) null coalescing operator expression 'expr1 ?? expr2'
 *   evaluates to expr2 if expr1 is NULL, and expr1 otherwise.
 * In particular, this operator does not emit a notice
 * if the left-hand side value does not exist, just like isset().
 * This is especially useful on array keys.
 *
 * Expression defind as one of
 *     simple expression i.e. constant, variable or class property (array)
 *     method/function invoke, opt with arguments,
 *       no support for dynamic methodNames, $this->{$method}
 *
 * The result expression is enclosed in parenthesis.
 *
 * @package Kigkonsult\PcGen
 */
final class TernaryNullCoalesceMgr extends BaseA
{
    /**
     * True ternary, false null coalescing operator
     *
     * @var bool
     */
    private $ternaryOperator = true;

    /**
     * @var EntityMgr|FcnInvokeMgr
     */
    private $expr1 = null;

    /**
     * @var EntityMgr|FcnInvokeMgr
     */
    private $expr2 = null;

    /**
     * @var EntityMgr|FcnInvokeMgr
     */
    private $expr3 = null;

    /**
     * @param string|EntityMgr|FcnInvokeMgr $expr1    used in ternary and null coalescing operators
     * @param string|EntityMgr|FcnInvokeMgr $expr2    used in ternary and null coalescing operators
     * @param string|EntityMgr|FcnInvokeMgr $expr3    used in ternary operator
     * @return static
     * @throws InvalidArgumentException
     */
    public static function factory( $expr1, $expr2, $expr3 = null )
    {
        $instance = new self();
        $instance->setExpr1( $expr1 );
        if( ! empty( $expr2 )) {
            $instance->setExpr2( $expr2 );
        }
        if( ! empty( $expr3 )) {
            $instance->setExpr3( $expr3 );
        }
        return $instance;
    }

    /**
     * @inheritDoc
     * @throws RuntimeException
     */
    public function toArray()
    {
        static $ERR1 = 'No expression %d set';
        static $ERR3 = '%d expressions set';
        static $TO1  = ' ?';
        static $TO2  = ': ';
        static $SP0  = '';
        static $SP1  = ' ';
        static $NCO  = ' ??';
        static $OUT  = '( %s )';
        if( ! $this->isExpr1Set()) {
            throw new RuntimeException( sprintf( $ERR1, 1 ));
        }
        if( ! $this->isTernaryOperator() && ! $this->isExpr2Set()) {
            throw new RuntimeException( sprintf( $ERR1, 2 ));
        }
        if( $this->isTernaryOperator() && ! $this->isExpr3Set()) {
            throw new RuntimeException( sprintf( $ERR1, 3 ));
        }
        if( ! $this->isTernaryOperator() && $this->isExpr3Set()) {
            throw new RuntimeException( sprintf( $ERR3, 3 ));
        }
        $row  = trim( $this->getExpr1()->toString());
        $row .= $this->isTernaryOperator() ? $TO1 : $NCO;
        $sp23 = $SP0;
        if( $this->isExpr2Set()) {
            $row .= $SP1 . trim( $this->getExpr2()->toString());
            $sp23 = $SP1;
        }
        if( $this->isTernaryOperator()) {
            $row .= $sp23 . $TO2 . trim( $this->getExpr3()->toString());
        }
        return [ sprintf( $OUT, Util::nullByteClean( $row )) ];
    }

    /**
     * @return bool
     */
    public function isTernaryOperator()
    {
        return $this->ternaryOperator;
    }

    /**
     * @param bool $ternaryOperator
     * @return static
     */
    public function setTernaryOperator( $ternaryOperator = true )
    {
        $this->ternaryOperator = (bool) $ternaryOperator;
        return $this;
    }

    /**
     * @param string|EntityMgr|FcnInvokeMgr $expr
     * @return EntityMgr|FcnInvokeMgr
     */
    private static function evalExpr( $expr )
    {
        switch( true ) {
            case ( $expr instanceof EntityMgr ) :
                break;
            case ( $expr instanceof FcnInvokeMgr ) :
                break;
            default :
                $expr = EntityMgr::factory( null, $expr );
                break;
        } // end switch
        return $expr;
    }

    /**
     * @return EntityMgr|FcnInvokeMgr
     */
    public function getExpr1()
    {
        return $this->expr1;
    }

    /**
     * @return bool
     */
    public function isExpr1Set()
    {
        return ( null !== $this->expr1 );
    }

    /**
     * @param string|EntityMgr|FcnInvokeMgr $expr1
     * @return static
     */
    public function setExpr1( $expr1 )
    {
        $this->expr1 = self::evalExpr( $expr1 );
        return $this;
    }

    /**
     * @return EntityMgr|FcnInvokeMgr
     */
    public function getExpr2()
    {
        return $this->expr2;
    }

    /**
     * @return bool
     */
    public function isExpr2Set()
    {
        return ( null !== $this->expr2 );
    }

    /**
     * @param EntityMgr|FcnInvokeMgr $expr2
     * @return static
     */
    public function setExpr2( $expr2 )
    {
        $this->expr2 = self::evalExpr( $expr2 );
        return $this;
    }

    /**
     * @return EntityMgr|FcnInvokeMgr
     */
    public function getExpr3()
    {
        return $this->expr3;
    }

    /**
     * @return bool
     */
    public function isExpr3Set()
    {
        return ( null !== $this->expr3 );
    }

    /**
     * @param string|EntityMgr|FcnInvokeMgr $expr3
     * @return static
     */
    public function setExpr3( $expr3 )
    {
        $this->expr3 = self::evalExpr( $expr3 );
        return $this;
    }
}
