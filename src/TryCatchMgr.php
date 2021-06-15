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

use function array_keys;
use function array_merge;
use function get_class;
use function is_array;
use function is_object;
use function is_string;
use function sprintf;

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
     * @var array catchMgr[]
     */
    private $catch = [];

    /**
     * TryCatchMgr factory method, set 'Exception'-catch with body
     *
     * @param string|string[] $tryBody
     * @param string|string[] $catchBody
     * @return static
     */
    public static function factory( $tryBody, $catchBody ) : self
    {
        $instance = self::init();
        $instance->setBody( $tryBody );
        $instance->appendCatch( null, $catchBody );
        return $instance;
    }

    /**
     * @inheritDoc
     */
    public function toArray() : array
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
    public function isCatchSet() : bool
    {
        return ( ! empty( $this->catch ));
    }

    /**
     * Append single exception-expression, without $exception is an 'Exception'-exception set
     *
     * @param string|CatchMgr $exception
     * @param string|array $catchBody
     * @return static
     * @throws InvalidArgumentException
     */
    public function appendCatch( $exception = null, $catchBody = null ) : self
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
                $catchBody = [];
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
     * @param array  $catch
     * @return static
     */
    public function setCatch( array $catch ) : self
    {
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
            } // end switch
        } // end foreach
        return $this;
    }
}
