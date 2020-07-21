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

/**
 * Class BaseC
 *
 * Adds visibility and static to BaseB
 *
 * @package Kigkonsult\PcGen
 */
abstract class BaseC extends BaseB
{
    /**
     * @var string
     */
    protected $visibility = self::PUBLIC_;

    /**
     * @var bool
     */
    protected $static = false;

    /**
     * @return string
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @return bool
     */
    public function isVisibilitySet()
    {
        return ( null !== $this->visibility );
    }

    /**
     * @param string $visibility
     * @return static
     * @throws InvalidArgumentException
     */
    public function setVisibility( $visibility = null )
    {
        static $FMT = 'Invalid visibility ';
        static $VISIBILITIES = [
            self::PUBLIC_,
            self::PROTECTED_,
            self::PRIVATE_
        ];
        if( empty( $visibility )) {
            $this->visibility = null;
            return $this;
        }
        foreach( $VISIBILITIES as $vsblt ) {
            if( 0 === strcasecmp( $visibility, $vsblt )) {
                $this->visibility = $vsblt;
                return $this;
            }
        }
        throw new InvalidArgumentException( $FMT . $visibility );
    }

    /**
     * @return bool
     */
    public function isStatic()
    {
        return $this->static;
    }

    /**
     * @param bool $static
     * @return static
     */
    public function setStatic( $static = true )
    {
        $this->static = (bool) $static;
        return $this;
    }
}
