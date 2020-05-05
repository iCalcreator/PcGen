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

abstract class BaseA implements PcGenInterface
{

    /**
     * Default
     *
     * @var string
     */
    protected static $DEFAULTEOL        = PHP_EOL;
    protected static $DEFAULTINDENT     = '    ';
    protected static $DEFAULTBASEINDENT = '    ';

    /**
     * @param array|string $eol
     */
    public static function setDefaultEol( $eol ) {
        self::$DEFAULTEOL = $eol;
    }

    /**
     * @param string $indent
     */
    public static function setDefaultIndent( $indent ) {
        self::$DEFAULTINDENT = $indent;
    }

    /**
     * @param string $indent
     */
    public static function setDefaultBaseIndent( $indent ) {
        self::$DEFAULTBASEINDENT = $indent;
    }

    protected static $DEFAULTVERSION = PHP_VERSION;
    public    static $TARGETVERSION  = null;

    /**
     * @return string
     */
    public static function getTargetPhpVersion() {
        return self::$TARGETVERSION;
    }

    /**
     * Set target PHP version, default current
     *
     * @param string $phpVersion
     * @return void
     */
    public static function setTargetPhpVersion( $phpVersion ) {
        self::$TARGETVERSION = $phpVersion;
    }

    /**
     * @var string
     */
    protected static $COMMA = ',';
    protected static $CRLFs = [ "\r\n", "\n\r", "\n", "\r" ];
    protected static $SP0   = '';
    protected static $SP1   = ' ';
    protected static $ERR1  = 'Empty argument %s';
    public    static $ERRx  = 'Invalid argument(s) %s';

    /**
     * @var string
     */
    protected $eol = null;

    /**
     * @var string
     */
    protected $indent = null;

    /**
     * @var string
     */
    protected $baseIndent = null;

    /**
     * BaseClass descendents constructor
     *
     * @param string $eol
     * @param string $indent
     * @param string $baseIndent
     */
    public function __construct( $eol = null, $indent = null, $baseIndent = null ) {
        if( null === $this->eol ) {
            $this->eol = self::$DEFAULTEOL;
        }
        if( null !== $eol ) {
            $this->setEol( $eol );
        }
        if( null === $this->indent ) {
            $this->indent = self::$DEFAULTINDENT;
        }
        if( null !== $indent ) {
            $this->setIndent( $indent );
        }
        if( null === $this->baseIndent ) {
            $this->baseIndent = self::$DEFAULTBASEINDENT;
        }
        if( null !== $baseIndent ) {
            $this->setBaseIndent( $baseIndent );
        }
        if( empty( self::$TARGETVERSION )) {
            self::$TARGETVERSION = self::$DEFAULTVERSION;
        }
    }

    /**
     * BaseClass descendents factory method
     *
     * @param string|BaseA $base
     * @param string       $indent
     * @param string       $baseIndent
     * @return static
     */
    public static function init( $base = null, $indent = null, $baseIndent = null ) {
        if( $base instanceof BaseA ) {
            return new static(
                $base->getEol(),
                $base->getIndent(),
                $base->getBaseIndent()
            );
        }
        return new static( $base, $indent, $baseIndent );
    }

    /**
     * Return code as array (with NO eol at line endings)
     *
     * @return array
     */
    abstract public function toArray();

    /**
     * Return code as string (with eol at line endings)
     *
     * @return string
     */
    public function toString() {
        return implode( $this->eol, $this->toArray()) . $this->eol;
    }

    /**
     * @return array|string|null
     */
    public function getEol() {
        return $this->eol;
    }

    /**
     * @param string $eol
     * @return static
     * @throws InvalidArgumentException
     */
    public function setEol( $eol ) {
        $eol = Util::nullByteClean( $eol );
        if( empty( $eol )) {
            $this->eol =  self::$DEFAULTEOL;
            return $this;
        }
        if( ! in_array( $eol, self::$CRLFs )) {
            $ords = [];
            for( $pos = 0; $pos < strlen( $eol ); $pos++ ) {
                $ords[] = ord( substr( $eol, $pos, 1 ));
            }
            throw new InvalidArgumentException( sprintf( self::$ERRx, implode( self::$SP1, $ords )));
        }
        $this->eol = $eol;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIndent() {
        return $this->indent;
    }

    /**
     * @param string $indent
     * @return static
     */
    public function setIndent( $indent = null ) {
        $this->indent = ( null === $indent ) ? self::$SP0 : Util::nullByteClean( $indent );
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBaseIndent() {
        return $this->baseIndent;
    }

    /**
     * @param string $indent
     * @return static
     */
    public function setBaseIndent( $indent = null ) {
        $this->baseIndent = ( null === $indent ) ? self::$SP0 : Util::nullByteClean( $indent );
        return $this;
    }

    /**
     * @return string
     */
    public function showIndents() {
        return get_called_class() . ' baseIndent ->' . $this->baseIndent . '<- indent ->' . $this->indent . '<-'; // test ###
    }

}
