<?php
/**
 * PcGen is the PHP Code Generation support package
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
/**
 * Kigkonsult\PcGen autoloader
 */
spl_autoload_register(
    function( $class ) {
        static $PREFIX = 'Kigkonsult\\PcGen\\';
        static $BS = '\\';
        static $PATHSRC = null;
        static $SRC = 'src';
        static $PATHTEST = null;
        static $TEST = 'test';
        static $FMT = '%s%s.php';
        if( empty( $PATHSRC ) ) {
            $PATHSRC  = __DIR__ . DIRECTORY_SEPARATOR . $SRC . DIRECTORY_SEPARATOR;
            $PATHTEST = __DIR__ . DIRECTORY_SEPARATOR . $TEST . DIRECTORY_SEPARATOR;
        }
        if( 0 != strncmp( $PREFIX, $class, 17 ) ) {
            return;
        }
        $class = substr( $class, 17 );
        if( false !== strpos( $class, $BS ) ) {
            $class = str_replace( $BS, DIRECTORY_SEPARATOR, $class );
        }
        $file = sprintf( $FMT, $PATHSRC, $class );
        if( file_exists( $file ) ) {
            include $file;
        }
        else {
            $file = sprintf( $FMT, $PATHTEST, $class );
            if( file_exists( $file ) ) {
                include $file;
            }
        }
    }
);
