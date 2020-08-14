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
 * Class ForeachMgr
 *
 * Manages foreach loops
 * Accepts variable, classProperty and class function as array_expression
 * AS for now no reference values
 * The foreach logic body is set using ForeachMgr::setBody()
 *
 * @package Kigkonsult\PcGen
 * @todo reference values
 */
final class ForeachMgr extends BaseB
{
    /**
     * Expression to iterate : variable, classProperty, class function
     *
     * @var string|EntityMgr|FcnInvokeMgr    anything returning array|Traversable
     */
    private $iterator = null;

    /**
     * The opt. index PHP variable name
     *
     * @var string
     */
    private $key = null;

    /**
     * The iteration current element value PHP variable name, default 'value'
     *
     * @var string
     */
    private $iterValue = 'value';

    /**
     * ForeachMgr class factory method
     *
     * @param string|EntityMgr|FcnInvokeMgr $iterator
     * @param string $key
     * @param string $iterValue
     * @return static
     * @throws InvalidArgumentException
     */
    public static function factory(
        $iterator = null,
        $key = null,
        $iterValue = null
    ) {
        $instance = self::init();
        if( null != $iterator ) {
            $instance->setIterator( $iterator );
        }
        if( null != $key ) {
            $instance->setKey( $key );
        }
        if( null != $iterValue ) {
            $instance->setIterValue( $iterValue );
        }
        return $instance;
    }

    /**
     * Return array, foreach loop with body
     *
     * @return array
     * @throws RuntimeException
     */
    public function toArray()
    {
        static $ERR1  = 'Missing foreach array|Traversable';
        static $FMT1  = '%sforeach( ';
        static $FMT2  = ' as ';
        static $FMT3  = ' => ';
        static $FMT4  = ' ) {';
        static $FMT5  = '} // end foreach';
        if( ! $this->isIteratorSet()) {
            throw new RuntimeException( $ERR1 );
        }
        $indent   = $this->baseIndent . $this->indent;
        $row      = sprintf( $FMT1, $indent );
        $iterName = $this->getIterator();
        if( is_string( $iterName )) {
            $row .= self::VARPREFIX . $iterName;
        }
        else {
            $row .= trim( $iterName->toString());
        }
        $row     .= $FMT2;
        if( $this->isKeySet()) {
            $row .= self::VARPREFIX . $this->getKey() . $FMT3;
        }
        $row     .= self::VARPREFIX . $this->getIterValue() . $FMT4;
        return array_merge(
            [ $row ],
            $this->getBody( $this->indent . $this->indent ),
            [ $indent . $FMT5 ]
        );
    }

    /**
     * @return string|EntityMgr|FcnInvokeMgr
     */
    public function getIterator()
    {
        return $this->iterator;
    }

    /**
     * @return bool
     */
    public function isIteratorSet()
    {
        return ( null !== $this->iterator );
    }

    /**
     * @param string|EntityMgr|FcnInvokeMgr $iterator
     * @return static
     * @throws InvalidArgumentException
     */
    public function setIterator( $iterator )
    {
        switch( true ) {
            case ( $iterator instanceof EntityMgr ) :
                break;
            case ( $iterator instanceof FcnInvokeMgr ) :
                break;
            case is_string( $iterator ) :
                $iterator = Assert::assertPhpVar( $iterator );
                break;
            default :
                throw new InvalidArgumentException(
                    sprintf(
                        self::$ERRx,
                        (string) ( is_object( $iterator )
                            ? get_class( $iterator )
                            : $iterator
                        )
                    )
                );
        } // end switch
        $this->iterator = $iterator;
        return $this;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return bool
     */
    public function isKeySet()
    {
        return ( null !== $this->key );
    }

    /**
     * @param string $key
     * @return static
     * @throws InvalidArgumentException
     */
    public function setKey( $key )
    {
        $this->key = Assert::assertPhpVar( $key );
        return $this;
    }

    /**
     * @return string
     */
    public function getIterValue() {
        return $this->iterValue;
    }

    /**
     * @param string $iterValue
     * @return static
     * @throws InvalidArgumentException
     */
    public function setIterValue( $iterValue )
    {
        $this->iterValue = Assert::assertPhpVar( $iterValue );
        return $this;
    }
}
