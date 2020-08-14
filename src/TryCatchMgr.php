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
 * Class TryCatchMgr
 *
 * Manages try-catch expression
 *     try-body is set using TryCatchMgr::setBody()
 *     catch-bodies using (single) TryCatchMgr::appendCatch() or (array) TryCatchMgr::setCatch()
 *
 * @package Kigkonsult\PcGen
 */
class TryCatchMgr extends BaseB
{
    /**
     * Try-clause catch expressions
     *
     * @var catchMgr[]
     */
    private $catch = [];

    /**
     * TryCatchMgr factory method, set 'Exception'-catch with body
     *
     * @param string|string[] $tryBody
     * @param string|string[] $catchBody
     * @return static
     */
    public static function factory( $tryBody, $catchBody )
    {
        $instance = self::init();
        $instance->setBody( $tryBody );
        $instance->appendCatch( null, $catchBody );
        return $instance;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        static $FMT = '%stry {';
        if( ! $this->isCatchSet()) {
            $this->appendCatch();
        }
        $indent1  = $this->baseIndent . $this->indent;
        $body     = array_merge(
            [ sprintf( $FMT, $indent1 ) ],
            $this->getBody( $indent1 ),
            [ $indent1 . self::$CODEBLOCKEND ]
        );
        foreach( array_keys( $this->catch ) as $cIx ) {
            $body = array_merge(
                $body,
                $this->catch[$cIx]->toArray()
            );
        } // end foreach
        return $body;
    }

    /**
     * @return bool
     */
    public function isCatchSet()
    {
        return ( ! empty( $this->catch ));
    }

    /**
     * Append single exception-expression, without $exception is an 'Exception'-exception set
     *
     * @param string|CatchMgr $exception
     * @param string|string[] $catchBody
     * @return static
     * @throws InvalidArgumentException
     */
    public function appendCatch( $exception = null, $catchBody = null )
    {
        switch( true ) {
            case empty( $exception ) :
                // set exception-expression Exception
                $exception = CatchMgr::init( $this );
                break;
            case is_string( $exception ) :
                $exception = CatchMgr::init( $this )->setException( $exception );
                break;
            case ( $exception instanceof CatchMgr ) :
                $catchBody = null;
                break;
            default :
                throw new InvalidArgumentException(
                    sprintf(
                        self::$ERRx,
                        (string) ( is_object( $exception )
                            ? get_class( $exception )
                            : $exception )
                    )
                );
                break;
        } // end switch
        if( ! empty( $catchBody )) {
            $exception->setBody( $catchBody );
        }
        $this->catch[] = $exception;
        return $this;
    }
    /**
     * Set array catch-expressions
     *
     * Each array element can be
     *     string (Exception)
     *     string (Exception), catchBody
     *     catchMgr
     *
     * @param array|catchMgr[] $catch
     * @return static
     */
    public function setCatch( $catch ) {
        foreach( array_keys( $catch ) as $cIx ) {
            switch( true ) {
                case is_string( $catch[$cIx] ) :
                    // exception without body
                    $this->appendCatch( $catch[$cIx] );
                    break;
                case is_array( $catch[$cIx] ) :
                    $this->appendCatch(
                        $catch[$cIx][0],
                        ( $catch[$cIx][1] ?: null )
                    );
                    break;
                case ( $catch[$cIx] instanceof CatchMgr ) :
                    $this->appendCatch( $catch[$cIx] );
                    break;
                default :
                    throw new InvalidArgumentException(
                        sprintf(
                            self::$ERRx,
                            (string) ( is_object( $catch[$cIx] )
                                ? get_class( $catch[$cIx] )
                                : $catch[$cIx] )
                        )
                    );
                    break;
            } // end switch
        } // end foreach
        return $this;
    }
}