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
use Kigkonsult\PcGen\Traits\ArgumentTrait;
use RuntimeException;

/**
 * Class FcnInvokeMgr
 *
 * Manages method/function invokes, opt with arguments, no support for dynamic methodNames, $this->{$method}
 *
 * @package Kigkonsult\PcGen
 */
final class FcnInvokeMgr extends BaseA
{
    /**
     * @var string
     */
    private static $ERR = 'Not initiated, no (class+)name set !!';

    /**
     * @var EntityMgr
     */
    private $name = null;

    /**
     * @param EntityMgr|string $class  string : one of null, parent, self, $this, 'otherClass', '$class'
     * @param string           $fcnName
     * @param array            $arguments
     * @return static
     */
    public static function factory( $class, $fcnName, array $arguments = null )
    {
        $instance = self::init()->setName( $class, $fcnName );
        if( ! empty( $arguments )) {
            $instance->setArguments( $arguments );
        }
        return $instance;
    }

    /**
     * Return code as array (with NO eol at line endings), no row trailing ';'
     *
     * @return array
     * @throws RuntimeException
     */
    public function toArray()
    {
        static $ERR = 'No function directives';
        if(( null === $this->name ) && empty( $this->getArgumentCount())) {
            throw new RuntimeException( $ERR );
        }
        $row = $this->getName()->toString();
        foreach( array_keys( $this->arguments ) as $argIx ) {
            $this->arguments[$argIx]
                ->setByReference( false )
                ->setVarType()
                ->setDefault();
        } // end foreach
        $code = $this->renderArguments( $row );
        return Util::nullByteClean( $code );
    }

    /**
     * @return EntityMgr
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param EntityMgr|string $class  string : one of null, parent, self, $this, 'otherClass', '$class'
     * @param string           $fcnName
     * @return static
     * @throws InvalidArgumentException
     */
    public function setName( $class, $fcnName = null )
    {
        switch( true ) {
            case ( $class instanceof EntityMgr ) :
                $this->name = $class->setForceVarPrefix( false );
                break;
            case ( ! empty( $fcnName )) :
                $fcnName    = Assert::assertPhpVar( $fcnName ); // skip $-class-prefix
                $this->name = EntityMgr::init( $this )
                    ->setClass( $class )
                    ->setVariable( $fcnName )
                    ->setForceVarPrefix( false );
                break;
            default :
                throw new InvalidArgumentException(
                    sprintf(
                        self::$ERRx,
                        var_export( $fcnName, true ) .
                        self::SP1 .
                        var_export( $fcnName, true )
                    )
                );
                break;
        } // end switch
        return $this;
    }

    /**
     * Set method class
     *
     * @param string $class
     * @return static
     * @throws InvalidArgumentException
     */
    public function setClass( $class )
    {
        if( empty( $this->name )) {
            throw new RuntimeException( self::$ERR );
        }
        $this->name->setClass( $class );
        return $this;
    }

    use ArgumentTrait;

    /**
     * Set isStatic, only applicable for class '$class', ignored by the others
     *
     * @param bool $staticStatus
     * @return static
     * @throws InvalidArgumentException
     */
    public function setIsStatic( $staticStatus )
    {
        if( null === $this->name ) {
            throw new InvalidArgumentException( self::$ERR );
        }
        $this->getName()->setIsStatic((bool) $staticStatus );
        return $this;
    }
}
