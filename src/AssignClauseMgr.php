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
use Kigkonsult\PcGen\Traits\OperatorTrait;
use RuntimeException;

/**
 * Class AssignClauseMgr
 *
 * Manages assign target (value) from
 *   source (value)
 *   (scalar) fixedSourceValue
 *   no 'body' code
 *
 * With
 *   target/source (values) means variable, constant, class property (opt static) or constant
 *   class means 'this' (ie class instance) or otherClass
 *   otherClass means class instance (variable) or FQCN (also interface)
 *
 * @package Kigkonsult\PcGen\Rows
 */
final class AssignClauseMgr extends BaseR1
{
    /**
     * @var EntityMgr
     */
    private $target = null;

    use OperatorTrait;

    /**
     * Set (EntityMgr) target and (EntityMgr) source
     *
     * 'Fixed' values are autodetected and update this->fixedSourceValue
     *
     * @param string     $targetClass one of null, self, this, otherClass (fqcn), $class
     * @param mixed      $targetVariable
     * @param int|string $targetIndex
     * @param string     $sourceClass one of null, self, this, otherClass (fqcn), $class
     * @param mixed      $sourceVariable
     * @param int|string $sourceIndex
     * @return static
     * @throws InvalidArgumentException
     */
    public static function factory(
        $targetClass = null,
        $targetVariable = null,
        $targetIndex = null,
        $sourceClass = null,
        $sourceVariable = null,
        $sourceIndex = null
    ) {
        return self::init()
            ->setTarget( $targetClass, $targetVariable, $targetIndex )
            ->setSource( $sourceClass, $sourceVariable, $sourceIndex );
    }

    /**
     * Return single row assign clause
     *
     * @return array|
     * @throws RuntimeException
     */
    public function toArray()
    {
        static $ERR1 = 'No target set';
        if( ! $this->isTargetSet()) {
            throw new RuntimeException( $ERR1 );
        }
        $row1    = $this->getbaseIndent() . $this->getIndent();
        $row1   .= $this->getTarget()->toString();
        $row1   .= $this->getOperator( false );
        $code    = $this->getRenderedSource();
        $code[0] = $row1 . $code[0];
        $lastIx         = count( $code ) - 1;
        $code[$lastIx] .= self::$END;
        return Util::nullByteClean( $code );
    }

    /**
     * @return EntityMgr
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @return bool
     */
    public function isTargetSet()
    {
        return ( null !== $this->target );
    }

    /**
     * Set (EntityMgr) target
     *
     * @param string|EntityMgr $class one of null, self, this, otherClass (fqcn), $class
     * @param mixed            $variable
     * @param int|string       $index
     * @return static
     * @throws InvalidArgumentException
     */
    public function setTarget( $class = null, $variable = null, $index = null )
    {
        switch( true ) {
            case ( $class instanceof EntityMgr ) : // replace
                $this->target = $class;
                break;
            case (( null === $class ) && ( null === $variable )) :
                $this->target = EntityMgr::init( $this );
                break;
            case ( null === $this->target ) :
                $this->target = EntityMgr::init( $this )
                    ->setClass( $class )
                    ->setVariable( $variable );
                if( null !== $index ) {
                    $this->target->setIndex( $index );
                }
                break;
            default :
                $this->getTarget()
                    ->setClass( $class )
                    ->setVariable( $variable )
                    ->setIndex( $index );
        }
        return $this;
    }

    /**
     * Set (EntityMgr) target as class ('this' class instance) property, opt with index
     *
     * @param mixed      $property
     * @param int|string $index
     * @return static
     * @throws InvalidArgumentException
     */
    public function setThisPropertyTarget( $property, $index = null )
    {
        if( ! is_string( $property )) {
            throw new InvalidArgumentException(
                sprintf( self::$ERRx, var_export( $property, true ))
            );
        }
        $this->target = EntityMgr::init( $this )
            ->setClass( self::THIS_KW )
            ->setVariable( Util::unSetVarPrefix( $property ));
        if( null !== $index ) {
            $this->target->setIndex( $index );
        }
        return $this;
    }

    /**
     * Set (EntityMgr) target as plain variable, opt with index
     *
     * @param mixed            $variable
     * @param int|string       $index
     * @return static
     * @throws InvalidArgumentException
     */
    public function setVariableTarget( $variable, $index = null )
    {
        if( ! is_string( $variable )) {
            throw new InvalidArgumentException(
                sprintf( self::$ERRx, var_export( $variable, true ))
            );
        }
        $this->target = EntityMgr::init( $this )
            ->setVariable( Util::setVarPrefix( $variable ));
        if( null !== $index ) {
            $this->target->setIndex( $index );
        }
        return $this;
    }

    /**
     * @param bool $isConst
     * @return static
     */
    public function setTargetIsConst( $isConst = true )
    {
        if( null === $this->target ) {
            $this->setTarget();
        }
        $this->getTarget()->setIsConst((bool) $isConst );
        return $this;
    }

    /**
     * @return bool
     */
    public function isTargetStatic()
    {
        return $this->isTargetSet() && $this->getTarget()->isStatic();
    }

    /**
     * @param bool $staticStatus
     * @return static
     */
    public function setTargetIsStatic( $staticStatus = true )
    {
        if( null === $this->target ) {
            $this->setTarget();
        }
        $this->getTarget()->setIsStatic((bool) $staticStatus );
        return $this;
    }
}
