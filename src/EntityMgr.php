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
 * Class EntityMgr
 *
 * Manages PHP entities
 *     class  : null, parent, self, this, 'otherClass', '$class'
 *     variable : variable/property
 *     index   : opt array index
 * Ex: $var, self::$var, $this->var, fqcn::$var, opt $var[]
 *
 * @package Kigkonsult\PcGen\Rows
 */
final class EntityMgr extends BaseA
{

    /**
     * @var array
     */
    public static $CLASSPREFIXes = [ self::PARENT_KW, self::SELF_KW, self::THIS_KW ];

    /**
     * Values : null, parent, self, $this, 'otherClass', '$class'
     *
     * @var string
     */
    private $class = null;

    /**
     * @var string
     */
    private $variable = null;

    /**
     * Opt. array index
     *
     * @var int|string
     */
    private $index = null;

    /**
     * If variable is a constant
     *
     * @var bool
     */
    private $isConst = false;

    /**
     * @var bool
     */
    private $isStatic = false;

    /**
     * @var bool
     */
    private $forceVarPrefix = true;

    /**
     * @param string     $class
     * @param string     $variable
     * @param int|string $index
     * @param bool       $forceVarPrefix
     * @return static
     * @throws InvalidArgumentException
     */
    public static function factory( $class = null, $variable = null, $index = null, $forceVarPrefix = true ) {
        $instance = self::init();
        $instance->setClass( $class );
        if( null !== $variable ) {
            $instance->setVariable( $variable );
        }
        if( null !== $index ) {
            $instance->setIndex( $index );
        }
        $instance->setForceVarPrefix( $forceVarPrefix );
        return $instance;
    }

    /**
     * @return string  testing
     */
    public function __toString() {
        return (( null === $this->class ) ? self::SP1 : $this->class ) . ' - ' .
            (( null === $this->variable ) ? self::SP1 : $this->variable ) . ' - ' .
            (( null === $this->index ) ? self::SP1 : '[' . $this->index . ']' ) .
            ', isStatic : ' . var_export( $this->isStatic, true ) .
            ', isConst : ' . var_export( $this->isConst, true ) .
            ', $-force : ' . var_export( $this->forceVarPrefix, true );
    }

    /**
     * @return array
     */
    public function toArray() {
        return [ $this->toString() ];
    }

    /**
     * Note, NO trailing eol here!!
     *
     * @return string
     */
    public function toString() {
        if(( null === $this->class ) && ( null === $this->variable )) { // empty ??
            return self::SP0;
        }
        $row = $this->getPrefixCode();
        $row = $this->getSubjectCode( $row );
        $row = $this->getIndexCode( $row );
        return $row;
    }

    /**
     * @return string
     */
    private function getPrefixCode() {
        $row = self::SP0;
        switch( true ) {
            case empty( $this->class ) :
                break;
            case in_array( $this->class, self::$CLASSPREFIXes ) :
                $row .= $this->class;
                break;
            default : // otherClass(fqcn) or $class  with class constant or class variable
                $row .= $this->class;
                break;
        }
        return $row;
    }

    /**
     * @param string $row
     * @return string
     */
    private function getSubjectCode( $row ) {
        static $COLONCOLON = '::';
        static $DASHARROW  = '->';
        switch( true ) {
            case empty( $this->variable ) :
                $this->index = null;
                break;

            case $this->isConst :
                if( ! empty( $this->class )) {
                    $row .= $COLONCOLON;
                }
                $row .= strtoupper( Util::unSetVarPrefix( $this->variable ));
                break;

            case empty( $this->class ) :
                $row .= $this->fixVariablePrefix( $this->forceVarPrefix );
                break;
            case in_array( $this->class, [ self::PARENT_KW, self::SELF_KW ]) : // always static property
                $row .= $COLONCOLON;
                $row .= $this->fixVariablePrefix( $this->forceVarPrefix );
                break;
            case ( self::THIS_KW == $this->class ) :
                $row .= $DASHARROW;
                $row .= $this->fixVariablePrefix( false );
                break;
            case ( Util::isVarPrefixed( $this->class ) && ! $this->isStatic()) :
                $row .= $DASHARROW;
                $row .= $this->fixVariablePrefix( false );
                break;
            case ( Util::isVarPrefixed( $this->class ) && $this->isStatic()) :
                $row .= $COLONCOLON;
                $row .= $this->fixVariablePrefix( $this->forceVarPrefix );
                break;
            default : // FQCN, always static property
                $row .= $COLONCOLON;
                $row .= $this->fixVariablePrefix( $this->forceVarPrefix );
                break;
        }
        return $row;
    }

    /**
     * @param bool $expectVarPrefixed
     * @return string
     */
    private function fixVariablePrefix( $expectVarPrefixed ) {
        if( $expectVarPrefixed ) {
            return Util::setVarPrefix( $this->variable );
        }
        return Util::unSetVarPrefix( $this->variable );
    }

    /**
     * @param string $row
     * @return string
     */
    private function getIndexCode( $row ) {
        static $ARR = '[%s]';
        switch( true ) { // index
            case ( 0 == strcmp( self::ARRAY2_T, (string) $this->index )) :
                $row .= self::ARRAY2_T;
                break;
            case Util::isInt( $this->index ) :
                $row .= sprintf( $ARR, (string) $this->index );
                break;
            case empty( $this->index ) :
                break;
            default :
                $row .= sprintf( $ARR, $this->index );
                break;
        }
        return $row;
    }

    /**
     * @return string
     */
    public function getClass() {
        return $this->class;
    }

    /**
     * Accepts null, self, this, otherClass (fqcn), $class
     *
     * @param string $class
     * @return static
     * @throws InvalidArgumentException
     */
    public function setClass( $class = null ) {
        switch( true ) {
            case ( empty( $class ) || in_array( $class, self::$CLASSPREFIXes )) :
                break;
            case Util::isVarPrefixed( $class ) :
                Assert::assertPhpVar( $class );
                break;
            default :
                Assert::assertFqcn( $class );
                break;
        }
        $this->class = $class;
        return $this;
    }

    /**
     * @return bool|string
     */
    public function getVariable() {
        return $this->variable;
    }

    /**
     * Set variable
     *
     * @param string $variable
     * @return static
     * @throws InvalidArgumentException
     */
    public function setVariable( $variable ) {
        if( ! is_string( $variable ) || empty( $variable )) {
            throw new InvalidArgumentException( sprintf( self::$ERRx, var_export( $variable, true )));
        }
        Assert::assertPhpVar( $variable );
        $this->variable = $variable;
        return $this;
    }

    /**
     * @return int|string
     */
    public function getIndex() {
        return $this->index;
    }

    /**
     * Set variable/property index
     *
     * @param int|string $index
     * @return static
     * @throws InvalidArgumentException
     */
    public function setIndex( $index ) {
        switch( true ) {
            case Util::isInt( $index ) :
                break;
            case (( null === $index ) || empty( $index )):
                return $this;
            case ( ! is_string( $index )) :
                throw new InvalidArgumentException( sprintf( self::$ERRx, var_export( $index, true )));
                break;
            case ( self::ARRAY2_T == $index ) :
                break;
            default :
                $index = Assert::assertPhpVar( $index );
                $index = Util::setVarPrefix( $index );
                break;
        } // end switch
        $this->index = $index;
        return $this;
    }

    /**
     * @return bool
     */
    public function isConst() {
        return $this->isConst;
    }

    /**
     * @param bool $isConst
     * @return static
     */
    public function setIsConst( $isConst = true ) {
        $this->isConst = (bool) $isConst;
        return $this;
    }

    /**
     * @return bool
     */
    public function isStatic() {
        return $this->isStatic;
    }

    /**
     * Set isStatic, only for class '$class', ignored by the others
     *
     * @param bool $isStatic
     * @return static
     */
    public function setIsStatic( $isStatic = true ) {
        $this->isStatic = (bool) $isStatic;
        return $this;
    }

    /**
     * @param bool $forceVarPrefix
     * @return EntityMgr
     */
    public function setForceVarPrefix( $forceVarPrefix = true ) {
        $this->forceVarPrefix = (bool) $forceVarPrefix;
        return $this;
    }

}
