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

use InvalidArgumentException;

use function strcasecmp;

/**
 * Class BaseC
 *
 * Extend BaseB with visibility and static properties
 *
 * @package Kigkonsult\PcGen
 */
abstract class BaseC extends BaseB
{
    /**
     * @var null|string
     */
    protected $visibility = self::PUBLIC_;

    /**
     * @var bool
     */
    protected $static = false;

    /**
     * @return string
     */
    public function getVisibility() : string
    {
        return $this->visibility;
    }

    /**
     * @return bool
     */
    public function isVisibilitySet() : bool
    {
        return ( null !== $this->visibility );
    }

    /**
     * @param string $visibility
     * @return static
     * @throws InvalidArgumentException
     */
    public function setVisibility( $visibility = null ) : self
    {
        static $FMT = 'Invalid visibility ';
        static $VISIBILITIES = [
            self::PUBLIC_,
            self::PROTECTED_,
            self::PRIVATE_
        ];
        if( null === $visibility ) {
            $this->visibility = null;
            return $this;
        }
        foreach( $VISIBILITIES as $vsblt ) {
            if( 0 === strcasecmp( $visibility, $vsblt )) {
                $this->visibility = $vsblt;
                return $this;
            }
        } // end foreach
        throw new InvalidArgumentException( $FMT . $visibility );
    }

    /**
     * @return bool
     */
    public function isStatic() : bool
    {
        return $this->static;
    }

    /**
     * @param bool $static
     * @return static
     */
    public function setStatic( $static = true ) : self
    {
        $this->static = $static ?? true;
        return $this;
    }
}
