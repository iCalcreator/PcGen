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

use function count;
use function is_array;
use function reset;
use function sprintf;
use function strcasecmp;
use function strlen;

/**
 * Class PcGen
 *
 * @package Kigkonsult\PcGen
 * @link https://phpdoc.org
 * @todo accept annotations in the same way as tags
 */
final class DocBlockMgr extends BaseA
{
    /**
     * Row template
     *
     * @var string
     */
    private static $TMPLROW1 = '%s * %s';

    /**
     * @var array
     */
    private static $TAGNAMELIST = [
        self::API_T,
        self::AUTHOR_T,
        self::CATEGORY_T,
        self::COPYRIGHT_T,
        self::DEPRECATED_T,
        self::EXAMPLE_T,
        self::FILESOURCE_T,
        self::GLOBAL_T,
        self::IGNORE_T,
        self::INHERITDOC_T,
        self::INTERNAL_T,
        self::LICENCE_T,
        self::LINK_T,
        self::METHOD_T,
        self::PACKAGE_T,
        self::PARAM_T,
        self::PROPERTY_T,
        self::PROPERTY_READ_T,
        self::PROPERTY_WRITE_T,
        self::RETURN_T,
        self::SEE_T,
        self::SINCE_T,
        self::SOURCE_T,
        self::SUBPACKAGE_T,
        self::THROWS_T,
        self::TODO_T,
        self::USES_T,
        self::USED_BY_T,
        self::VAR_T,
        self::VERSION_T,
    ];

    public static $VARTYPELIST = [
        self::ARRAY_T,
        self::BOOL_T,
        self::CALLABLE_T,
        self::FLOAT_T,
        self::INT_T,
        self::RESOURCE_T,
        self::STRING_T,
        self::BOOLARRAY_T,
        self::CALLABLEARRAY_T,
        self::FLOATARRAY_T,
        self::INTARRAY_T,
        self::RESOURCEARRAY_T,
        self::STRINGARRAY_T,
    ];

    /**
     * @var string
     */
    private $summary = null;

    /**
     * @var string[]|string[][]
     */
    private $description = [];

    /**
     * Contains tags, *( tagName => *( tagDirectivs ))
     *
     * @var array string[][]
     */
    private $tags = [];

    /**
     * Class factory method, opt set tag
     *
     * @param null|string $tagName
     * @param null|string|array $tagType
     * @param null|string $tagText
     * @param null|string $tagComment
     * @param null|string $tagExt
     * @return static
     */
    public static function factory(
        $tagName = null,
        $tagType = null,
        $tagText = null,
        $tagComment = null,
        $tagExt = null
    ) : self
    {
        $self = new self();
        if( ! empty( $tagName )) {
            $self->setTag( $tagName, $tagType, $tagText, $tagComment, $tagExt );
        }
        return $self;
    }

    /**
     * Return code as array (with NO eol at line endings)
     *
     * @return array
     */
    public function toArray() : array
    {
        $code = $this->initCode( $addEmptyRow );
        if( ! empty( $this->summary )) {
            $this->summaryToArray( $code, $addEmptyRow );
        }
        if( ! empty( $this->description )) {
            $this->descriptionToArray( $code, $addEmptyRow );
        }
        if( ! empty( $this->tags )) {
            $this->tagsToArray( $code, $addEmptyRow );
        }
        return $this->exitCode( $code );
    }

    /**
     * Init code
     *
     * @param bool $addEmptyRow
     * @return array
     */
    private function initCode( & $addEmptyRow ) : array
    {
        static $START = '%s/**';
        $addEmptyRow  = false;
        return [ self::SP0, sprintf( $START, $this->baseIndent ) ];
    }

    /**
     * End up code code
     *
     * @param array $code
     * @return array
     */
    private function exitCode( array $code ) : array
    {
        static $END = '%s */';
        $code[]     = sprintf( $END, $this->baseIndent );
        return Util::nullByteCleanArray( $code );
    }

    /**
     * Add summary to code
     *
     * @param array $code
     * @param bool  $addEmptyRow
     */
    private function summaryToArray( array & $code, bool & $addEmptyRow )
    {
        $code[]      = sprintf( self::$TMPLROW1, $this->baseIndent, $this->summary );
        $addEmptyRow = true;
    }

    /**
     * Add description to code,
     *
     * Empty first or last row in (/)description part) rows are skipped.
     * If not first in the docBlock, an empty leading row is inserted.
     * Then, all but first, will have an empty leading row.
     *
     * @param array $code
     * @param bool  $addEmptyRow
     */
    private function descriptionToArray( array & $code, & $addEmptyRow = false )
    {
        foreach( $this->description as $description ) {
            if( $addEmptyRow ) {
                $this->addEmptyRow( $code );
            }
            if( ! is_array( $description )) {
                $description = [ $description ];
            }
            $lastIx = count( $description ) - 1;
            foreach( $description as $x => $descrPart ) {
                if(( empty( $x ) || ( $x == $lastIx )) && empty( $descrPart )) {
                    continue;
                }
                if( ! is_array( $descrPart )) {
                    $descrPart = [ $descrPart ];
                }
                foreach( $descrPart as $descrPart2 ) {
                    $code[] = sprintf( self::$TMPLROW1, $this->baseIndent, $descrPart2 );
                }
            } // end foreach
            $addEmptyRow = true;
        }  // end foreach
    }

    /**
     * Add tags to code
     *
     * If not first in the docBlock, an empty leading row is inserted
     * TagNames are space-padded to the same length.
     *
     * @param array $code
     * @param bool  $addEmptyRow
     */
    private function tagsToArray( array & $code, $addEmptyRow = false )
    {
        static $TMPLROW = '%s * @%s %s %s %s %s';
        if( $addEmptyRow ) {
            $this->addEmptyRow( $code );
        }
        $namePadLen = $paramTypePadLen = 0;
        foreach( $this->tags as $tagName => $tagInfoArr ) {
            if( $namePadLen < strlen( $tagName )) {
                $namePadLen = strlen( $tagName );
            }
            if( self::PARAM_T == $tagName ) {
                foreach( $tagInfoArr as $data ) {
                    if( $paramTypePadLen < strlen( $data[0] )) {
                        $paramTypePadLen = strlen( $data[0] );
                    }
                }
            } // end if
        } // end foreach
        foreach( $this->tags as $tagName => $tagInfoArr ) {
            $theTagName = str_pad( $tagName, $namePadLen );
            foreach( $tagInfoArr as $data ) {
                if( self::PARAM_T == $tagName ) {
                    $data[0] = str_pad( $data[0], $paramTypePadLen );
                }
                $code[] = rtrim(
                    sprintf( $TMPLROW, $this->baseIndent, $theTagName, $data[0], $data[1], $data[2], $data[3] )
                );
            } // end foreach
        } // end foreach
    }

    /**
     * Add empty row to code
     *
     * @param array $code
     */
    private function addEmptyRow( array & $code )
    {
        static $EMPTY = '%s *';
        $code[] = sprintf( $EMPTY, $this->baseIndent );
    }

    /**
     * @return bool
     */
    public function isSummarySet() : bool
    {
        return ( null !== $this->summary );

    }

    /**
     * Set (header) short description, overwrite if exists
     *
     * @param null|string $summary
     * @return DocBlockMgr
     */
    public function setSummary( $summary ) : self
    {
        if( ! empty( $summary )) {
            $this->summary = $summary;
        }
        return $this;
    }

    /**
     * Set a long description, each one will have an empty leading row
     *
     * @param string|array $description
     * @return DocBlockMgr
     */
    public function setDescription( $description ) : self
    {
        if( ! empty( $description )) {
            $this->description[] = $description;
        }
        return $this;
    }

    /**
     * Set summary AND (one) description
     *
     * Convenient shortcut, combining setSummary and setDescription methods
     *
     * @param string $summary
     * @param string|array $description
     * @return DocBlockMgr
     */
    public function setInfo( string $summary, $description = null ) : self
    {
        $this->setSummary( $summary );
        if( null !== $description ) {
            $this->setDescription( $description );
        }
        return $this;
    }

    /**
     * @param string $tagName
     * @return bool
     */
    public function isTagSet( string $tagName ) : bool
    {
        return array_key_exists( $tagName, $this->tags );
    }
    /**
     * Set tag
     *
     * Args are modelled after the 'param' tag usage
     *
     * @param string $tagName
     * @param null|string|array $tagType
     * @param null|string $tagText
     * @param null|string $tagComment
     * @param null|string $tagExt
     * @return DocBlockMgr
     * @throws InvalidArgumentException
     */
    public function setTag(
        string $tagName,
        $tagType = null,
        $tagText = null,
        $tagComment = null,
        $tagExt = null
    ) : self
    {
        static $PIPE  = '|';
        $tagName = self::assertTagName( $tagName );
        if( self::PARAM_T == $tagName ) {
            $tagType = self::assertTagType( $tagType );
            if( is_array( $tagType )) {
                $tagType = implode( $PIPE, $tagType );
            }
            $tagText = Util::setVarPrefix( Assert::assertPhpVar( $tagText ));
        }
        if( ! isset( $this->tags[$tagName] )) {
            $this->tags[$tagName] = [];
        }
        $this->tags[$tagName][] = [ $tagType, $tagText, $tagComment, $tagExt ];
        return $this;
    }

    /**
     * Return bool true if tag variable is accepted
     *
     * @param string $tagName
     * @return bool
     */
    public static function isValidTagName( string $tagName ) : bool
    {
        foreach( self::$TAGNAMELIST as $validTagname ) {
            if( 0 === strcasecmp( $tagName, $validTagname )) {
                return true;
            }
        }
        return false;
    }

    /**
     * Assert accepted tag variable
     *
     * @param string $tagName
     * @return string
     * @throws InvalidArgumentException
     */
    private static function assertTagName( string $tagName ) : string
    {
        static $ERR1 = 'Invalid tag %s';
        foreach( self::$TAGNAMELIST as $validTagName ) {
            if( 0 === strcasecmp( $tagName, $validTagName )) {
                return $validTagName;
            }
        }
        throw new InvalidArgumentException( sprintf( $ERR1, $tagName ));
    }

    /**
     * Assert accepted tag param varType(s), if found. If not, accept anyway...
     *
     * @param string|array $tagType
     * @return string|string[]
     * @todo move to assert::varType? const/fqcn, also in VarDto
     */
    private static function assertTagType( $tagType )
    {
        if( ! is_array( $tagType )) {
            $tagType = [ $tagType ];
        }
        foreach( $tagType as & $theTagType ) {
            foreach( self::$VARTYPELIST as $validTagType ) {
                switch( true ) {
                    case ( 0 == strcasecmp( self::BOOLEAN_T , $theTagType )) :
                        $theTagType = self::BOOL_T;
                        break 2;
                    case ( 0 == strcasecmp( self::BOOLEANARRAY_T , $theTagType )) :
                        $theTagType = self::BOOLARRAY_T;
                        break 2;
                    case ( 0 === strcasecmp( $theTagType, $validTagType )) :
                        $theTagType = $validTagType;
                        break 2;
                } // end switch
            } // end foreach
        } // end foreach
        return ( 1 == count( $tagType )) ? reset( $tagType ) : $tagType;
    }
}
