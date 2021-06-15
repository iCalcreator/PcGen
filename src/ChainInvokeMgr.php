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
use function count;
use function rtrim;
use function str_replace;
use function trim;
use function sprintf;

final class ChainInvokeMgr extends BaseA
{
    /**
     * @var FcnInvokeMgr[]
     */
    private $invokes = [];

    /**
     * @var string
     */
    private $invokeClass = null;

    /**
     * @param FcnInvokeMgr ...$args
     * @return static
     */
    public static function factory( ...$args ) : self
    {
        return self::init()->setInvokes( $args );
    }

    /**
     * @inheritDoc
     * @throws RuntimeException
     */
    public function toArray() : array
    {
        static $ERR = 'No function directives';
        if( ! $this->isInvokesSet()) {
            throw new RuntimeException( $ERR );
        }
        $cnt1 = count( $this->invokes );
        $code = [ rtrim( $this->invokes[0]->toString()) ];
        if( 1 == $cnt1 ) {
            return $code;
        }
        $repl = Util::setVarPrefix( $this->invokeClass );
        $ind  = $this->getBaseIndent() . $this->getIndent();
        for( $ifx = 1; $ifx < $cnt1; $ifx++ ) { // all next displ without class, now has all $-prefix
            $invoke = $this->invokes[$ifx]->toArray();
            $code[] = $this->getIndent() .
                str_replace( $repl, self::SP0, trim( $invoke[0] ));
            $cnt2   = count( $invoke );
            if( 1 == $cnt2 ) {
                continue;
            }
            $cnt2  -= 1;
            for( $iVx = 1; $iVx < $cnt2; $iVx++ ) {
                $code[] = $ind . trim( $invoke[$iVx] );
            }
            $code[] = $this->getBaseIndent() . trim( $invoke[$iVx] ); // last invoke row
        } // end for
        return $code;
    }

    /**
     * @return FcnInvokeMgr[]
     */
    public function getInvokes() : array
    {
        return $this->invokes;
    }

    /**
     * @return bool
     */
    public function isInvokesSet() : bool
    {
        return ( ! empty( $this->invokes ));
    }

    /**
     * Append FcnInvokeMgr with chained invoke, support one-liners
     *
     * First must have "class" : parent, self, $this, 'otherClass', '$class' when next is set
     * Next must have $this, 'otherClass', '$class'
     * For all but first, first  "class"  is set as 'sourceClass'
     *
     * @param FcnInvokeMgr $invoke
     * @return static
     * @throws InvalidArgumentException
     */
    public function appendInvoke( FcnInvokeMgr $invoke ) : self
    {
        static $ERR1 = 'No invoke class, first invoke \'%s\', next (#%d) \'%s\'';
        static $ERR2 = 'First invoke \'%s\', invalid next (#%d) \'%s\'';
        switch( true ) {
            case empty( $this->invokes ) : // first invoke, accepts all
                $this->invokeClass = $invoke->getName()->getClass();
                break;
            case empty( $this->invokeClass ) :
                throw new InvalidArgumentException(
                    sprintf( $ERR1,
                        trim( $this->invokes[0]->toString()),
                        ( count( $this->invokes ) + 1 ),
                        trim( $invoke->toString())
                    )
                );
            case ( ! $invoke->getName()->isClassSet()) : // force same class as first
                $invoke->getName()->setClass( Util::setVarPrefix( $this->invokeClass ));
                break;
            case ( ! self::evaluateClass( $invoke->getName()->getClass())) :
                throw new InvalidArgumentException(
                    sprintf( $ERR2,
                        trim( $this->invokes[0]->toString()),
                        ( count( $this->invokes ) + 1 ),
                        trim( $invoke->toString())
                    )
                );
            default : // next invoke, force same class as first
                $invoke->getName()->setClass( Util::setVarPrefix( $this->invokeClass ));
                break;
        } // end switch
        $this->invokes[] = $invoke->rig( $this );
        return $this;
    }

    /**
     * Most (all?) errors here already catched by FcnInvokeMgr/EntityMgr
     *
     * @param string $invokeClass
     * @return bool
     */
    private static function evaluateClass( string $invokeClass ) : bool
    {
        if( empty( $invokeClass )) {
            return false;
        }
        if( self::THIS_KW == $invokeClass ) {
            return true;
        }
        $invokeClass = Util::unSetVarPrefix( $invokeClass );
        try {
            Assert::assertFqcn( $invokeClass );
            return true;
        }
        catch( InvalidArgumentException $e ) {}
        return false;
    }

    /**
     * @param FcnInvokeMgr[] $invokes
     * @return static
     * @throws InvalidArgumentException
     */
    public function setInvokes( array $invokes ) : self
    {
        $this->invokes     = [];
        $this->invokeClass = null;
        foreach( array_keys( $invokes ) as $fIx ) {
            $this->appendInvoke( $invokes[$fIx] );
        }
        return $this;
    }
}
