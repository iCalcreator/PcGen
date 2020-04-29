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
use Kigkonsult\PcGen\Dto\VarDto;
use RuntimeException;

final class ClassMgr extends BaseB
{

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
     * Class use / imports
     *
     * @var array
     */
    private $uses = null;

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
     * @param null $eol
     * @param null $indent
     */
    public function __construct( $eol = null, $indent = null ) {
        parent::__construct( $eol, $indent );
        $this->targetType = self::$class;
    }

    /**
     * Return code as array (with NO eol at line endings)
     *
     * @return array
     * @throws RuntimeException
     */
    public function toArray() {
        if( ! $this->isNameSet()) {
            throw new RuntimeException( self::$ERR1 );
        }
        $this->checkFixIteratorInterface();
        $code = array_merge(
            $this->initCode(),
            [ self::$CODEBLOCKSTART ],
            $this->bodyCode(),
            [ self::$CODEBLOCKEND ]
        );
        return Util::nullByteClean( $code );
    }

    /**
     * @return void
     */
    private function checkFixIteratorInterface() {
        if( ! $this->hasOneArrayProperty()) {
            return;
        }
        if( $this->isNamespaceSet()) {
            foreach( ClassMethodFactory::$USES as $use ) {
                $this->addUse( $use );
            }
        }
        foreach( ClassMethodFactory::$IMPLEMENTS as $implement ) {
            $this->addImplement( $implement );
        }
        $this->addProperty( ClassMethodFactory::getPositionProperty());
    }

    /**
     * @return array
     */
    private function initCode() {
        $TMPL1 = 'namespace %s;';
        $TMPL2 = 'use %s;';
        $TMPL3 = 'use %s as %s;';
        $TMPL5 = ' extends ';
        $TMPL6 = ' implements ';
        $dbg = DocBlockMgr::init()
            ->setBaseIndent()
            ->setSummary(ucfirst( $this->getTargetType()) . self::$SP1 . $this->getName());
        $this->setBaseIndent( self::$DEFAULTINDENT );
        $code = [];
        if( ! empty( $this->namespace )) {
            $code[] = sprintf( $TMPL1, $this->namespace );
            $dbg->setTag( self::PACKAGE_T, $this->namespace );
        }
        if( ! empty( $this->uses )) {
            $code[] = self::$SP0;
            asort( $this->uses, SORT_FLAG_CASE | SORT_STRING );
            foreach( $this->uses as $alias => $fqcn ) {
                $code[] = ctype_digit((string) $alias ) ? sprintf( $TMPL2, $fqcn ) : sprintf( $TMPL3, $fqcn, $alias );
            }
        }
        $code = array_merge( $code, $dbg->toArray());
        $code[] = $this->getTargetType() . self::$SP1 . $this->getName();
        if( ! empty( $this->extend )) {
            $code[] = $this->indent . $TMPL5 . $this->extend;
        }
        if( ! empty( $this->implements )) {
            $code[] = $this->indent . $TMPL6 . implode( self::$COMMA . self::$SP1, $this->implements );
        }
        return $code;
    }

    /**
     * @return array
     */
    private function bodyCode() {
        $hasProperties = ! empty( $this->properties );
        return array_merge(
            ( $hasProperties     ? $this->defineProperties() : [] ),
            ( $this->construct   ? ClassMethodFactory::renderConstructorMethod( $this->getName()) : [] ),
            ( $this->factory     ? ClassMethodFactory::renderFactoryMethod( $this ) : [] ),
            ( $this->isBodySet() ? array_merge( [ self::$SP0 ], $this->getBody()) : [] ),
            ( $hasProperties     ? $this->produceMethodsForProperties() : [] )
        );
    }

    /**
     * @return array
     */
    private function defineProperties() {
        $code = [];
        foreach( $this->getPropertyIndex() as $pIx ) {
            $propertyMgr = $this->properties[$pIx];
            $dbg = DocBlockMgr::init()
                ->setSummary( $propertyMgr->getVarDto()->getSummary())
                ->setDescription( $propertyMgr->getVarDto()->getDescription());
            $varType = $propertyMgr->getVarDto()->getParamTagVarType();
            if( $propertyMgr->isConst()) {
                $dbg->setDescription( self::CONST_ . self::$SP1 . $varType);
            }
            else {
                $dbg->setTag( self::VAR_T, $varType );
            }
            $code = array_merge( $code,
                $dbg->toArray(),
                $propertyMgr->toArray()
            );
        } // end foreach
        return $code;
    }

    /**
     * @return array
     */
    private function produceMethodsForProperties() {
        $code = [];
        if( $this->hasOneArrayProperty()) {
            ClassMethodFactory::renderIteratorGetterMethods( $this->properties[0]->getVarDto(), $code );
            $this->properties[0]->setMakeGetter( false );
            $this->properties[0]->setMakeSetter( true );
        }
        foreach( $this->getPropertyIndex() as $pIx ) {
            $varDto = $this->properties[$pIx]->getVarDto();
            if( $this->properties[$pIx]->isMakeGetter()) {
                ClassMethodFactory::renderGetterMethod( $varDto, $code );
                ClassMethodFactory::renderIsPropertySetMethod( $varDto, $code );
                ClassMethodFactory::renderPropertyCountMethod( $varDto, $code );
            }
            if( $this->properties[$pIx]->isMakeSetter()) {
                if( $this->properties[$pIx]->getVarDto()->isTypedArray()) {
                    ClassMethodFactory::renderAppendArrayMethod( $varDto, $code );
                }
                ClassMethodFactory::renderSetterMethod( $varDto, $code );
            }
        } // end foreach
        return $code;
    }


    /**
     * @return string
     */
    private function getTargetType() {
        return $this->targetType;
    }

    /**
     * @return ClassMgr
     */
    public function setClass() {
        $this->targetType = self::$class;
        return $this;
    }
    /**
     * @return ClassMgr
     */
    public function setInterface() {
        $this->targetType = self::$interface;
        return $this;
    }
    /**
     * @return ClassMgr
     */
    public function setTrait() {
        $this->targetType = self::$trait;
        return $this;
    }

    /**
     * @return bool
     */
    public function isNamespaceSet() {
        return ( null !== $this->namespace );
    }

    /**
     * @param string $namespace
     * @return ClassMgr
     * @throws InvalidArgumentException
     */
    public function setNamespace( $namespace ) {
        Assert::assertFqcn( $namespace );
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * Add class use, fqcn [, alias ]
     *
     * @param string $fqcn
     * @param string $alias
     * @return ClassMgr
     * @throws InvalidArgumentException
     */
    public function addUse( $fqcn, $alias = null ) {
        if( is_array( $this->uses ) && in_array( $fqcn, $this->uses )) {
            foreach( array_keys( $this->uses, $fqcn ) as $key ) {
                if(( null === $alias ) && is_int( $key )) {
                    return $this; // duplicate (on fqcn and no alias)
                }
                if( $key == $alias ) {
                    return $this; // also duplicate (on fqcn and alias)
                }
            }
        }
        Assert::assertFqcn( $fqcn );
        $key = ( null === $alias ) ? count( $this->uses ) : Assert::assertPhpVar( $alias );
        if( ! is_array( $this->uses )) {
            $this->uses = [];
        }
        $this->uses[$key] = $fqcn;
        return $this;
    }

    /**
     * Set array of class use set , each array of ( fqcn [, alias ] )
     *
     * @param string|array $uses
     * @return ClassMgr
     * @throws InvalidArgumentException
     */
    public function setUses( $uses ) {
        if( empty( $uses )) {
            throw new InvalidArgumentException( self::$ERR0 );
        }
        $this->uses = [];
        if( is_string( $uses )) {
            $this->addUse( $uses );
            return $this;
        }
        foreach( $uses as $useSet ) {
            if( empty( $useSet )) {
                throw new InvalidArgumentException( self::$ERR0 );
            }
            $this->addUse(
                $useSet[0],
                ( isset( $useSet[1] ) ? $useSet[1] : null )
            );
        } // end foreach
        return $this;
    }

    /**
     * @param string $extend
     * @return ClassMgr
     * @throws InvalidArgumentException
     */
    public function setExtend( $extend ) {
        Assert::assertFqcn( $extend );
        $this->extend = $extend;
        return $this;
    }

    /**
     * @param string $implement
     * @return ClassMgr
     * @throws InvalidArgumentException
     */
    public function addImplement( $implement ) {
        if( is_array( $this->implements ) && in_array( $implement, $this->implements )) {
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
     * @param string|array $implements
     * @return ClassMgr
     * @throws InvalidArgumentException
     */
    public function setImplements( $implements ) {
        if( empty( $implements )) {
            throw new InvalidArgumentException( self::$ERR0 );
        }
        $this->implements = [];
        if( ! is_array( $implements )) {
            $implements = [ $implements ];
        }
        foreach( $implements as $implement ) {
            if( empty( $implement )) {
                throw new InvalidArgumentException( self::$ERR0 );
            }
            $this->addImplement( $implement );
        }
        return $this;
    }

    /**
     * @param bool $construct
     * @return ClassMgr
     */
    public function setConstruct( $construct = true ) {
        $this->construct = (bool) $construct;
        return $this;
    }

    /**
     * @param bool $factory
     * @return ClassMgr
     */
    public function setFactory( $factory = true ) {
        $this->factory = (bool) $factory;
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
     * @param string|PropertyMgr|VariableMgr|VarDto $name
     * @param string  $type
     * @param mixed   $default
     * @param string  $summary
     * @param string  $description
     * @param bool    $getter
     * @param bool    $setter
     * @param bool    $argInFactory
     * @return ClassMgr
     * @throws InvalidArgumentException
     */
    public function addProperty(
        $name,
        $type = null,
        $default = null,
        $summary = null,
        $description = null,
        $getter = true,
        $setter = true,
        $argInFactory = true
    ) {
        if( is_array( $name )) {
            $name = array_values( $name );
            list( $name, $type, $default, $summary, $description, $getter, $setter, $argInFactory ) =
                array_pad( $name, 8, null );
        }
        switch( true ) {
            case ( $name instanceof PropertyMgr ) :
                $property = $name;
                break;
            case ( $name instanceof VariableMgr ) :
                $name = $name->getVarDto();
            // fall through
            case ( $name instanceof VarDto ) :
                $property = PropertyMgr::factory( $name )
                    ->setMakeGetter(( null === $type )        ? true  : (bool) $type )     // getter
                    ->setMakeSetter(( null === $default )     ? true  : (bool) $default )  // setter
                    ->setArgInFactory( ( null === $summary ) ? false : (bool) $summary ); // arg in factory
                break;
            case is_string( $name ) :
                $property = PropertyMgr::factory( VarDto::factory( $name, $type, $default, $summary, $description ))
                    ->setMakeGetter( ( null === $getter )          ? true  : (bool) $getter )        // getter
                    ->setMakeSetter( ( null === $setter )          ? true  : (bool) $setter )        // setter
                    ->setArgInFactory( ( null === $argInFactory ) ? false : (bool) $argInFactory ); // arg in factory
                break;
            default :
                throw new InvalidArgumentException( sprintf( self::$ERRx, var_export( $name, true )));
                break;

        } // end switch
        $this->properties[] = $property;
        return $this;
    }

    /**
     * @param $pIx
     * @return PropertyMgr
     */
    public function getProperty( $pIx ) {
        return $this->properties[$pIx];
    }

    /**
     * @return int
     */
    public function getPropertyCount() {
        return count( $this->properties );
    }

    /**
     * @return array
     */
    public function getPropertyIndex() {
        return array_keys( $this->properties );
    }

    /**
     * @return bool
     */
    private function hasOneArrayProperty() {
        switch( true ) {
            case ( 2 < count( $this->properties )) :
                break;
            case ( true !== ( $this->properties[0]->getVarDto()->isTypedArray() &&
                    ! $this->properties[0]->isConst() &&
                    ! $this->properties[0]->isStatic())) :
                break;
            case ( 1 == count( $this->properties )) :
                return true;
                break;
            case ( ClassMethodFactory::$POSITION == $this->properties[1]->getVarDto()->getName()) :
                return true;
                break;
            default :
                break;
        }
        return false;
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
    public function setProperties( $properties ) {
        static $ERRx1 = 'Invalid property (#%d) %s';
        $this->properties = [];
        if( ! is_array( $properties )) {
            $properties = [ $properties ];
        }
        foreach( $properties as $pIx => $property ) {
            switch( true ) {
                case empty( $property ) :
                    throw new InvalidArgumentException( sprintf( $ERRx1, $pIx, var_export( $property, true )));
                    break;
                case ( $property instanceof PropertyMgr ) :
                    $this->addProperty( $property );
                    break;
                case ( $property instanceof VariableMgr ) :
                    $property = $property->getVarDto();
                    // fall through
                case ( $property instanceof VarDto ) :
                    // fall through
                case ( is_string( $property )) :
                    $this->addProperty(
                        PropertyMgr::factory( $property )
                            ->setMakeGetter( true)
                            ->setMakeSetter( true)
                            ->setArgInFactory( false )
                    );
                    break;
                case ( ! is_array( $property )) :
                    throw new InvalidArgumentException( sprintf( $ERRx1, $pIx, var_export( $property, true )));
                    break;
                case ( $property[0] instanceof VarDto ) :
                    $this->addProperty(
                        PropertyMgr::factory( $property[0] )
                            ->setMakeGetter( Util::getIfSet( $property, 1, self::BOOL_T, true ) )  // make getter,
                            ->setMakeSetter( Util::getIfSet( $property, 2, self::BOOL_T, true ) )  // make getter
                            ->setArgInFactory( Util::getIfSet( $property, 1, self::BOOL_T, false ) ) // argInFactory
                    );
                    break;
                default :
                    $this->addProperty(
                        PropertyMgr::factory(
                            VarDto::factory(
                                Util::getIfSet( $property, 0 ),                           // variable,
                                Util::getIfSet( $property, 1 ),                           // varType,
                                Util::getIfSet( $property, 2 ),                           // default
                                Util::getIfSet( $property, 3, self::STRING_T ),           // summary
                                Util::getIfSet( $property, 4, self::ARRAY_T )             // description
                            )
                        )
                            ->setMakeGetter(
                                Util::getIfSet( $property, 5, self::BOOL_T, true )  // make getter
                            )
                            ->setMakeGetter(
                                Util::getIfSet( $property, 6, self::BOOL_T, true )  // make getter
                            )
                            ->setArgInFactory(
                                Util::getIfSet( $property, 7, self::BOOL_T, false ) // argInFactory
                            )
                    );
                    break;
            } // end switch
        } // end foreach
        return $this;
    }

}
