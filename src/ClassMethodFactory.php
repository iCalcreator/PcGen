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
     * @param string $className
     * @return array
     */
    public static function renderConstructorMethod( $className ) {
        $TMPL1 = 'Class %s %s';
        $TMPL2 = 'constructor';
        $TMPL3 = '__construct';
        return array_merge(
            DocBlockMgr::init()
                ->setSummary( sprintf( $TMPL1, $className, $TMPL2 ))
                ->toArray(),
            FcnFrameMgr::init()->setName( $TMPL3 )
                ->setReturnType( self::SELF_KW )
                ->toArray()
        );
    }

    /**
     * Render class factory method, opt with properties to update as arguments
     *
     * @param ClassMgr $classMgr
     * @return array
     */
    public static function renderFactoryMethod( ClassMgr $classMgr ) {
        static $SUMMAY  = 'Class %s %s method';
        static $FACTORY = 'factory';
        static $RETURNNEWSTATIC = 'return new static();';
        static $INSTANCEINIT    = '$instance = new static();';
        static $TMPL7   = 'return $instance;';
        $dbm = DocBlockMgr::init()
            ->setSummary( sprintf( $SUMMAY, $classMgr->getName(), $FACTORY ));
        $ffm = FcnFrameMgr::init()
            ->setStatic()
            ->setName( $FACTORY )
            ->setReturnType( self::SELF_KW );
        if( empty( $classMgr->getPropertyCount())) {
            $dbm->setTag( self::RETURN_T, self::STATIC_KW );
            return array_merge( $dbm->toArray(), $ffm->setBody( $RETURNNEWSTATIC )->toArray());
        }
        $body = [];
        foreach( $classMgr->getPropertyIndex() as $pIx ) {
            $property = $classMgr->getProperty( $pIx );
            if( ! $property->isFactoryFcnArgument()) {
                continue;
            }
            $varDto = $property->getVarDto();
            $ffm->addArgument( ArgumentDto::factory( $varDto ));
            $dbm->setTag( self::PARAM_T, $varDto->getParamTagVarType(), $varDto->getName());
            self::renderPropertyAssignSetCode( $varDto, $property->isMakeSetter(), $body );
        } // end foreach
        if( empty( $body )) {
            $body[] = $RETURNNEWSTATIC;
        }
        else {
            array_unshift( $body, $INSTANCEINIT );
            $body[] = $TMPL7;
        }
        $dbm->setTag( self::RETURN_T, self::STATIC_KW );
        return array_merge( $dbm->toArray(), $ffm->setBody( $body )->toArray());
    }

    /**
     * Render property assign code
     *
     * @param VarDto $varDto
     * @param bool   $hasSetterMethod
     * @param array  $code
     * @return void
     */
    private static function renderPropertyAssignSetCode( VarDto $varDto, $hasSetterMethod, array & $code ) {
        static $INSTANCE = '$instance';
        static $SET      = 'set';
        static $END      = ';';
        $propName = $varDto->getName();
        if( ! $hasSetterMethod ) {
            // update class property direct
            $code[] = AssignClauseMgr::init()
                ->setTarget( EntityMgr::factory( $INSTANCE, $propName, null, false ))
                ->setVariableSource( $propName )
                ->toString();
            return;
        }
        // use property set-method
        $code = array_merge( $code,
            FcnInvokeMgr::init()
                ->setName(
                    EntityMgr::factory( $INSTANCE, $SET . ucfirst( $propName ), null, false )
                )
                ->addArgument( $propName )
                ->toArray()
        );
        $bIx         = count( $code ) - 1;
        $code[$bIx] .= $END;
    }

    /**
     * Render class property get-methods
     */

     /**
     * Render get-method for property
     *
     * @param VarDto $varDto
     * @param array  $code
     * @return void
     */
    public static function renderGetterMethod( VarDto $varDto, array & $code ) {
        static $GET = 'get';
        static $IS  = 'is';
        static $SUMMARY = 'Return %s %s';
        $propName = $varDto->getName();
        $varType  = $varDto->getParamTagVarType();
        $prefix   = ( self::BOOL_T == $varType ) ? $IS : $GET;
        $ffm      = FcnFrameMgr::init()
            ->setName( $prefix . ucfirst( $propName ))
            ->setReturnProperty( $propName );
        if( ! empty( $varType ) && ( self::MIXED_KW !== $varType )) {
            $ffm->setReturnType( $varType );
        }
        $code= array_merge( $code,
            DocBlockMgr::factory( self::RETURN_T, $varType )
                ->setSummary( sprintf( $SUMMARY, $varType, $propName ))
                ->toArray(),
            $ffm->toArray()
        );
    }

    /**
     * Render is-property-set-method for (not bool-) property
     *
     * @param VarDto $varDto
     * @param array  $code
     * @return void
     */
    public static function renderIsPropertySetMethod( VarDto $varDto, array & $code ) {
        static $SUMMARY    = 'Return bool true if  %s  is set (i.e. not %s)';
        static $FCNNAME    = 'is%sSet';
        static $NULLCODE   = 'return ( null !== $this->%s );';
        static $BOOLCODE   = 'return ( %s !== $this->%s );';
        static $SCALARCODE = 'return ( %s != $this->%s );';
        static $ARRAYCODE  = 'return ( [] != $this->%s );';
        $propName = $varDto->getName();
        $varType  = $varDto->getParamTagVarType();
        if( self::BOOL_T == $varType ) {
            return;
        }
        $initValue = $varDto->getDefault();
        switch( true ) {
            case ( null === $initValue ) :
                $val  = self::NULL_T;
                $body = sprintf( $NULLCODE, $propName );
                break;
            case is_bool( $initValue ) :
                $val  = Util::renderScalarValue( $initValue );
                $body = sprintf( $BOOLCODE, $val, $propName );
                break;
            case is_scalar( $initValue ) :
                $val  = Util::renderScalarValue( $initValue );
                $body = sprintf( $SCALARCODE, $val, $propName );
                break;
            default : // array
                $val  = self::ARRAY2_T;
                $body = sprintf( $ARRAYCODE, $propName );
                break;
        }
        $code = array_merge( $code,
            DocBlockMgr::factory( self::RETURN_T, self::BOOL_T )
                ->setSummary( sprintf( $SUMMARY, $propName, $val ))
                ->toArray(),
            FcnFrameMgr::init()
                ->setName( sprintf( $FCNNAME, ucfirst( $propName )))
                ->setReturnType( self::BOOL_T )
                ->setBody( $body )
                ->toArray()
        );
    }

    /**
     * Render property-count-method for array-property
     *
     * @param VarDto $varDto
     * @param array  $code
     * @return void
     */
    public static function renderPropertyCountMethod( VarDto $varDto, array & $code ) {
        static $COUNT   = 'Count';
        static $SUMMARY = 'Return count %s';
        static $CODE    = 'return count( $this->%s );';
        if( ! $varDto->isTypedArray()) {
            return;
        }
        $propName   = $varDto->getName();
        $code = array_merge( $code,
            DocBlockMgr::factory( self::RETURN_T, self::INT_T )
                ->setSummary( sprintf( $SUMMARY, $propName ))
                ->toArray(),
            FcnFrameMgr::init()
                ->setName( lcfirst( $propName ) . $COUNT )
                ->setReturnType( self::INT_T )
                ->setBody( sprintf( $CODE, $propName ))
                ->toArray()
        );
    }

    /**
     * Render class property set-methods
     */

    /**
     * Render append-method for array property
     *
     * @param VarDto $varDto
     * @param array  $code
     * @return void
     */
    public static function renderAppendArrayMethod( VarDto $varDto, array & $code ) {
        $ADD1     = 'Append an %s array element';
        $ADD2     = 'append';
        $propName = $varDto->getName();
        $code = array_merge( $code,
            DocBlockMgr::init()
                ->setSummary( sprintf( $ADD1, $propName ))
                ->setTag(
                    self::PARAM_T,
                    $varDto->hasTypeHintArraySpec( PHP_VERSION, $typeHint )
                        ? $typeHint
                        : self::MIXED_KW,
                    $propName
                )
                ->setTag( self::RETURN_T, self::STATIC_KW )
                ->toArray(),
            FcnFrameMgr::init()
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
     * @param VarDto $varDto
     * @param array  $code
     * @return void
     */
    public static function renderSetterMethod( VarDto $varDto, array & $code ) {
        $SET      = 'set';
        $SP1      = ' ';
        $propName = $varDto->getName();
        $code     = array_merge( $code,
            DocBlockMgr::init()
                ->setSummary( ucfirst( $SET ) . $SP1 . $propName )
                ->setTag( self::PARAM_T, $varDto->getParamTagVarType(), $propName )
                ->setTag( self::RETURN_T, self::STATIC_KW )
                ->toArray(),
            FcnFrameMgr::factory(
                $SET . ucfirst( $propName ),
                ArgumentDto::factory( $varDto )->setUpdClassProperty( ArgumentDto::BEFORE )
            )
                ->setReturnThis()
                ->toArray()
        );
    }

    /**
     * Render class array property (Seekable-)Iterator/Countable-methods
     */

    /**
     * @var string  (Seekable-)Iterator usage
     */
    public static $POSITION      = 'position';
    public static $OoBException  = 'OutOfBoundsException';
    public static $USES          = [
        'ArrayIterator',
        'Countable',
        'IteratorAggregate',
        'OutOfBoundsException',
        'SeekableIterator',
        'Traversable'
    ];
    public static $IMPLEMENTS    = [
        'SeekableIterator',
        'Countable',
        'IteratorAggregate'
    ];

    /**
     * Return the Iterator position property
     *
     * @return PropertyMgr
     */
    public static function getPositionProperty() {
        static $ITERATORixTxt = 'Iterator index';
        return PropertyMgr::factory(self::$POSITION, self::INT_T, 0, $ITERATORixTxt )
                          ->setVisibility( self::PRIVATE_ )
                          ->setMakeGetter( false )
                          ->setMakeSetter( false );
    }

    /**
     * Render (Seekable-)Iterator/Countable methods for array typed property
     *
     * @param VarDto $varDto
     * @param array  $code
     * @return void
     */
    public static function renderIteratorGetterMethods( VarDto $varDto, array & $code ) {
        $code = array_merge( $code,
            self::renderCountMethod( $varDto ),
            self::renderCurrentMethod( $varDto ),
            self::renderExistsMethod( $varDto ),
            self::renderGetIteratorMethod( $varDto ),
            self::renderKeyMethod(),
            self::renderLastMethod( $varDto ),
            self::renderNextMethod(),
            self::renderPreviousMethod(),
            self::renderRewindMethod(),
            self::renderSeekMethod( $varDto ),
            self::renderValidMethod( $varDto )
        ); // end array_merge
    }

    /**
     * @param VarDto $varDto
     * @return array
     */
    private static function renderCountMethod( VarDto $varDto ) {
        static $SUMMARY     = 'Return count of elements';
        static $DESCRIPTION = 'Required method implementing the Countable interface';
        static $FCNNAME     = 'count';
        static $CODETMPL    = 'return count( $this->%s );';
        return array_merge(
            DocBlockMgr::factory( self::RETURN_T, self::INT_T )
                ->setInfo( $SUMMARY, $DESCRIPTION )
                ->toArray(),
            FcnFrameMgr::init()
                ->setName( $FCNNAME )
                ->setReturnType( self::INT_T )
                ->setBody( sprintf( $CODETMPL, $varDto->getName()))
                ->toArray()
        ); // end return array_merge
    }

    /**
     * @param VarDto $varDto
     * @return array
     */
    private static function renderCurrentMethod( VarDto $varDto ) {
        static $SUMMARY     = 'Return the current element';
        static $DESCRIPTION = 'Required method implementing the Iterator interface';
        static $FCNNAME     = 'current';
        static $CODETMPL    = 'return $this->%s[$this->position];';
        $returnType = $varDto->hasTypeHintArraySpec( null, $typeHint ) ? $typeHint : self::MIXED_KW;
        return array_merge(
            DocBlockMgr::factory( self::RETURN_T, $returnType )
                ->setInfo( $SUMMARY, $DESCRIPTION )
                ->toArray(),
            FcnFrameMgr::init()
                ->setName( $FCNNAME )
                ->setBody( sprintf( $CODETMPL, $varDto->getName()))
                ->toArray()
        ); // end return array_merge
    }

    /**
     * @param VarDto $varDto
     * @return array
     */
    private static function renderExistsMethod( VarDto $varDto ) {
        static $SUMMARY     = 'Checks if position is set';
        static $FCNNAME     = 'exists';
        static $CODETMPL    = 'return array_key_exists( $position, $this->%s );';
        return array_merge(
            DocBlockMgr::init()
                ->setSummary( $SUMMARY )
                ->setTag( self::PARAM_T, self::INT_T, self::$POSITION )
                ->setTag( self::RETURN_T, self::BOOL_T )
                ->toArray(),
            FcnFrameMgr::factory( $FCNNAME, varDto::factory( self::$POSITION, self::INT_T ))
                ->setReturnType( self::BOOL_T )
                ->setBody( sprintf( $CODETMPL, $varDto->getName()))
                ->toArray()
        ); // end return array_merge
    }

    /**
     * Implement IteratorAggregate, method GetIterator
     *
     * @link https://www.php.net/manual/en/class.iteratoraggregate.php
     * @param VarDto $varDto
     * @return array
     */
    private static function renderGetIteratorMethod( VarDto $varDto ) {
        static $SUMMARY     = 'Retrieve an external iterator';
        static $DESCRIPTION =  [
            'Required method implementing the IteratorAggregate interface,',
            'returning Traversable, i.e. makes the class traversable using foreach.',
            'Usage : \'foreach( $class as $value ) { .... }\''
        ];
        static $FCNNAME     = 'GetIterator';
        static $RETURNTYPE  = 'Traversable';
        static $CODETMPL    = 'return new ArrayIterator( $this->%s );';
        return array_merge(
            DocBlockMgr::factory( self::RETURN_T, $RETURNTYPE )
                ->setInfo( $SUMMARY, $DESCRIPTION )
                ->toArray(),
            FcnFrameMgr::init()
                ->setName( $FCNNAME )
                ->setReturnType( $RETURNTYPE )
                ->setBody( sprintf( $CODETMPL, $varDto->getName()))
                ->toArray()
        ); // end return array_merge
    }

    /**
     * @return array
     */
    private static function renderKeyMethod() {
        static $SUMMARY     = 'Return the key of the current element';
        static $DESCRIPTION = 'Required method implementing the Iterator interface';
        static $FCNNAME     = 'key';
        return array_merge(
            DocBlockMgr::factory( self::RETURN_T, self::INT_T )
                ->setInfo( $SUMMARY, $DESCRIPTION )
                ->toArray(),
            FcnFrameMgr::init()
                ->setName( $FCNNAME )
                ->setReturnType( self::INT_T )
                ->setReturnProperty( self::$POSITION )
                ->toArray()
        ); // end return array_merge
    }

    /**
     * @param VarDto $varDto
     * @return array
     */
    private static function renderLastMethod( VarDto $varDto ) {
        static $SUMMARY  = 'Move position to last element';
        static $FCNNAME  = 'last';
        static $CODETMPL = '$this->position = count( $this->%s ) - 1;';
        return array_merge(
            DocBlockMgr::factory( self::RETURN_T, self::STATIC_KW )
                ->setSummary( $SUMMARY )
                ->toArray(),
            FcnFrameMgr::init()
                ->setName( $FCNNAME )
                ->setBody( sprintf( $CODETMPL, $varDto->getName()))
                ->setReturnThis()
                ->toArray()
        ); // end return array_merge
    }

    /**
     * @return array
     */
    private static function renderNextMethod() {
        static $SUMMARY     = 'Move position forward to next element';
        static $DESCRIPTION = 'Required method implementing the Iterator interface';
        static $FCNNAME     = 'next';
        static $OPERATOR    = '+=';
        return array_merge(
            DocBlockMgr::factory( self::RETURN_T, self::STATIC_KW )
                ->setInfo( $SUMMARY, $DESCRIPTION )
                ->toArray(),
            FcnFrameMgr::init()
                ->setName( $FCNNAME )
                ->setBody(
                    AssignClauseMgr::init()
                        ->setTarget( self::THIS_KW, self::$POSITION )
                        ->setOperator( $OPERATOR )
                        ->setFixedSourceValue( 1 )
                        ->toString()
                )
                ->setReturnThis()
                ->toArray()
        ); // end return array_merge
    }

    /**
     * @return array
     */
    private static function renderPreviousMethod() {
        static $SUMMARY  = 'Move position backward to previous element';
        static $FCNNAME  = 'previous';
        static $OPERATOR = '-=';
        return array_merge(
            DocBlockMgr::factory( self::RETURN_T, self::STATIC_KW )
                ->setSummary( $SUMMARY )
                ->toArray(),
            FcnFrameMgr::init()
                ->setName( $FCNNAME )
                ->setBody(
                    AssignClauseMgr::init()
                        ->setTarget( self::THIS_KW, self::$POSITION )
                        ->setOperator( $OPERATOR )
                        ->setFixedSourceValue( 1 )
                        ->toString()
                )
                ->setReturnThis()
                ->toArray()
        ); // end return array_merge
    }

    /**
     * @return array
     */
    private static function renderRewindMethod() {
        static $SUMMARY     = 'Rewind the Iterator to the first element';
        static $DESCRIPTION = 'Required method implementing the Iterator interface';
        static $FCNNAME     = 'rewind';
        return array_merge(
            DocBlockMgr::factory( self::RETURN_T, self::STATIC_KW )
                ->setInfo( $SUMMARY, $DESCRIPTION )
                ->toArray(),
            FcnFrameMgr::init()
                ->setName( $FCNNAME )
                ->setBody(
                    AssignClauseMgr::init()
                        ->setTarget( self::THIS_KW, self::$POSITION )
                        ->setFixedSourceValue( 0 )
                        ->toString()
                )
                ->setReturnThis()
                ->toArray()
        ); // end return array_merge
    }

    /**
     * @param VarDto $varDto
     * @return array
     */
    private static function renderSeekMethod( VarDto $varDto ) {
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
            DocBlockMgr::init()
                ->setInfo( $SUMMARY, $DESCRIPTION )
                ->setTag( self::PARAM_T, self::INT_T, $argument->getName() )
                ->setTag( self::RETURN_T, self::VOID_KW )
                ->setTag( self::THROWS_T, self::$OoBException )
                ->toArray(),
            FcnFrameMgr::factory( $FCNNAME, self::$POSITION ) // no typed arg here..
                ->setBody(
                    VariableMgr::init()
                        ->setBaseIndent()
                        ->setName( $ERRVARNAME )
                        ->setVisibility()
                        ->setStatic( true )
                        ->setInitValue( $ERRVARTMPL )
                        ->toArray(),
                    explode( PHP_EOL, sprintf( $CODETMPL, $varDto->getName()))
                )
                ->toArray()
        ); // end return array_merge
    }

    /**
     * @param VarDto $varDto
     * @return array
     */
    private static function renderValidMethod( VarDto $varDto ) {
        static $SUMMARY     = 'Checks if current position is valid';
        static $DESCRIPTION = 'Required method implementing the Iterator interface';
        static $FCNNAME     = 'valid';
        static $CODETMPL    = 'return array_key_exists( $this->position, $this->%s );';
        return array_merge(
            DocBlockMgr::factory( self::RETURN_T, self::BOOL_T )
                ->setInfo( $SUMMARY, $DESCRIPTION )
                ->toArray(),
            FcnFrameMgr::init()
                ->setName( $FCNNAME )
                ->setReturnType( self::BOOL_T )
                ->setBody( sprintf( $CODETMPL, $varDto->getName()))
                ->toArray()
        ); // end return array_merge
    }

}
