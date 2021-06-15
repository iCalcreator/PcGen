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

use function get_called_class;
use function implode;
use function ord;
use function sprintf;
use function substr;

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
     * @param string $eol
     */
    public static function setDefaultEol( $eol )
    {
        self::$DEFAULTEOL = $eol;
    }

    /**
     * @param string $indent
     */
    public static function setDefaultIndent( string $indent )
    {
        self::$DEFAULTINDENT = $indent;
    }

    /**
     * @param string $indent
     */
    public static function setDefaultBaseIndent( string $indent )
    {
        self::$DEFAULTBASEINDENT = $indent;
    }

    /**
     * @var string
     */
    protected static $TARGETVERSION  = null;

    /**
     * @return string
     */
    public static function getTargetPhpVersion() : string
    {
        return ( self::$TARGETVERSION ?: PHP_VERSION );
    }

    /**
     * Set target PHP version, default current
     *
     * @param string $phpVersion
     * @return void
     */
    public static function setTargetPhpVersion( string $phpVersion )
    {
        self::$TARGETVERSION = $phpVersion;
    }

    /**
     * @var string
     */
    public    static $COMMA       = ',';
    protected static $CLOSECLAUSE = ';';
    protected static $CRLFs       = [ "\r\n", "\n\r", "\n", "\r" ];
    protected static $ERR1        = 'Empty argument %s';
    public    static $ERRx        = 'Invalid argument(s) %s';

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
     * @param null|string $eol
     * @param null|string $indent
     * @param null|string $baseIndent
     */
    public function __construct( $eol = null, $indent = null, $baseIndent = null )
    {
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
    }

    /**
     * BaseClass descendents factory method
     *
     * @param null|string|BaseA $base
     * @param null|string       $indent
     * @param null|string       $baseIndent
     * @return static
     */
    public static function init( $base = null, $indent = null, $baseIndent = null ) : self
    {
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
    abstract public function toArray() : array;

    /**
     * Return code as string (with eol at line endings)
     *
     * @return string
     */
    public function toString() : string
    {
        return implode( $this->eol, $this->toArray()) . $this->eol;
    }

    /**
     * @return string|null
     */
    public function getEol()
    {
        return $this->eol;
    }

    /**
     * @param string $eol
     * @return static
     * @throws InvalidArgumentException
     */
    public function setEol(string $eol ) : self
    {
        $eol = Util::nullByteCleanString( $eol );
        if( empty( $eol )) {
            $this->eol =  self::$DEFAULTEOL;
            return $this;
        }
        if( ! in_array( $eol, self::$CRLFs )) {
            $ords = [];
            for( $pos = 0; $pos < strlen( $eol ); $pos++ ) {
                $ords[] = ord( substr( $eol, $pos, 1 ));
            }
            throw new InvalidArgumentException( sprintf( self::$ERRx, implode( self::SP1, $ords )));
        }
        $this->eol = $eol;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIndent()
    {
        return $this->indent;
    }

    /**
     * @param string $indent
     * @return static
     */
    public function setIndent( $indent = null ) : self
    {
        if( null === $indent ) {
            $this->indent = self::SP0;
        }
        else {
            Assert::assertIndent( $indent );
            $this->indent = Util::nullByteCleanString( $indent );
        }
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBaseIndent()
    {
        return $this->baseIndent;
    }

    /**
     * @param string $indent
     * @return static
     */
    public function setBaseIndent( $indent = null ) : self
    {
        if( null === $indent ) {
            $this->baseIndent = self::SP0;
        }
        else {
            Assert::assertIndent( $indent );
            $this->baseIndent = Util::nullByteCleanString( $indent );
        }
        return $this;
    }

    /**
     * @param BaseA $base
     * @return static
     */
    public function rig( BaseA $base ) : self
    {
        $this->setEol( $base->getEol());
        $this->setIndent( $base->getIndent());
        $this->setBaseIndent( $base->getBaseIndent());
        return $this;
    }

    /**
     * @return string
     */
    public function showIndents() : string
    {
        static $FMT = '%s baseIndent =>%s<-  indent =>%s<-';
        return sprintf( $FMT, get_called_class(), $this->baseIndent, $this->indent ); // test ###
    }
}
