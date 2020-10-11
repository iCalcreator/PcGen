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
namespace Kigkonsult\PcGen\Traits;

use InvalidArgumentException;
use Kigkonsult\PcGen\ChainInvokeMgr;
use Kigkonsult\PcGen\EntityMgr;
use Kigkonsult\PcGen\FcnInvokeMgr;
use Kigkonsult\PcGen\TernaryNullCoalesceMgr;
use Kigkonsult\PcGen\Util;
use RuntimeException;

/**
 * Trait SourceTrait
 *
 * Used by AssignClauseMgr, ReturnClauseMgr
 *
 * @package Kigkonsult\PcGen\Traits
 */
trait SourceTrait
{
    use ScalarTrait;

    /**
     * @var EntityMgr
     */
    private $source = null;

    /**
     * @var TernaryNullCoalesceMgr
     */
    private $ternaryNullCoalesceExpr = null;

    /**
     * @var ChainInvokeMgr
     */
    private $fcnInvoke = null;

    /**
     * Return rendered source: fixed (scalar) value, class property, constant, variable OR function invoke
     *
     * @return array
     * @throws RuntimeException
     */
    protected function getRenderedSource()
    {
        static $ERR = 'No source set';
        switch( true ) {
            case $this->isScalarSet() :
                $code[] = $this->getScalar( false );
                break;
            case $this->isSourceSet() :
                $code[] = rtrim( $this->getSource()->toString());
                break;
            case $this->isTernaryNullCoalesceExprSet() :
                $code = $this->getTernaryNullCoalesceExpr()->toArray();
                break;
            case $this->isFcnInvokeSet() :
                $code = $this->getFcnInvoke()->toArray();
                break;
            default :
                throw new RuntimeException( $ERR );
                break;
        } // end switch
        return $code;
    }

    /**
     * @return EntityMgr
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return bool
     */
    public function isSourceSet()
    {
        return ( null !==  $this->source );
    }

    /**
     * Set (EntityMgr) source
     *
     * @param string|EntityMgr $class one of null, self, this, otherClass (fqcn), $class
     * @param mixed            $variable
     * @param int|string       $index
     * @return static
     * @throws InvalidArgumentException
     */
    public function setSource( $class = null, $variable = null, $index = null )
    {
        switch( true ) {
            case ( $class instanceof EntityMgr ) :
                $this->source = $class;
                break;
            case (( null === $class ) && ( null === $variable )) :
                $this->source = EntityMgr::init( $this );
                break;
            case ( ! empty( $class ) && is_string( $variable )) :
                $this->source = EntityMgr::init( $this )
                    ->setClass( $class )
                    ->setVariable( $variable );
                if( null !== $index ) {
                    $this->source->setIndex( $index );
                }
                break;
            case ( is_scalar( $variable ) && ! Util::isVarPrefixed( $variable )) :
                // and empty class
                $this->setScalar( $variable );
                break;
            default :
                $this->source = EntityMgr::init( $this );
                if( null !== $class ) {
                    $this->source->setClass( $class );
                }
                if( null !== $variable ) {
                    $this->source->setVariable( $variable );
                }
                if( null !== $index ) {
                    $this->source->setIndex( $index );
                }
                break;
        } // end switch
        return $this;
    }

    /**
     * Set source (EntityMgr) as class ('this' class instance) property, opt with index
     *
     * @param mixed      $property
     * @param int|string $index
     * @return static
     * @throws InvalidArgumentException
     */
    public function setThisPropertySource( $property, $index = null )
    {
        if( ! is_string( $property )) {
            throw new InvalidArgumentException(
                sprintf( self::$ERRx, var_export( $property, true ))
            );
        }
        $this->source = EntityMgr::init( $this )
            ->setClass( self::THIS_KW )
            ->setVariable( Util::unSetVarPrefix( $property ));
        if( null !== $index ) {
            $this->source->setIndex( $index );
        }
        return $this;
    }

    /**
     * Set source (EntityMgr) as plain variable, opt with index
     *
     * @param mixed      $variable
     * @param int|string $index
     * @return static
     * @throws InvalidArgumentException
     */
    public function setVariableSource( $variable, $index = null )
    {
        if( ! is_string( $variable )) {
            throw new InvalidArgumentException(
                sprintf( self::$ERRx, var_export( $variable, true ))
            );
        }
        $this->source = EntityMgr::init( $this )
            ->setVariable( Util::setVarPrefix( $variable ));
        if( null !== $index ) {
            $this->source->setIndex( $index );
        }
        return $this;
    }

    /**
     * @param bool $isConst
     * @return static
     */
    public function setSourceIsConst( $isConst = true )
    {
        if( null === $this->source ) {
            $this->setSource();
        }
        $this->getSource()->setIsConst((bool) $isConst );
        return $this;
    }

    /**
     * @return bool
     */
    public function isSourceStatic()
    {
        return $this->isSourceSet() && $this->getSource()->isStatic();
    }

    /**
     * @param bool $staticStatus
     * @return static
     */
    public function setSourceIsStatic( $staticStatus = true )
    {
        if( null === $this->source ) {
            $this->setSource();
        }
        $this->getSource()->setIsStatic((bool) $staticStatus );
        return $this;
    }

    /**
     * @return TernaryNullCoalesceMgr
     */
    public function getTernaryNullCoalesceExpr() {
        return $this->ternaryNullCoalesceExpr;
    }

    /**
     * @return bool
     */
    public function isTernaryNullCoalesceExprSet() {
        return ( null !== $this->ternaryNullCoalesceExpr );
    }

    /**
     * @param string|EntityMgr|FcnInvokeMgr|TernaryNullCoalesceMgr $expr1
     * @param string|EntityMgr|FcnInvokeMgr $expr2
     * @param string|EntityMgr|FcnInvokeMgr $expr3
     * @param bool $ternaryOperator   true : ternary expr, false : null coalesce expr
     * @param TernaryNullCoalesceMgr $ternaryNullCoalesceExpr
     * @return static
     */
    public function setTernaryNullCoalesceExpr(
        $expr1,
        $expr2 = null,
        $expr3 = null,
        $ternaryOperator = true
    ) {
        $this->ternaryNullCoalesceExpr = ( $expr1 instanceof TernaryNullCoalesceMgr )
            ? $expr1
            : TernaryNullCoalesceMgr::factory( $expr1, $expr2, $expr3 )
                ->setTernaryOperator( $ternaryOperator );
        return $this;
    }

    /**
     * @return ChainInvokeMgr
     */
    public function getFcnInvoke()
    {
        return $this->fcnInvoke;
    }

    /**
     * @return bool
     */
    public function isFcnInvokeSet()
    {
        return ( null !== $this->fcnInvoke );
    }

    /**
     * @param FcnInvokeMgr $invoke
     * @return static
     * @throws InvalidArgumentException
     */
    public function appendInvoke( FcnInvokeMgr $invoke )
    {
        if( empty( $this->fcnInvoke )) {
            $this->fcnInvoke = ChainInvokeMgr::init( $this );
        }
        $this->fcnInvoke->appendInvoke( $invoke->rig( $this));
        return $this;
    }

    /**
     * @param FcnInvokeMgr[] $fcnInvokes
     * @return static
     * @throws InvalidArgumentException
     */
    public function setFcnInvoke( array $fcnInvokes )
    {
        $this->fcnInvoke = ChainInvokeMgr::init( $this )->setInvokes( $fcnInvokes );
        return $this;
    }
}
