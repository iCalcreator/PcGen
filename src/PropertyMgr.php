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
     * @return bool
     */
    public function isMakeGetter() {
        return $this->makeGetter;
    }

    /**
     * @param bool $makeGetter
     * @return PropertyMgr
     */
    public function setMakeGetter( $makeGetter ) {
        $this->makeGetter = (bool) $makeGetter;
        if( $this->makeGetter && ( $this->isConst() || $this->isStatic())) {
            $this->makeGetter = false;
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isMakeSetter() {
        return $this->makeSetter;
    }

    /**
     * @param bool $makeSetter
     * @return PropertyMgr
     */
    public function setMakeSetter( $makeSetter ) {
        $this->makeSetter = (bool) $makeSetter;
        if( $this->makeSetter && ( $this->isConst() || $this->isStatic())) {
            $this->makeSetter = false;
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isFactoryFcnArgument()
    {
        return $this->argInFactory;
    }

    /**
     * @param bool $argInFactory
     * @return static
     */
    public function setArgInFactory( $argInFactory ) {
        $this->argInFactory = (bool) $argInFactory;
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
     * @return VariableMgr
     */
    public function setIsConst( $isConst = true ) {
        $this->isConst = (bool) $isConst;
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
    public function setStatic( $static = true ) {
        $this->static = (bool) $static;
        if( $this->static ) {
            $this->makeGetter = false;
            $this->makeSetter = false;
            $this->setVisibility( self::PROTECTED_ );
        }
        return $this;
    }

}
