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
namespace Kigkonsult\PcGen\Dto;

use Kigkonsult\PcGen\Assert;
use Kigkonsult\PcGen\ClassMgr;
use InvalidArgumentException;

use function implode;
use function in_array;
use function sprintf;

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
     * @var string
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
     * @param string $fqcn
     * @param null|string $alias
     * @param null|string $type
     * @return static
     * @throws InvalidArgumentException
     */
    public static function factory( string $fqcn, $alias = null, $type = null ) : self
    {
        $instance = new self();
        $instance->setSubject( $fqcn );
        if( null !== $alias ) {
            $instance->setAlias( $alias );
        }
        if( null !== $type ) {
            $instance->setUseSubjectType( $type );
        }
        return $instance;
    }

    /**
     * @return null|string
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
    public function setSubject( string $subject ) : self
    {
        Assert::assertFqcn( $subject );
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return bool
     */
    public function isAliasSet() : bool
    {
        return ( ! empty( $this->alias ));
    }

    /**
     * @param string $alias
     * @return UseSubjectDto
     * @throws InvalidArgumentException
     */
    public function setAlias( string $alias ) : self
    {
        Assert::assertPhpVar( $alias );
        $this->alias = $alias;
        return $this;
    }

    /**
     * @return string
     */
    public function getUseSubjectType() : string
    {
        return $this->useSubjectType;
    }

    /**
     * @return bool
     */
    public function isClassUseType() : bool
    {
        return ( ClassMgr::CLASS_ === $this->useSubjectType );
    }

    /**
     * @return bool
     */
    public function isFunctionUseType() : bool
    {
        return ( ClassMgr::FUNC_ === $this->useSubjectType );
    }

    /**
     * @return bool
     */
    public function isConstantUseType() : bool
    {
        return ( ClassMgr::CONST_ === $this->useSubjectType );
    }

    /**
     * @param string $useSubjectType
     * @return UseSubjectDto
     * @throws InvalidArgumentException
     */
    public function setUseSubjectType( string $useSubjectType ) : self
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
    public function getSortKey() : string
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
    public function toString() : string
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
