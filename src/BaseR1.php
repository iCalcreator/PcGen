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

abstract class BaseR1 extends BaseA
{
    /**
     * @var string
     */
    protected static $END = ';';

    /**
     * Scalar value
     *
     * @var bool|int|float|string
     */
    protected $fixedSourceValue = null;

    /**
     * @var bool   true if fixedSourceValue is a PHP expression
     */
    protected $isExpression = false;

    /**
     * @var EntityMgr
     */
    protected $source = null;

    /**
     * @var ChainInvokeMgr
     */
    protected $fcnInvoke = null;

    /**
     * @return array
     * @throws RuntimeException
     */
    protected function getRenderedSource()
    {
        static $ERR = 'No source set';
        $code = [];
        switch( true ) {
            case $this->isFixedSourceValueSet() :
                $code[] = $this->getFixedSourceValue( false );
                break;
            case $this->isSourceSet() :
                $code[] = rtrim( $this->getSource()->toString());
                break;
            case $this->isFcnInvokeSet() :
                $code = $this->getFcnInvoke()->toArray();
                break;
            default :
                throw new RuntimeException( $ERR );
                break;
        }
        return $code;
    }
    /**
     * @param bool $strict  false returns fixedSourceValue as string
     * @return bool|float|int|string
     */
    public function getFixedSourceValue( $strict = true )
    {
        if( $strict || $this->isExpression ) {
            return $this->fixedSourceValue;
        }
        return Util::renderScalarValue( $this->fixedSourceValue );
    }

    /**
     * @return bool
     */
    public function isFixedSourceValueSet()
    {
        return ( null !== $this->fixedSourceValue );
    }

    /**
     * @param bool|float|int|string $fixedSourceValue
     * @return static
     * @throws InvalidArgumentException
     */
    public function setFixedSourceValue( $fixedSourceValue )
    {
        if( ! is_scalar( $fixedSourceValue )) {
            throw new InvalidArgumentException(
                sprintf( self::$ERRx, var_export( $fixedSourceValue, true  ))
            );
        }
        $this->fixedSourceValue = $fixedSourceValue;
        $this->isExpression     = false;
        return $this;
    }

    /**
     * @param string $expression  any PHP expression
     * @return static
     */
    public function setSourceExpression( $expression )
    {
        if( ! is_string( $expression )) {
            throw new InvalidArgumentException(
                sprintf( self::$ERRx, var_export( $expression, true  ))
            );
        }
        $this->fixedSourceValue = rtrim( trim( $expression ), self::$END );
        $this->isExpression     = true;
        return $this;
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
                $this->setFixedSourceValue( $variable );
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
        }
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
