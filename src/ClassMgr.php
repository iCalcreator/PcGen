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
use Kigkonsult\PcGen\Dto\UseSubjectDto;
use Kigkonsult\PcGen\Dto\VarDto;
use Kigkonsult\PcGen\Traits\NameTrait;
use RuntimeException;

use function array_merge;
use function implode;
use function in_array;
use function is_array;
use function ksort;
use function sprintf;
use function ucfirst;
use function var_export;

/**
 * Class ClassMgr
 *
 * Manages class, interface and trait
 *
 * @package Kigkonsult\PcGen
 */
final class ClassMgr extends BaseB
{
    /**
     * Class use-clause subject types, CLASS_ default
     */
    const CLASS_ = 'class';
    const FUNC_  = 'function';
//  const CONST_ = self::CONST_;

    use NameTrait;

    /**
     * @var string  targetTypes
     */
    private static $class     = 'class';
    private static $interface = 'interface';
    private static $trait     = 'trait';

    /*
     * One of class / interface / trait
     *
     * @var string
     */
    private $targetType = null;

    /*
     * Class namespace
     *
     * @var string
     */
    private $namespace = null;

    /**
     * Class use / imports
     *
     * @var array
     */
    private $uses = null;

    /**
     * The class docBlock
     *
     * @var DocBlockMgr
     */
    private $docBlock = null;

    /**
     * True if class is abstract
     *
     * @var bool
     */
    private $abstract = false;

    /**
     * Class extends
     *
     * @var string
     */
    private $extend = null;

    /**
     * Class implements
     *
     * @var array
     */
    private $implements = null;

    /**
     * @var bool
     */
    private $construct = false;

    /**
     * @var bool
     */
    private $factory = false;

    /**
     * @var PropertyMgr[]
     */
    private $properties = [];

    /**
     * ClassMgr constructor
     *
     * @param null|string $eol
     * @param null|string $indent
     * @param null|string $baseIndent
     */
    public function __construct( $eol = null, $indent = null, $baseIndent = null )
    {
        parent::__construct( $eol, $indent, $baseIndent );
        $this->targetType = self::$class;
        $this->docBlock   = DocBlockMgr::init( $this );
    }

    /**
     * Return code as array (with NO eol at line endings)
     *
     * @return array
     * @throws RuntimeException
     */
    public function toArray() : array
    {
        static $NAME = 'name';
        if( ! $this->isNameSet()) {
            throw new RuntimeException( sprintf( self::$ERR1, $NAME ));
        }
        $code = array_merge(
            $this->initCode(),
            [ self::$CODEBLOCKSTART ],
            $this->bodyCode(),
            [ self::$CODEBLOCKEND ]
        );
        return Util::nullByteCleanArray( $code );
    }

    /**
     * @return array
     */
    private function initCode() : array
    {
        $TMPL1 = 'namespace %s;';
        $TMPL4 = 'abstract ';
        $TMPL5 = ' extends ';
        $TMPL6 = ' implements ';
        $this->docBlock->setBaseIndent();
        if( ! $this->docBlock->isSummarySet()) {
            $this->docBlock->setSummary(
                ucfirst( $this->getTargetType()) . self::SP1 . $this->getName()
            );
        }
        $this->setBaseIndent( self::$DEFAULTINDENT );
        $code = [];
        if( ! empty( $this->namespace )) {
            $code[] = sprintf( $TMPL1, $this->namespace );
            if( ! $this->docBlock->isTagSet( self::PACKAGE_T )) {
                $this->docBlock->setTag( self::PACKAGE_T, $this->namespace );
            }
        }
        if( $this->hasOneArrayProperty()) {
            ClassMethodFactory::setUpIteratorForClass( $this );
        }
        if( ! empty( $this->uses )) {
            $code[] = self::SP0;
            ksort( $this->uses, SORT_FLAG_CASE | SORT_STRING );
            foreach( $this->uses as $useSubject ) {
                $code[] = $useSubject->toString();
            }
        }
        $code   = array_merge( $code, $this->docBlock->toArray());
        $row    = $this->isAbstract() ? $TMPL4 : self::SP0;
        $code[] = $row . $this->getTargetType() . self::SP1 . $this->getName();
        if( ! empty( $this->extend )) {
            $code[] = $this->indent . $TMPL5 . $this->extend;
        }
        if( ! empty( $this->implements )) {
            $code[] = $this->indent . $TMPL6 . implode( self::$COMMA . self::SP1, $this->implements );
        }
        return $code;
    }

    /**
     * @return array
     */
    private function bodyCode() : array
    {
        $hasProperties = ! empty( $this->getPropertyCount());
        $body = array_merge(
            ( $hasProperties     ? $this->defineProperties() : [] ),
            ( $this->construct   ? ClassMethodFactory::renderConstructorMethod( $this ) : [] ),
            ( $this->factory     ? ClassMethodFactory::renderFactoryMethod( $this ) : [] ),
            ( $this->isBodySet() ? array_merge( [ self::SP0 ], $this->getBody()) : [] ),
            ( $hasProperties     ? $this->produceMethodsForProperties() : [] )
        );
        $body = Util::trimLeading( $body );
        $body = Util::trimTrailing( $body );
        return empty( $body ) ? [] : $body;
    }

    /**
     * @return array
     */
    private function defineProperties() : array
    {
        $code  = [];
        $props = [ [], [] ];
        foreach( $this->getPropertyIndex() as $p1Ix ) {
            $p2Ix = $this->properties[ $p1Ix ]->isConst() ? 0 : 1;
            $props[$p2Ix][] = $p1Ix;
        }
        for( $p2Ix = 0; $p2Ix < 2; $p2Ix++ ) {
            foreach( $props[$p2Ix] as $p1Ix ) {
                $propertyMgr = $this->properties[ $p1Ix ]
                    ->setBaseIndent( $this->getBaseIndent() )
                    ->setIndent( $this->getIndent() );
                $docBlockMgr = DocBlockMgr::init( $this )
                    ->setSummary( $propertyMgr->getVarDto()->getSummary() )
                    ->setDescription( $propertyMgr->getVarDto()->getDescription() );
                $varType     = $propertyMgr->getVarDto()->getParamTagVarType();
                if( $propertyMgr->isConst() ) {
                    $docBlockMgr->setDescription( self::CONST_ . self::SP1 . $varType );
                }
                else {
                    $docBlockMgr->setTag( self::VAR_T, $varType );
                }
                $code = array_merge( $code,
                    $docBlockMgr->toArray(),
                    $propertyMgr->toArray()
                );
            } // end foreach
        } // end for
        return $code;
    }

    /**
     * @return array
     */
    private function produceMethodsForProperties() : array
    {
        $code    = [];
        $oneOnly = $this->hasOneArrayProperty( $propIx );
        foreach( $this->getPropertyIndex() as $pIx ) {
            $property = $this->properties[$pIx]
                ->setBaseIndent( $this->getBaseIndent())
                ->setIndent( $this->getIndent());
            switch( true ) {
                case ( ! $this->properties[$pIx]->isMakeGetter()) :
                    break;
                case ( $oneOnly && ( $pIx == $propIx )) :
                    ClassMethodFactory::renderIteratorGetterMethods( $property, $code );
                    break;
                default :
                    ClassMethodFactory::renderGetterMethod( $property, $code );
                    ClassMethodFactory::renderIsPropertySetMethod( $property, $code );
                    ClassMethodFactory::renderPropertyCountMethod( $property, $code );
                    break;
            } // end switch
            if( $this->properties[$pIx]->isMakeSetter()) {
                if( $property->getVarDto()->isTypedArray()) {
                    ClassMethodFactory::renderAppendArrayMethod( $property, $code );
                }
                ClassMethodFactory::renderSetterMethod( $property, $code );
            }
        } // end foreach
        return $code;
    }


    /**
     * @return null|string
     */
    private function getTargetType()
    {
        return $this->targetType;
    }

    /**
     * @return ClassMgr
     */
    public function setClass() : self
    {
        $this->targetType = self::$class;
        return $this;
    }
    /**
     * @return ClassMgr
     */
    public function setInterface() : self
    {
        $this->targetType = self::$interface;
        return $this;
    }
    /**
     * @return ClassMgr
     */
    public function setTrait() : self
    {
        $this->targetType = self::$trait;
        return $this;
    }

    /**
     * @return bool
     */
    public function isNamespaceSet() : bool
    {
        return ( null !== $this->namespace );
    }

    /**
     * @param string $namespace
     * @return ClassMgr
     * @throws InvalidArgumentException
     */
    public function setNamespace( string $namespace ) : self
    {
        Assert::assertFqcn( $namespace );
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * Add class use, fqcn [, alias [, type ]]
     *
     * @param string|UseSubjectDto $fqcn    a fqcn-Class/-constant/-function
     * @param null|string          $alias   opt
     * @param null|string          $type    one of ClassMgr::CLASS_, ClassMgr::CONST_, ClassMgr::FUNC_
     * @return ClassMgr
     * @throws InvalidArgumentException
     */
    public function addUse( $fqcn, $alias = null, $type = null ) : self
    {
        $useSubject = ( $fqcn instanceof UseSubjectDto )
            ? $fqcn
            : UseSubjectDto::factory( $fqcn, $alias, $type );
        $this->uses[$useSubject->getSortKey()] = $useSubject;
        return $this;
    }

    /**
     * Set array of class use set , each array element : array( fqcn [, alias [, type ] ] )
     *
     * For array item content, se above
     *
     * @param array $uses
     * @return ClassMgr
     * @throws InvalidArgumentException
     */
    public function setUses( array $uses ) : self
    {
        static $USE = 'use';
        if( empty( $uses )) {
            throw new InvalidArgumentException( sprintf( self::$ERR1, $USE ));
        }
        $this->uses = [];
        foreach( $uses as $useSet ) {
            if( empty( $useSet )) {
                throw new InvalidArgumentException( sprintf( self::$ERR1, $USE ));
            }
            if( $useSet[0] instanceof UseSubjectDto ) {
                $this->addUse( $useSet[0] );
                continue;
            }
            $this->addUse(
                UseSubjectDto::factory(
                    $useSet[0],
                    ( $useSet[1] ?? null ),
                    ( $useSet[2] ?? null )
                )
            );
        } // end foreach
        return $this;
    }

    /**
     * @return null|DocBlockMgr
     */
    public function getDocBlock()
    {
        return $this->docBlock;
    }

    /**
     * @param DocBlockMgr $docBlock
     * @return ClassMgr
     */
    public function setDocBlock( DocBlockMgr $docBlock ) : self
    {
        $this->docBlock = $docBlock;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAbstract() : bool
    {
        return $this->abstract;
    }

    /**
     * Set class as abstract
     *
     * @param bool $abstract
     * @return ClassMgr
     */
    public function setAbstract( bool $abstract ) : self
    {
        $this->abstract = $abstract;
        return $this;
    }

    /**
     * @return null|string
     * @throws InvalidArgumentException
     */
    public function getExtend()
    {
        return $this->extend;
    }

    /**
     * @return bool
     */
    public function isExtendsSet() : bool
    {
        return ( null !== $this->extend );
    }

    /**
     * @param string $extend
     * @return ClassMgr
     * @throws InvalidArgumentException
     */
    public function setExtend( string $extend ) : self
    {
        Assert::assertFqcn( $extend );
        $this->extend = $extend;
        return $this;
    }

    /**
     * @param string $implement
     * @return ClassMgr
     * @throws InvalidArgumentException
     */
    public function addImplement( string $implement ) : self
    {
        if( is_array( $this->implements ) &&
            in_array( $implement, $this->implements )) {
            return $this;
        }
        Assert::assertFqcn( $implement );
        if( ! is_array( $this->implements )) {
            $this->implements = [];
        }
        $this->implements[] = $implement;
        return $this;
    }

    /**
     * Set class interface implements
     *
     * @param array $implements
     * @return ClassMgr
     * @throws InvalidArgumentException
     */
    public function setImplements( array $implements ) : self
    {
        static $IMPLEMENTS = 'implements';
        if( empty( $implements )) {
            throw new InvalidArgumentException( sprintf( self::$ERR1, $IMPLEMENTS ));
        }
        $this->implements = [];
        foreach( $implements as $implement ) {
            if( empty( $implement )) {
                throw new InvalidArgumentException( sprintf( self::$ERR1, $IMPLEMENTS ));
            }
            $this->addImplement( $implement );
        }
        return $this;
    }

    /**
     * @param bool $construct
     * @return ClassMgr
     */
    public function setConstruct( $construct = true ) : self
    {
        $this->construct = $construct ?? true;
        return $this;
    }

    /**
     * @param bool $factory
     * @return ClassMgr
     */
    public function setFactory( $factory = true ) : self
    {
        $this->factory = $factory ?? true;
        return $this;
    }

    /**
     * Add property
     *
     *     PropertyMgr
     *     VariableMgr, getter, setter, argInFactory
     *     VarDto, getter, setter, argInFactory
     *     ( array, to conform to setProperty)
     *     variable, varType, default, summary, description, getter, setter, argInFactory
     *
     * @param string|array|PropertyMgr|VariableMgr|VarDto $name
     * @param null|string  $type
     * @param null|mixed   $default
     * @param null|string  $summary
     * @param null|string  $description
     * @param null|bool    $getter
     * @param null|bool    $setter
     * @param null|bool    $argInFactory
     * @return ClassMgr
     * @throws InvalidArgumentException
     */
    public function addProperty(
        $name,
        $type = null,
        $default = null,
        $summary = null,
        $description = null,
        $getter = false,
        $setter = false,
        $argInFactory = false
    ) : self
    {
        if( is_array( $name )) {
            $name = array_values( $name );
            list( $name, $type, $default, $summary, $description, $getter, $setter, $argInFactory ) =
                array_pad( $name, 8, null );
        }
        switch( true ) {
            case ( $name instanceof PropertyMgr ) :
                $property = $name;
                $property->rig( $this );
                break;
            case ( $name instanceof VariableMgr ) :
                $property = PropertyMgr::init( $this )
                    ->cloneFromParent( $name )
                    ->setMakeGetter( $type ?? false )
                    ->setMakeSetter( $default ?? false )
                    ->setArgInFactory( $summary ?? false );
                break;
            case ( $name instanceof VarDto ) :
                $property = PropertyMgr::init( $this )
                    ->setVarDto( $name )
                    ->setMakeGetter( $type ?? false )
                    ->setMakeSetter( $default ?? false )
                    ->setArgInFactory( $summary ?? false );
                break;
            case is_string( $name ) :
                $property = PropertyMgr::init( $this )
                    ->setVarDto( VarDto::factory( $name, $type, $default, $summary, $description ))
                    ->setMakeGetter( $getter ?? false )
                    ->setMakeSetter( $setter ?? false )
                    ->setArgInFactory( $argInFactory ?? false );
                break;
            default :
                throw new InvalidArgumentException(
                    sprintf( self::$ERRx, var_export( $name, true ))
                );

        } // end switch
        $this->properties[] = $property;
        return $this;
    }

    /**
     * @param int $pIx
     * @return PropertyMgr
     */
    public function getProperty( int $pIx ) : PropertyMgr
    {
        return $this->properties[$pIx];
    }

    /**
     * @return int
     */
    public function getPropertyCount() : int
    {
        return count( $this->properties );
    }

    /**
     * @return array
     */
    public function getPropertyIndex() : array
    {
        return array_keys( $this->properties );
    }

    /**
     * @param int $propIx
     * @return bool
     */
    private function hasOneArrayProperty( & $propIx = null ) : bool
    {
        $cntProps = 0;
        $arrayPropIx = null;
        foreach( $this->getPropertyIndex() as $pIx ) {
            switch( true ) {
                case ( $this->properties[$pIx]->isConst() ||
                    $this->properties[$pIx]->isStatic()) :
                    continue 2;
                case ( ClassMethodFactory::$POSITION ==
                    $this->properties[$pIx]->getVarDto()->getName()) :
                    continue 2;
                case ( null !== $arrayPropIx ) :
                    break;
                case ! $this->properties[$pIx]->isMakeGetter() :
                    break;
                case $this->properties[$pIx]->getVarDto()->isTypedArray() :
                    $arrayPropIx = $pIx;
                    break;
            } // end switch
            $cntProps += 1;
        } // end foreach
        if( empty( $cntProps ) || ( 1 < $cntProps ) || ( null === $arrayPropIx )) {
            return false;
        }
        $propIx = $arrayPropIx;
        return true;
    }

    /**
     * Set array of properties
     *
     * Input array item :
     *     PropertyMgr
     *     VariableMgr
     *     VarDto
     *     variable
     *     array( VariableMgr, getter, setter, argInFactory )
     *     array( VarDto, getter, setter, argInFactory )
     *     array( variable, varType, default, summary, description, getter, setter, argInFactory )
     *
     * @param mixed $properties
     * @return ClassMgr
     * @throws InvalidArgumentException
     */
    public function setProperties( $properties ) : self
    {
        static $ERRx1 = 'Invalid property (#%d) %s';
        $this->properties = [];
        if( ! is_array( $properties )) {
            $properties = [ $properties ];
        }
        foreach( $properties as $pIx => $property ) {
            switch( true ) {
                case empty( $property ) :
                    throw new InvalidArgumentException( sprintf( $ERRx1, $pIx, var_export( $property, true )));
                case ( $property instanceof PropertyMgr ) :
                    $this->addProperty( $property );
                    break;
                case ( $property instanceof VariableMgr ) :
                    $this->addProperty(
                        PropertyMgr::init( $this )
                            ->cloneFromParent( $property )
                            ->setMakeGetter( false )
                            ->setMakeSetter( false )
                            ->setArgInFactory( false )
                    );
                    break;
                case (( $property instanceof VarDto ) || is_string( $property )) :
                    $this->addProperty(
                        PropertyMgr::factory( $property )
                            ->setMakeGetter( false )
                            ->setMakeSetter( false )
                            ->setArgInFactory( false )
                    );
                    break;
                case ( ! is_array( $property )) :
                    throw new InvalidArgumentException(
                        sprintf( $ERRx1, $pIx, var_export( $property, true ))
                    );
                case ( $property[0] instanceof VariableMgr ) :
                    $this->addProperty(
                        PropertyMgr::init( $this )
                            ->cloneFromParent( $property[0] )
                            ->setMakeGetter( $property[1] ?? false )
                            ->setMakeSetter( $property[2] ?? false )
                            ->setArgInFactory( $property[3] ?? false )
                    );
                    break;
                case ( $property[0] instanceof VarDto ) :
                    $this->addProperty(
                        PropertyMgr::factory( $property[0] )
                            ->setMakeGetter( $property[1] ?? false )
                            ->setMakeSetter( $property[2] ?? false )
                            ->setArgInFactory( $property[3] ?? false )
                    );
                    break;
                default :
                    $this->addProperty(
                        PropertyMgr::factory(
                            ( $property[0] ?? null ),  // variable,
                            ( $property[1] ?? null ),  // varType,
                            ( $property[2] ?? null ),  // default
                            ( $property[3] ?? null ),  // summary
                            ( $property[4] ?? null )   // description
                        )
                            ->setMakeGetter( $property[5] ?? false )
                            ->setMakeSetter( $property[6] ?? false )
                            ->setArgInFactory( $property[7] ?? false )
                    );
                    break;
            } // end switch
        } // end foreach
        return $this;
    }
}
