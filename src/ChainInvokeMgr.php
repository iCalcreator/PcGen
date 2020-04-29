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

final class ChainInvokeMgr extends BaseA
{

    /**
     * @var FcnInvokeMgr[]
     */
    private $chainInvokes = [];

    /**
     * @var null
     */
    private $chainClass = null;

    /**
     * @param FcnInvokeMgr ...$args
     * @return static
     */
    public static function factory( ...$args ) {
        return self::init()->setChainInvokes( $args );
    }

    /**
     * @inheritDoc
     * @throws RuntimeException
     */
    public function toArray() {
        static $ERR = 'No function directives';
        if( ! $this->isChainInvokesSet()) {
            throw new RuntimeException( $ERR );
        }
        $count = count( $this->chainInvokes );
        $code  = [ rtrim( $this->chainInvokes[0]->toString()) ];
        if( 1 == $count ) {
            return $code;
        }
        $len    = strlen( $this->chainClass );
        $ind    = $this->getBaseIndent() . $this->getIndent();
        for( $ifx = 1; $ifx < $count; $ifx++ ) {
            $code[] = $ind . substr( rtrim( $this->chainInvokes[$ifx]->toString()), $len );
        }
        return $code;
    }

    /**
     * @return FcnInvokeMgr[]
     */
    public function getChainInvokes() {
        return $this->chainInvokes;
    }

    /**
     * @return bool
     */
    public function isChainInvokesSet() {
        return ( ! empty( $this->chainInvokes ));
    }

    /**
     * Append FcnInvokeMgr to chained invokes, only $this or $class allowed for first if next is set
     *
     * @param FcnInvokeMgr $chainedInvoke
     * @return static
     * @throws InvalidArgumentException
     */
    public function appendChainedInvoke( FcnInvokeMgr $chainedInvoke ) {
        $firstInvoke = empty( $this->chainInvokes );
        $chainClass  = $chainedInvoke->getName()->getClass();
        switch( true ) {
            case $firstInvoke :
                $this->chainClass = $chainedInvoke->getName()->getClass();
                break;
            case (( FcnInvokeMgr::THIS_KW != $this->chainClass ) && ! Util::isVarPrefixed( $this->chainClass )) :
                // 2nd invoke require this or $class already set
                throw new InvalidArgumentException( sprintf( self::$ERRx, $chainClass ));
                break;
            case (( FcnInvokeMgr::THIS_KW != $chainClass ) && ! Util::isVarPrefixed( $chainClass )) :
                // next invoke require this or $class set
                throw new InvalidArgumentException( sprintf( self::$ERRx, $chainClass ));
                break;
            default :
                $chainedInvoke->getName()->setClass( $this->chainClass );
                break;
        }
        $this->chainInvokes[] = $chainedInvoke;
        return $this;
    }

    /**
     * @param FcnInvokeMgr[] $chainInvokes
     * @return static
     * @throws InvalidArgumentException
     */
    public function setChainInvokes( array $chainInvokes ) {
        $this->chainInvokes = [];
        $this->chainClass   = null;
        foreach( array_keys( $chainInvokes ) as $fIx ) {
            $this->appendChainedInvoke( $chainInvokes[ $fIx ] );
        }
        return $this;
    }

}
