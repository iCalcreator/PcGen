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

use function array_merge;
use function sprintf;

/**
 * Class CatchMgr
 *
 * Manages try-clause catch expression
 *     catch Exception argument always '$e'
 *     used only by TryCatchMgr
 *
 * @package Kigkonsult\PcGen
 */
class CatchMgr extends BaseB
{
    const EXCEPTION                = 'Exception';
    const RUNTIMEEXCEPTION         = 'RuntimeException';
    const INVALIDARGUMENTEXCEPTION = 'InvalidArgumentException';

    /**
     * @var string
     */
    private $exception = self::EXCEPTION;

    /**
     * @param string $exception
     * @param null $catchBody
     * @return CatchMgr
     */
    public static function factory( string $exception, $catchBody = null ) : self
    {
        $instance = new self();
        $instance->setException( $exception );
        if( ! empty( $catchBody )) {
            $instance->setBody( $catchBody );
        }
        return $instance;
    }

    /**
     * @inheritDoc
     */
    public function toArray() : array
    {
        static $FMT = '%scatch( %s $e ) {';
        $indent1 = $this->baseIndent . $this->indent;
        return array_merge(
            [ sprintf( $FMT, $indent1, $this->exception ) ],
            $this->getBody( $indent1 ),
            [ $indent1 . self::$CODEBLOCKEND ]
        );
    }

    /**
     * Set Exception class, note, accepts all
     *
     * @param string $exception
     * @return static
     */
    public function setException(string $exception ) : self
    {
        $this->exception = $exception;
        return $this;
    }
}
