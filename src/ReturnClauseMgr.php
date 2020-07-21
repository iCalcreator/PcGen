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

/**
 * Class ReturnClauseMgr
 *
 * Manages method/function coded return values
 *
 * With return values means
 *   fixed (scalar) values
 *   constant or variable
 *   class property (opt static) or constant
 *   class means 'this' (ie class instance) or otherClass
 *   otherClass means class instance (variable) or FQCN (also interface)
 *
 * @package Kigkonsult\PcGen\Rows
 */
final class ReturnClauseMgr extends BaseR1
{
    /**
     * @param string     $class
     * @param mixed      $variable
     * @param int|string $index
     * @return static
     * @throws InvalidArgumentException
     */
    public static function factory( $class = null, $variable = null, $index = null )
    {
        return self::init()->setSource( $class, $variable, $index );
    }

    /**
     * Return (single) row return clause
     *
     * @return array
     * @throws RuntimeException
     */
    public function toArray()
    {
        $row1 = $this->getbaseIndent() . $this->getIndent() . self::RETURN_T;
        $code = $this->getRenderedSource();
        if(( 1 == count( $code )) && empty( $code[0] )) { // Source initiated null, null, null
            $code = [ $row1 ];
        }
        else {
            $code[0] = $row1 . self::SP1 . $code[0];
        }
        $lastIx        = count( $code ) - 1;
        $code[$lastIx] = rtrim( $code[$lastIx] ) . self::$END;
        return Util::nullByteClean( $code );
    }
}
