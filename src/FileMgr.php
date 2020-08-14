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
     * @param string $eol
     * @param string $indent
     * @param string $baseIndent
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
    public function toArray()
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
        return Util::nullByteClean( $code );
    }

    /**
     * @return DocBlockMgr[]
     */
    public function getDocBlock()
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
    public function setDocBlock( $docBlocks )
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
     * @param string|array $description
     * @return static
     */
    public function setInfo( $summary, $description = null )
    {
        $this->docBlock[0]->setInfo( $summary, $description );
        return $this;
    }

    /**
     * @return bool
     */
    public function isFileBodySet() {
        return ( ! empty( $this->fileBody ));
    }

    /**
     * @param ClassMgr $fileBody
     * @return FileMgr
     */
    public function setFileBody( ClassMgr $fileBody )
    {
        $this->fileBody = $fileBody;
        return $this;
    }
}