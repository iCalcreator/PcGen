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
namespace Kigkonsult\PcGen\Dto;

use Kigkonsult\PcGen\Assert;
use Kigkonsult\PcGen\ClassMgr;
use InvalidArgumentException;

/**
 * Class CatchMgr
 *
 * Holds class use-clause subjects; class, function or constant
 *
 * @package Kigkonsult\PcGen
 */
class UseSubjectDto
{
    /**
     * The subject itself; class, function or constant string (fqcn)
     *
     * @var string
     */
    private $subject = null;

    /**
     * The subject opt. alias
     *
     * @var string
     */
    private $alias   = null;

    /**
     * The type of use; class, function or constant
     *
     * @var int
     */
    private $useSubjectType = ClassMgr::CLASS_;

    /**
     * Class use-clause subject types, CLASS_ default
     */
    private static $TYPES = [
        ClassMgr::CLASS_,
        ClassMgr::CONST_,
        ClassMgr::FUNC_,
    ];

    /**
     * Class factory method
     *
     * @param $fqcn
     * @param $alias
     * @param $type
     * @return static
     * @throws InvalidArgumentException
     */
    public static function factory( $fqcn, $alias = null, $type = null )
    {
        $instance = new self();
        $instance->setSubject( $fqcn );
        if( null != $alias ) {
            $instance->setAlias( $alias );
        }
        if( null != $type ) {
            $instance->setUseSubjectType( $type );
        }
        return $instance;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     * @return UseSubjectDto
     * @throws InvalidArgumentException
     */
    public function setSubject( $subject )
    {
        Assert::assertFqcn( $subject );
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return bool
     */
    public function isAliasSet()
    {
        return ( ! empty( $this->alias ));
    }

    /**
     * @param string $alias
     * @return UseSubjectDto
     * @throws InvalidArgumentException
     */
    public function setAlias( $alias )
    {
        Assert::assertPhpVar( $alias );
        $this->alias = $alias;
        return $this;
    }

    /**
     * @return int
     */
    public function getUseSubjectType()
    {
        return $this->useSubjectType;
    }

    /**
     * @return bool
     */
    public function isClassUseType()
    {
        return ( ClassMgr::CLASS_ === $this->useSubjectType );
    }

    /**
     * @return bool
     */
    public function isFunctionUseType()
    {
        return ( ClassMgr::FUNC_ === $this->useSubjectType );
    }

    /**
     * @return bool
     */
    public function isConstantUseType()
    {
        return ( ClassMgr::CONST_ === $this->useSubjectType );
    }

    /**
     * @param int $useSubjectType
     * @return UseSubjectDto
     * @throws InvalidArgumentException
     */
    public function setUseSubjectType( $useSubjectType )
    {
        static $ERR  = 'Invalid use type %s, expects one of %s';
        if( ! in_array( $useSubjectType, self::$TYPES )) {
            throw new InvalidArgumentException(
                sprintf( $ERR, $useSubjectType, implode( ClassMgr::$COMMA, self::$TYPES ))
            );
        }
        $this->useSubjectType = $useSubjectType;
        return $this;
    }

    /**
     * @return string
     */
    public function getSortKey()
    {
        switch( true ) {
            case $this->isClassUseType() :
                $sortKey = 1;
                break;
            case $this->isConstantUseType() :
                $sortKey = 2;
                break;
            default :
                $sortKey = 3;
                break;
        } // end switch
        $sortKey .= $this->isAliasSet() ? $this->alias : $this->subject;
        return $sortKey;
    }

    /**
     * Return nice rendered code
     *
     * @return string
     */
    public function toString()
    {
        static $USE         = 'use ';
        static $USEConstant = 'use const ';
        static $USEFunction = 'use function ';
        static $AS          = ' as ';
        static $SQ          = ';';
        switch( true ) {
            case $this->isClassUseType() :
                $row = $USE;
                break;
            case $this->isConstantUseType() :
                $row = $USEConstant;
                break;
            default :
                $row = $USEFunction;
                break;
        } // end switch
        $row .= $this->subject;
        if( $this->isAliasSet()) {
            $row .= $AS . $this->alias;
        }
        return $row .$SQ;
    }
}
