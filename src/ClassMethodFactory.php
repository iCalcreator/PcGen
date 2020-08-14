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

use Kigkonsult\PcGen\Dto\ArgumentDto;
use Kigkonsult\PcGen\Dto\VarDto;

/**
 * Class ClassMethodFactory
 *
 * Manages rendering of class methods
 *
 * @package Kigkonsult\PcGen
 */
class ClassMethodFactory implements PcGenInterface
{
    /**
     * Fixed class methods
     */

    /**
     * Render class constructor
     *
     * @param ClassMgr $classMgr
     * @return array
     */
    public static function renderConstructorMethod( ClassMgr $classMgr )
    {
        $TMPL1 = 'Class %s %s';
        $TMPL2 = 'constructor';
        $TMPL3 = '__construct';
        return array_merge(
            DocBlockMgr::init( $classMgr )
                ->setSummary( sprintf( $TMPL1, $classMgr->getName(), $TMPL2 ))
                ->toArray(),
            FcnFrameMgr::init( $classMgr )
                ->setName( $TMPL3 )
                ->toArray()
        );
    }

    /**
     * Render class factory method, opt with properties to update as arguments
     *
     * @param ClassMgr $classMgr
     * @return array
     */
    public static function renderFactoryMethod( ClassMgr $classMgr )
    {
        static $SUMMARY         = 'Class %s %s method';
        static $EXISTS          = ' exists';
        static $RETURNNEWSTATIC = 'return new static();';
        static $INSTANCEINIT    = '$instance = new static();';
        static $RETURNINSTANCE  = 'return $instance;';
        $docBlock = DocBlockMgr::init( $classMgr )
            ->setSummary( sprintf( $SUMMARY, $classMgr->getName(), self::FACTORY ));
        $fcnFrameMgr = FcnFrameMgr::init( $classMgr )
            ->setStatic()
            ->setName( self::FACTORY )
            ->setReturnType( self::SELF_KW );
        if( empty( $classMgr->getPropertyCount())) {
            $docBlock->setTag( self::RETURN_T, self::STATIC_KW );
            if( $classMgr->isExtendsSet()) {
                $docBlock->setTag( self::TODO_T, self::PARENT_KW . $EXISTS );
            }
            return array_merge(
                $docBlock->toArray(),
                $fcnFrameMgr->setBody( $RETURNNEWSTATIC )->toArray()
            );
        }
        $body = [];
        foreach( $classMgr->getPropertyIndex() as $pIx ) {
            $property = $classMgr->getProperty( $pIx );
            if( ! $property->isFactoryFcnArgument()) {
                continue;
            }
            $varDto = $property->getVarDto();
            $fcnFrameMgr->addArgument( $varDto );
            $docBlock->setTag(
                self::PARAM_T,
                $varDto->getParamTagVarType(),
                $varDto->getName()
            );
            self::renderPropertyAssignSetCode( $property, $body );
        } // end foreach
        if( empty( $body )) {
            $body[] = $RETURNNEWSTATIC;
        }
        else {
            array_unshift( $body, $INSTANCEINIT );
            $body[] = $RETURNINSTANCE;
        }
        $docBlock->setTag( self::RETURN_T, self::STATIC_KW );
        if( $classMgr->isExtendsSet()) {
            $docBlock->setTag(
                self::TODO_T,
                self::PARENT_KW . $EXISTS
            );
        }
        return array_merge(
            $docBlock->toArray(),
            $fcnFrameMgr->setBody( $body )->toArray()
        );
    }

    /**
     * Render class instance property assign code, using set-method or direct
     *
     * @param PropertyMgr $property
     * @param array       $code
     * @return void
     */
    private static function renderPropertyAssignSetCode(
        PropertyMgr $property,
        array & $code
    ) {
        static $INSTANCE = '$instance';
        static $SET = 'set';
        static $END = ';';
        $propName = $property->getName();
        $target   = EntityMgr::init( $property )
            ->setClass( $INSTANCE )
            ->setForceVarPrefix( false );
        if( $property->isMakeSetter() ) { // use property set-method
            $row  = FcnInvokeMgr::init( $property->getEol(), self::SP0, self::SP0 )
                ->setName( $target->setVariable( $SET . ucfirst( $propName )))
                ->addArgument( $propName )
                ->toString();
            $code[] = rtrim( $row ) . $END;
            return;
        }
        // update class property direct, has no set-method
        $code = array_merge( $code,
            AssignClauseMgr::init( $property->getEol(), self::SP0, self::SP0 )
                ->setTarget( $target->setVariable( $propName ))
                ->setVariableSource( $propName )
                ->toArray()
        );
    }

    /**
     * Render class property get-methods
     */

     /**
     * Render get-method for property
     *
     * @param PropertyMgr $property
     * @param array  $code
     * @return void
     */
    public static function renderGetterMethod(
        PropertyMgr $property,
        array & $code
    ) {
        static $GET = 'get';
        static $IS  = 'is';
        static $SUMMARY = 'Return %s %s';
        $propName = $property->getName();
        $varType  = $property->getVarDto()->getParamTagVarType();
        switch( true ) {
            case ( 0 == strcasecmp( $IS, substr( $propName, 0, 2 ))) :
                $fcnName = $propName;
                break;
            case ( self::BOOL_T == $varType ) :
                $fcnName = $IS . ucfirst( $propName );
                break;
            default :
                $fcnName = $GET . ucfirst( $propName );
        } // end switch
        $fcnFrameMgr = FcnFrameMgr::init( $property )
            ->setName( $fcnName )
            ->setReturnProperty( $propName );
        if( ! empty( $varType ) &&
            ! is_array( $varType ) && ( self::MIXED_KW != $varType )) {
            $fcnFrameMgr->setReturnType( $varType );
        }
        $code= array_merge( $code,
            DocBlockMgr::init( $property )
                ->setSummary( sprintf( $SUMMARY, $varType, $propName ))
                ->setTag( self::RETURN_T, $varType )
                ->toArray(),
            $fcnFrameMgr->toArray()
        );
    }

    /**
     * Render is-property-set-method for (not bool-) property
     *
     * @param PropertyMgr $property
     * @param array  $code
     * @return void
     */
    public static function renderIsPropertySetMethod(
        PropertyMgr $property,
        array & $code
    ) {
        static $SUMMARY    = 'Return bool true if  %s  is set (i.e. not %s)';
        static $FCNNAME    = 'is%sSet';
        static $NULLCODE   = 'return ( null !== $this->%s );';
        static $BOOLCODE   = 'return ( %s !== $this->%s );';
        static $ARRAYCODE  = 'return ! empty( $this->%s );';
        $propName = $property->getName();
        $varType  = $property->getVarDto()->getParamTagVarType();
        if( self::BOOL_T == $varType ) {
            return;
        }
        $initValue = $property->getVarDto()->getDefault();
        switch( true ) {
            case (( null === $initValue ) ||
                ( is_string( $initValue ) && ( 0 === strcasecmp(self::NULL_T, $initValue )))) :
                $val  = self::NULL_T;
                $body = sprintf( $NULLCODE, $propName );
                break;
            case is_bool( $initValue ) :
                $val  = Util::renderScalarValue( $initValue );
                $body = sprintf( $BOOLCODE, $val, $propName );
                break;
            case is_scalar( $initValue ) :
                $val  = self::NULL_T;
                $body = sprintf( $NULLCODE, $propName );
                break;
            default : // array
                $val  = self::ARRAY2_T;
                $body = sprintf( $ARRAYCODE, $propName );
                break;
        } // end switch
        $code = array_merge( $code,
            DocBlockMgr::init( $property )
                ->setSummary( sprintf( $SUMMARY, $propName, $val ))
                ->setTag( self::RETURN_T, self::BOOL_T )
                ->toArray(),
            FcnFrameMgr::init( $property )
                ->setName( sprintf( $FCNNAME, ucfirst( $propName )))
                ->setReturnType( self::BOOL_T )
                ->setBody( $body )
                ->toArray()
        );
    }

    /**
     * Render property-count-method for array-property
     *
     * @param PropertyMgr $property
     * @param array  $code
     * @return void
     */
    public static function renderPropertyCountMethod(
        PropertyMgr $property,
        array & $code
    ) {
        if( ! $property->getVarDto()->isTypedArray()) {
            return;
        }
        $propName = $property->getName();
        $code = array_merge( $code,
            self::implIterCountMethod(
                $property,
                null,
                $propName
            )
        );
    }

    /**
     * @param PropertyMgr $property
     * @param string      $decsription
     * @param string      $fcnNamePrefix
     * @return array
     */
    private static function implIterCountMethod(
        PropertyMgr $property,
        $decsription = null,
        $fcnNamePrefix = null
    ) {
        static $COUNT    = 'count';
        static $SUMMARY  = 'Return count of %s elements';
        static $CODETMPL = 'return count( $this->%s );';
        $propName    = $property->getName();
        $docBlockMgr = DocBlockMgr::init( $property )
            ->setSummary( sprintf( $SUMMARY, $propName ) )
            ->setTag( self::RETURN_T, self::INT_T );
        if( ! empty( $decsription )) {
            $docBlockMgr->setDescription( $decsription );
        }
        $fcnFrameMgr = FcnFrameMgr::init( $property )
            ->setReturnType( self::INT_T )
            ->setBody( sprintf( $CODETMPL, $propName ));
        $fcnFrameMgr->setName(
            empty( $fcnNamePrefix)
                ? $COUNT
                : $fcnNamePrefix . ucfirst( $COUNT )
        );
        return array_merge(
            $docBlockMgr->toArray(),
            $fcnFrameMgr->toArray()
        );
    }

    /**
     * Render class property set-methods
     */

    /**
     * Render append-method for array property
     *
     * @param PropertyMgr $property
     * @param array  $code
     * @return void
     */
    public static function renderAppendArrayMethod(
        PropertyMgr $property,
        array & $code
    ) {
        $ADD1     = 'Append an %s array element';
        $ADD2     = 'append';
        $varDto   = $property->getVarDto();
        $propName = $varDto->getName();
        $code = array_merge( $code,
            DocBlockMgr::init( $property )
                ->setSummary( sprintf( $ADD1, $propName ))
                ->setTag(
                    self::PARAM_T,
                    $varDto->hasTypeHintArraySpec(
                        DocBlockMgr::getTargetPhpVersion(),
                        $typeHint
                    )
                        ? $typeHint
                        : self::MIXED_KW,
                    $propName
                )
                ->setTag( self::RETURN_T, self::STATIC_KW )
                ->toArray(),
            FcnFrameMgr::init( $property )
                ->setName( $ADD2 . ucfirst( $propName ))
                ->addArgument(
                    ArgumentDto::factory( $varDto )
                        ->setUpdClassProperty( ArgumentDto::BEFORE )
                        ->setNextVarPropIndex( true )
                )
                ->setReturnThis()
                ->toArray()
        );
    }

    /**
     * Render set-method
     *
     * @param PropertyMgr $property
     * @param array  $code
     * @return void
     */
    public static function renderSetterMethod(
        PropertyMgr $property,
        array & $code
    ) {
        $SET      = 'set';
        $varDto   = $property->getVarDto();
        $propName = $varDto->getName();
        $code     = array_merge( $code,
            DocBlockMgr::init( $property )
                ->setSummary( ucfirst( $SET ) . self::SP1 . $propName )
                ->setTag( self::PARAM_T, $varDto->getParamTagVarType(), $propName )
                ->setTag( self::RETURN_T, self::STATIC_KW )
                ->toArray(),
            FcnFrameMgr::init( $property )
                ->setName($SET . ucfirst( $propName ))
                ->addArgument( ArgumentDto::factory( $varDto )
                    ->setUpdClassProperty( ArgumentDto::BEFORE ))
                ->setReturnThis()
                ->toArray()
        );
    }

    /**
     * Render (code) setting class instance property value from (same named) argument
     *
     * If BEFORE and default is null', a null-test is done
     *
     * @param ArgumentDto $argument
     * @param BaseA       $base
     * @return array  $code
     */
    public static function renderPropValueSetCode(
        ArgumentDto $argument,
        BaseA $base
    ) {
        static $IFNOTNULL = '%1$sif( null !== $%2$s ) {';
        static $ACTION    = '%1$s%2$s%3$s';
        static $END       = '%s}';
        $code = AssignClauseMgr::init( $base )
            ->setTarget(
                self::THIS_KW,
                $argument->getName(),
                ( $argument->isNextVarPropIndex() ? self::ARRAY2_T : null )
            )
            ->setSource( null, Util::setVarPrefix( $argument->getName()))
            ->toArray();
        if(( ArgumentDto::BEFORE != $argument->getUpdClassProp()) ||
            ! $argument->isDefaultSet() ||
            ! $argument->isDefaultTypedNull()) {
            return $code;
        }
        $indent = $base->getBaseIndent() . $base->getIndent();
        return [
            sprintf( $IFNOTNULL, $indent, $argument->getName()),
            sprintf( $ACTION, $indent, $base->getIndent(), trim( $code[0] )),
            sprintf( $END, $indent )
        ];
    }

    /**
     * Render class array property (Seekable-)Iterator/Countable-methods
     */

    /**
     * @var string  (Seekable-)Iterator usage
     */
    public  static $POSITION      = 'position';
    private static $OoBException  = 'OutOfBoundsException';
    public  static $USES          = [
        'ArrayIterator',
        'Countable',
        'OutOfBoundsException',
        'SeekableIterator',
        'Traversable'
    ];
    public  static $IMPLEMENTS    = [
        'SeekableIterator',
        'Countable'
    ];

    /**
     * @param ClassMgr $classMgr
     * @return void
     */
    public static function setUpIteratorForClass( ClassMgr $classMgr )
    {
        if( $classMgr->isNamespaceSet()) {
            foreach( self::$USES as $use ) {
                $classMgr->addUse( $use );
            }
        }
        foreach( self::$IMPLEMENTS as $implement ) {
            $classMgr->addImplement( $implement );
        }
        ClassMethodFactory::setPositionProperty( $classMgr );
    }

    /**
     * Fix the Iterator position property, no getter or setter
     *
     * @param ClassMgr $classMgr
     * @return void
     */
    private static function setPositionProperty( ClassMgr $classMgr )
    {
        static $ITERATORixTxt = 'Iterator index';
        foreach( $classMgr->getPropertyIndex() as $pIx ) {
            if( self::$POSITION == $classMgr->getProperty( $pIx )->getName()) {
                return; // already set
            }
        }
        $classMgr->addProperty(
            PropertyMgr::init( $classMgr )
                ->setVarDto(
                    VarDto::factory( self::$POSITION, self::INT_T, 0, $ITERATORixTxt )
                )
                ->setMakeGetter( false )
                ->setMakeSetter( false )
        );
    }

    /**
     * Render (Seekable-)Iterator/Countable methods etc for array typed property
     *
     * @param PropertyMgr $property
     * @param array  $code
     * @return void
     */
    public static function renderIteratorGetterMethods(
        PropertyMgr $property,
        array & $code
    ) {
        $code = array_merge(
            $code,
            self::renderIterCountMethod( $property ),
            self::renderIterCurrentMethod( $property ),
            self::renderIterExistsMethod( $property ),
            self::renderIterGetIteratorMethod( $property ),
            self::renderIterKeyMethod( $property ),
            self::renderIterLastMethod( $property ),
            self::renderIterNextMethod( $property ),
            self::renderIterPreviousMethod( $property ),
            self::renderIterRewindMethod( $property ),
            self::renderIterSeekMethod( $property ),
            self::renderIterValidMethod( $property )
        ); // end array_merge
    }

    /**
     * @param PropertyMgr $property
     * @return array
     */
    private static function renderIterCountMethod( PropertyMgr $property )
    {
        static $DESCRIPTION = 'Required method implementing the Countable interface';
        return self::implIterCountMethod( $property, $DESCRIPTION );
    }

    /**
     * @param PropertyMgr $property
     * @return array
     */
    private static function renderIterCurrentMethod( PropertyMgr $property )
    {
        static $SUMMARY     = 'Return the current element';
        static $DESCRIPTION = 'Required method implementing the Iterator interface';
        static $FCNNAME     = 'current';
        static $CODETMPL    = 'return $this->%s[$this->position];';
        $returnType = $property->getVarDto()->hasTypeHintArraySpec(
            DocBlockMgr::getTargetPhpVersion(),
            $typeHint
        )
            ? $typeHint
            : self::MIXED_KW;
        return array_merge(
            DocBlockMgr::init( $property )
                ->setInfo( $SUMMARY, $DESCRIPTION )
                ->setTag( self::RETURN_T, $returnType )
                ->toArray(),
            FcnFrameMgr::init( $property )
                ->setName( $FCNNAME )
                ->setBody( sprintf( $CODETMPL, $property->getName()))
                ->toArray()
        ); // end return array_merge
    }

    /**
     * @param PropertyMgr $property
     * @return array
     */
    private static function renderIterExistsMethod( PropertyMgr $property )
    {
        static $SUMMARY     = 'Checks if position is set';
        static $FCNNAME     = 'exists';
        static $CODETMPL    = 'return array_key_exists( $position, $this->%s );';
        return array_merge(
            DocBlockMgr::init()
                ->setBaseIndent( $property->getBaseIndent())
                ->setSummary( $SUMMARY )
                ->setTag( self::PARAM_T, self::INT_T, self::$POSITION )
                ->setTag( self::RETURN_T, self::BOOL_T )
                ->toArray(),
            // todo rework with init here
            FcnFrameMgr::init( $property )
                ->setName( $FCNNAME )
                ->addArgument( varDto::factory( self::$POSITION, self::INT_T ))
                ->setReturnType( self::BOOL_T )
                ->setBody( sprintf( $CODETMPL, $property->getName()))
                ->toArray()
        ); // end return array_merge
    }

    /**
     * Implement IteratorAggregate, method GetIterator
     *
     * @link https://www.php.net/manual/en/class.iteratoraggregate.php
     * @param PropertyMgr $property
     * @return array
     */
    private static function renderIterGetIteratorMethod( PropertyMgr $property )
    {
        static $SUMMARY     = 'Retrieve an external iterator';
        static $DESCRIPTION =  [
            'Method implementing the IteratorAggregate interface,',
            'returning Traversable, i.e. makes the class traversable using foreach.',
            'Usage : \'foreach( $class as $value ) { .... }\''
        ];
        static $FCNNAME     = 'GetIterator';
        static $RETURNTYPE  = 'Traversable';
        static $CODETMPL    = 'return new ArrayIterator( $this->%s );';
        return array_merge(
            DocBlockMgr::init( $property)
                ->setInfo( $SUMMARY, $DESCRIPTION )
                ->setTag( self::RETURN_T, $RETURNTYPE )
                ->toArray(),
            FcnFrameMgr::init( $property )
                ->setName( $FCNNAME )
                ->setReturnType( $RETURNTYPE )
                ->setBody( sprintf( $CODETMPL, $property->getName()))
                ->toArray()
        ); // end return array_merge
    }

    /**
     * @param PropertyMgr $property
     * @return array
     */
    private static function renderIterKeyMethod( PropertyMgr $property )
    {
        static $SUMMARY     = 'Return the key of the current element';
        static $DESCRIPTION = 'Required method implementing the Iterator interface';
        static $FCNNAME     = 'key';
        return array_merge(
            DocBlockMgr::init( $property )
                ->setInfo( $SUMMARY, $DESCRIPTION )
                ->setTag( self::RETURN_T, self::INT_T )
                ->toArray(),
            FcnFrameMgr::init( $property )
                ->setName( $FCNNAME )
                ->setReturnType( self::INT_T )
                ->setReturnProperty( self::$POSITION )
                ->toArray()
        ); // end return array_merge
    }

    /**
     * @param PropertyMgr $property
     * @return array
     */
    private static function renderIterLastMethod( PropertyMgr $property )
    {
        static $SUMMARY  = 'Move position to last element';
        static $FCNNAME  = 'last';
        static $CODETMPL = '$this->position = count( $this->%s ) - 1;';
        return array_merge(
            DocBlockMgr::init( $property )
                ->setSummary( $SUMMARY )
                ->setTag( self::RETURN_T, self::STATIC_KW )
                ->toArray(),
            FcnFrameMgr::init( $property )
                ->setName( $FCNNAME )
                ->setBody( sprintf( $CODETMPL, $property->getName()))
                ->setReturnThis()
                ->toArray()
        ); // end return array_merge
    }

    /**
     * @param PropertyMgr $property
     * @return array
     */
    private static function renderIterNextMethod( PropertyMgr $property )
    {
        static $SUMMARY     = 'Move position forward to next element';
        static $DESCRIPTION = 'Required method implementing the Iterator interface';
        static $FCNNAME     = 'next';
        static $OPERATOR    = '+=';
        return array_merge(
            DocBlockMgr::init( $property )
                ->setInfo( $SUMMARY, $DESCRIPTION )
                ->setTag( self::RETURN_T, self::STATIC_KW )
                ->toArray(),
            FcnFrameMgr::init( $property )
                ->setName( $FCNNAME )
                ->setBody(
                    AssignClauseMgr::init()
                        ->setBaseIndent()
                        ->setIndent()
                        ->setTarget( self::THIS_KW, self::$POSITION )
                        ->setOperator( $OPERATOR )
                        ->setScalar( 1 )
                        ->toString()
                )
                ->setReturnThis()
                ->toArray()
        ); // end return array_merge
    }

    /**
     * @param PropertyMgr $property
     * @return array
     */
    private static function renderIterPreviousMethod( PropertyMgr $property )
    {
        static $SUMMARY  = 'Move position backward to previous element';
        static $FCNNAME  = 'previous';
        static $OPERATOR = '-=';
        return array_merge(
            DocBlockMgr::init( $property )
                ->setSummary( $SUMMARY )
                ->setTag( self::RETURN_T, self::STATIC_KW )
                ->toArray(),
            FcnFrameMgr::init( $property )
                ->setName( $FCNNAME )
                ->setBody(
                    AssignClauseMgr::init()
                        ->setBaseIndent()
                        ->setIndent()
                        ->setTarget( self::THIS_KW, self::$POSITION )
                        ->setOperator( $OPERATOR )
                        ->setScalar( 1 )
                        ->toString()
                )
                ->setReturnThis()
                ->toArray()
        ); // end return array_merge
    }

    /**
     * @param PropertyMgr $property
     * @return array
     */
    private static function renderIterRewindMethod( PropertyMgr $property )
    {
        static $SUMMARY     = 'Rewind the Iterator to the first element';
        static $DESCRIPTION = 'Required method implementing the Iterator interface';
        static $FCNNAME     = 'rewind';
        return array_merge(
            DocBlockMgr::init( $property )
                ->setInfo( $SUMMARY, $DESCRIPTION )
                ->setTag( self::RETURN_T, self::STATIC_KW )
                ->toArray(),
            FcnFrameMgr::init( $property )
                ->setName( $FCNNAME )
                ->setBody(
                    AssignClauseMgr::init()
                        ->setBaseIndent()
                        ->setIndent()
                        ->setTarget( self::THIS_KW, self::$POSITION )
                        ->setScalar( 0 )
                        ->toString()
                )
                ->setReturnThis()
                ->toArray()
        ); // end return array_merge
    }

    /**
     * @param PropertyMgr $property
     * @return array
     */
    private static function renderIterSeekMethod( PropertyMgr $property )
    {
        static $SUMMARY     = 'Seeks to a given position in the iterator';
        static $DESCRIPTION = 'Required method implementing the SeekableIterator interface';
        static $FCNNAME     = 'seek';
        static $ERRVARNAME  = 'ERRTXT';
        static $ERRVARTMPL  = 'Position %d not found!';
        static $CODETMPL    =
            'if( ! array_key_exists( $position, $this->%s )) {' . PHP_EOL .
            '    throw new OutOfBoundsException( sprintf( $ERRTXT, $position ));' . PHP_EOL .
            '}';
        $argument = ArgumentDto::factory( self::$POSITION )
            ->setUpdClassProperty( ArgumentDto::AFTER );
        return array_merge(
            DocBlockMgr::init(  $property )
                ->setInfo( $SUMMARY, $DESCRIPTION )
                ->setTag( self::PARAM_T, self::INT_T, $argument->getName() )
                ->setTag( self::RETURN_T, self::VOID_KW )
                ->setTag( self::THROWS_T, self::$OoBException )
                ->toArray(),
            // todo rework with init here
            FcnFrameMgr::init( $property ) // no typed arg here..
                ->setName( $FCNNAME )
                ->addArgument( $argument )
                ->setBody(
                    VariableMgr::init( null, self::SP0, self::SP0 )
                        ->setName( $ERRVARNAME )
                        ->setVisibility()
                        ->setStatic( true )
                        ->setInitValue( $ERRVARTMPL )
                        ->toArray(),
                    explode( PHP_EOL, sprintf( $CODETMPL, $property->getName()))
                )
                ->toArray()
        ); // end return array_merge
    }

    /**
     * @param PropertyMgr $property
     * @return array
     */
    private static function renderIterValidMethod( PropertyMgr $property )
    {
        static $SUMMARY     = 'Checks if current position is valid';
        static $DESCRIPTION = 'Required method implementing the Iterator interface';
        static $FCNNAME     = 'valid';
        static $CODETMPL    = 'return array_key_exists( $this->position, $this->%s );';
        return array_merge(
            DocBlockMgr::init( $property )
                ->setInfo( $SUMMARY, $DESCRIPTION )
                ->setTag( self::RETURN_T, self::BOOL_T )
                ->toArray(),
            FcnFrameMgr::init( $property )
                ->setName( $FCNNAME )
                ->setReturnType( self::BOOL_T )
                ->setBody( sprintf( $CODETMPL, $property->getName()))
                ->toArray()
        ); // end return array_merge
    }
}
