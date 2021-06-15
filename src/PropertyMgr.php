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

/**
 * Class PropertyMgr
 *
 * Manages class properties
 *
 * @package Kigkonsult\PcGen\Dto
 */
final class PropertyMgr extends VariableMgr
{
    /**
     * Override parent BaseC, alter PUBLIC to PRIVATE
     *
     * @var string
     */
    protected $visibility = self::PRIVATE_;

    /**
     * @var bool
     */
    private $makeGetter = true;

    /**
     * @var bool
     */
    private $makeSetter = true;

    /**
     * True if property is argument in (static) factory method (and factory is marked to be produced)
     *
     * @var bool
     */
    private $argInFactory = false;

    /**
     * @param VariableMgr $variableMgr
     * @return static
     */
    public function cloneFromParent( VariableMgr $variableMgr ) : self
    {
        return $this->setVarDto( $variableMgr->getVarDto())
            ->setStatic( $variableMgr->isStatic())
            ->setVisibility( $variableMgr->getVisibility());
    }

    /**
     * @return bool
     */
    public function isMakeGetter() : bool
    {
        return $this->makeGetter;
    }

    /**
     * @param bool $makeGetter
     * @return static
     */
    public function setMakeGetter( bool $makeGetter ) : self
    {
        $this->makeGetter = $makeGetter;
        if( $this->makeGetter && ( $this->isConst() || $this->isStatic())) {
            $this->makeGetter = false;
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isMakeSetter() : bool
    {
        return $this->makeSetter;
    }

    /**
     * @param bool $makeSetter
     * @return static
     */
    public function setMakeSetter( bool $makeSetter ) : self
    {
        $this->makeSetter = $makeSetter;
        if( $this->makeSetter && ( $this->isConst() || $this->isStatic())) {
            $this->makeSetter = false;
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isFactoryFcnArgument() : bool
    {
        return $this->argInFactory;
    }

    /**
     * @param bool $argInFactory
     * @return static
     */
    public function setArgInFactory( bool $argInFactory ) : self
    {
        $this->argInFactory = $argInFactory;
        if( $this->argInFactory && ( $this->isConst() || $this->isStatic())) {
            $this->argInFactory = false;
        }
        return $this;
    }

    /**
    /**
     * If true, set instance to CONSTANT with PUBLIC visibility
     *
     * Override Variable parent
     *
     * @param bool $isConst
     * @return static
     */
    public function setIsConst( $isConst = true ) : VariableMgr
    {
        $this->isConst = $isConst ?? true;
        if( $this->isConst ) {
            $this->makeGetter = false;
            $this->makeSetter = false;
            $this->setVisibility( self::PUBLIC_ );
        }
        return $this;
    }

    /**
     * If true, set instance to class (static) variable with PROTECTED visibility
     *
     * Override parent BaseC
     *
     * @param bool $static
     * @return static
     */
    public function setStatic( $static = true ) : BaseC
    {
        $this->static = $static ?? true;
        if( $this->static ) {
            $this->makeGetter = false;
            $this->makeSetter = false;
            $this->setVisibility( self::PROTECTED_ );
        }
        return $this;
    }
}
