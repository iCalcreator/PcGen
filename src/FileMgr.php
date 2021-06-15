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
use RuntimeException;

use function array_keys;
use function array_merge;
use function get_class;
use function is_array;
use function is_object;
use function sprintf;


/**
 * Class FileMgr
 *
 * Manages file docBlock and (class/interface/trait) body
 * Allows other body using FileMgr::setBody()
 *
 * @package Kigkonsult\PcGen
 */
final class FileMgr extends BaseB
{
    /**
     * The file docBlocks
     *
     * @var DocBlockMgr[]
     */
    private $docBlock = [];

    /**
     * The file class/interface/trait body
     *
     * @var ClassMgr
     */
    private $fileBody = null;

    /**
     * FileMgr constructor
     *
     * @param null|string $eol
     * @param null|string $indent
     * @param null|string $baseIndent
     */
    public function __construct( $eol = null, $indent = null, $baseIndent = null )
    {
        parent::__construct( $eol, $indent, $baseIndent );
        $this->docBlock[0] = DocBlockMgr::init( $this );
    }

    /**
     * @inheritDoc
     * @throws RuntimeException
     */
    public function toArray() : array
    {
        $code = [];
        foreach( array_keys( $this->docBlock ) as $dbIx ) {
            $this->docBlock[$dbIx]->setBaseIndent();
            $code = array_merge(
                $code,
                Util::trimLeading( $this->docBlock[$dbIx]->toArray())
            );
        } // end foreach
        if( $this->isFileBodySet()) {
            $this->fileBody->rig( $this );
            $code = array_merge( $code, $this->fileBody->toArray());
        }
        if( $this->isBodySet()) {
            $code = array_merge( $code, $this->getBody());
        }
        return Util::nullByteCleanArray( $code );
    }

    /**
     * @return DocBlockMgr[]
     */
    public function getDocBlock() : array
    {
        return $this->docBlock;
    }

    /**
     * Set one or more docBlocks
     *
     * @param DocBlockMgr|DocBlockMgr[] $docBlocks
     * @return FileMgr
     * @throws InvalidArgumentException
     */
    public function setDocBlock( $docBlocks ) : self
    {
        $this->docBlock = [];
        if( ! is_array( $docBlocks )) {
            $docBlocks = [ $docBlocks ];
        }
        foreach( array_keys( $docBlocks ) as $dbIx ) {
            if( $docBlocks[$dbIx] instanceof DocBlockMgr ) {
                continue;
            }
            throw new InvalidArgumentException(
                sprintf(
                    self::$ERRx,
                    (string) ( is_object( $docBlocks[$dbIx] )
                        ? get_class( $docBlocks[$dbIx] )
                        : $docBlocks[$dbIx] )
                )
            );
        } // end foreach
        $this->docBlock = $docBlocks;
        return $this;
    }

    /**
     * Set file docBlock summary AND (one) description in FIRST docBlock
     *
     * @param string $summary
     * @param null|string|array $description
     * @return static
     */
    public function setInfo( string $summary, $description = null ) : self
    {
        $this->docBlock[0]->setInfo( $summary, $description );
        return $this;
    }

    /**
     * @return bool
     */
    public function isFileBodySet() : bool
    {
        return ( ! empty( $this->fileBody ));
    }

    /**
     * @param ClassMgr $fileBody
     * @return FileMgr
     */
    public function setFileBody( ClassMgr $fileBody ) : self
    {
        $this->fileBody = $fileBody;
        return $this;
    }
}
