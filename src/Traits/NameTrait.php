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
use Kigkonsult\PcGen\Assert;

/**
 * Class NameTrait
 *
 * Add name property
 *
 * @package Kigkonsult\PcGen\Traits
 */
trait NameTrait
{
    /**
     * @var string
     */
    private $name = null;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isNameSet()
    {
        return ( null !== $this->name );
    }

    /**
     * @param string $name
     * @return static
     * @throws InvalidArgumentException
     */
    public function setName( $name )
    {
        $this->name = Assert::assertPhpVar( $name );
        return $this;
    }
}