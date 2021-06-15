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
use Kigkonsult\PcGen\ChainInvokeMgr;
use Kigkonsult\PcGen\EntityMgr;
use Kigkonsult\PcGen\FcnInvokeMgr;
use Kigkonsult\PcGen\TernaryNullCoalesceMgr;
use Kigkonsult\PcGen\Util;
use RuntimeException;

use function is_scalar;
use function is_string;
use function sprintf;
use function var_export;

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
    protected function getRenderedSource() : array
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
        } // end switch
        return $code;
    }

    /**
     * @return null|EntityMgr
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return bool
     */
    public function isSourceSet() : bool
    {
        return ( null !==  $this->source );
    }

    /**
     * Set (EntityMgr) source
     *
     * @param null|string|EntityMgr $class one of null, self, this, otherClass (fqcn), $class
     * @param null|mixed            $variable
     * @param null|int|string       $index
     * @return static
     * @throws InvalidArgumentException
     */
    public function setSource( $class = null, $variable = null, $index = null ) : self
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
    public function setThisPropertySource( $property, $index = null ) : self
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
    public function setVariableSource( $variable, $index = null ) : self
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
    public function setSourceIsConst( $isConst = true ) : self
    {
        if( null === $this->source ) {
            $this->setSource();
        }
        $this->getSource()->setIsConst( $isConst ?? true );
        return $this;
    }

    /**
     * @return bool
     */
    public function isSourceStatic() : bool
    {
        return $this->isSourceSet() && $this->getSource()->isStatic();
    }

    /**
     * @param bool $staticStatus
     * @return static
     */
    public function setSourceIsStatic( $staticStatus = true ) : self
    {
        if( null === $this->source ) {
            $this->setSource();
        }
        $this->getSource()->setIsStatic( $staticStatus ?? true );
        return $this;
    }

    /**
     * @return null|TernaryNullCoalesceMgr
     */
    public function getTernaryNullCoalesceExpr()
    {
        return $this->ternaryNullCoalesceExpr;
    }

    /**
     * @return bool
     */
    public function isTernaryNullCoalesceExprSet() : bool
    {
        return ( null !== $this->ternaryNullCoalesceExpr );
    }

    /**
     * @param string|EntityMgr|FcnInvokeMgr|TernaryNullCoalesceMgr $expr1
     * @param null|string|EntityMgr|FcnInvokeMgr $expr2
     * @param null|string|EntityMgr|FcnInvokeMgr $expr3
     * @param null|bool $ternaryOperator   true : ternary expr, false : null coalesce expr
     * @return static
     */
    public function setTernaryNullCoalesceExpr(
        $expr1,
        $expr2 = null,
        $expr3 = null,
        $ternaryOperator = true
    ) : self
    {
        $this->ternaryNullCoalesceExpr = ( $expr1 instanceof TernaryNullCoalesceMgr )
            ? $expr1
            : TernaryNullCoalesceMgr::factory( $expr1, $expr2, $expr3 )
                ->setTernaryOperator( $ternaryOperator ?? true );
        return $this;
    }

    /**
     * @return null|ChainInvokeMgr
     */
    public function getFcnInvoke()
    {
        return $this->fcnInvoke;
    }

    /**
     * @return bool
     */
    public function isFcnInvokeSet() : bool
    {
        return ( null !== $this->fcnInvoke );
    }

    /**
     * @param FcnInvokeMgr $invoke
     * @return static
     * @throws InvalidArgumentException
     */
    public function appendInvoke( FcnInvokeMgr $invoke ) : self
    {
        if( empty( $this->fcnInvoke )) {
            $this->fcnInvoke = ChainInvokeMgr::init( $this );
        }
        $this->fcnInvoke->appendInvoke( $invoke->rig( $this ));
        return $this;
    }

    /**
     * @param FcnInvokeMgr[] $fcnInvokes
     * @return static
     * @throws InvalidArgumentException
     */
    public function setFcnInvoke( array $fcnInvokes ) : self
    {
        $this->fcnInvoke = ChainInvokeMgr::init( $this )->setInvokes( $fcnInvokes );
        return $this;
    }
}
