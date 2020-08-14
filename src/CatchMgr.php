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

    public static function factory( $exception, $catchBody = null )
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
    public function toArray() {
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
    public function setException( $exception ) {
        $this->exception = $exception;
        return $this;
    }
}