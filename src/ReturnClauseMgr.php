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
    public static function factory( $class = null, $variable = null, $index = null ) {
        return self::init()->setSource( $class, $variable, $index );
    }

    /**
     * Return (single) row return clause
     *
     * class             (scope)   variable (type)
     * null                         bool, int, string, $-prefixed string (opt with subjectIndex) ie variable
     *                              but not closure or callable...
     * self                ::       string (constant), $-prefixed string (opt with subjectIndex) class (static) variable
     *                              (static) method
     * this                ->       string (property, opt with subjectIndex)
     * this                ->       predefind this->method( arg(s) )
     * this                         none (i.e. 'return $this')
     * otherClass (fqcn)   ::       string (constant), $-prefixed string (opt with subjectIndex) class (static) variable
     * $class              ::       string (constant), $-prefixed string (opt with subjectIndex) class (static) variable
     * $class              ->       string (opt with subjectIndex), NOT accepted here (class with public property)
     *
     * @return array
     * @throws RuntimeException
     */
    public function toArray() {
        static $RETURN = 'return';
        $row1 = $this->getbaseIndent() . $this->getIndent() . $RETURN;
        $code = $this->getRenderedSource();
        if( empty( $code )) { // Source initiated null, null, null
            $code = [ $row1 ];
        }
        else {
            $code[0] = $row1 . self::$SP1 . $code[0];
        }
        $lastIx        = count( $code ) - 1;
        $code[$lastIx] = rtrim( $code[$lastIx] ) . self::$END;
        return Util::nullByteClean( $code );
    }

    /**
     * Set return source (EntityMgr) as class ('this' class instance) property, opt with index
     *
     * @param mixed      $property
     * @param int|string $index
     * @return static
     * @throws InvalidArgumentException
     */
    public function setThisPropertySource( $property, $index = null ) {
        if( ! is_string( $property )) {
            throw new InvalidArgumentException( sprintf( self::$ERRx, var_export( $property, true )));
        }
        if( Util::isVarPrefixed( $property )) {
            $property = substr( $property, 1 );
        }
        $this->source = EntityMgr::factory( self::THIS_KW, $property, $index );
        return $this;
    }

    /**
     * Set return source (EntityMgr) as plain variable, opt with index
     *
     * @param mixed      $variable
     * @param int|string $index
     * @return static
     * @throws InvalidArgumentException
     */
    public function setVariableSource( $variable, $index = null ) {
        if( ! is_string( $variable )) {
            throw new InvalidArgumentException( sprintf( self::$ERRx, var_export( $variable, true )));
        }
        if( ! Util::isVarPrefixed( $variable )) {
            $variable = self::VARPREFIX . $variable;
        }
        $this->source = EntityMgr::factory( null, $variable, $index );
        return $this;
    }

    /**
     * @return string
     */
    public function getClass() {
        return $this->getSource()->getClass();
    }

    /**
     * @param string $prefix
     * @return static
     * @throws InvalidArgumentException
     */
    public function setClass( $prefix = null ) {
        if( null === $this->source ) {
            $this->setSource( EntityMgr::init());
        }
        $this->getSource()->setClass( $prefix );
        return $this;
    }

    /**
     * @return string
     */
    public function getVariable() {
        return $this->getSource()->getVariable();
    }

    /**
     * @param mixed $variable
     * @return static
     * @throws InvalidArgumentException
     */
    public function setVariable( $variable ) {
        if( null === $this->source ) {
            $this->setSource( EntityMgr::init());
        }
        $this->getSource()->setVariable( $variable );
        return $this;
    }

    /**
     * @return int|string
     */
    public function getIndex() {
        return $this->getSource()->getIndex();
    }

    /**
     * @param int|string $index
     * @return static
     * @throws InvalidArgumentException
     */
    public function setIndex( $index ) {
        if( null === $this->source ) {
            $this->setSource( EntityMgr::init());
        }
        $this->getSource()->setIndex( $index );
        return $this;
    }

    /**
     * @param bool $isConst
     * @return static
     */
    public function setIsConst( $isConst = true ) {
        if( null === $this->source ) {
            $this->setSource();
        }
        $this->getSource()->setIsConst((bool) $isConst );
        return $this;
    }

    /**
     * @return bool
     */
    public function isStatic() {
        return $this->getSource()->isStatic();
    }

    /**
     * @param bool $staticStatus
     * @return static
     */
    public function setIsStatic( $staticStatus = true ) {
        $this->getSource()->setIsStatic((bool) $staticStatus );
        return $this;
    }

}
