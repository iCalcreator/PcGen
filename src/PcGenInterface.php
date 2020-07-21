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

interface PcGenInterface
{
    /**
     * PHP visibilities
     */
    const PUBLIC_           = 'public';
    const PROTECTED_        = 'protected';
    const PRIVATE_          = 'private';

    /**
     * PHP types (varType hints)
     */
    const ARRAY_T           = 'array';     // PHP 5.1 varType hint
    const BOOL_T            = 'bool';      // PHP 7.0 varType hint
    const CALLABLE_T        = 'callable';  // PHP 5.4 varType hint
    const FLOAT_T           = 'float';     // PHP 7.0 varType hint
    const INT_T             = 'int';       // PHP 7.0 varType hint
    const ITERABLE_T        = 'iterable';  // PHP 7.1 varType hint
    const NULL_T            = 'null';
    const RESOURCE_T        = 'resource';
    const STRING_T          = 'string';    // PHP 7.0 varType hint

    /**
     * PHP types as array
     */
    const ARRAY2_T          = '[]';
    const BOOLARRAY_T       = 'bool[]';
    const CALLABLEARRAY_T   = 'callable[]';
    const FLOATARRAY_T      = 'float[]';
    const INTARRAY_T        = 'int[]';
    const RESOURCEARRAY_T   = 'resource[]';
    const STRINGARRAY_T     = 'string[]';

    /**
     * PHP keywords (varType hints)
     */
    const FALSE_KW          = 'false';
    const MIXED_KW          = 'mixed';
    const OBJECT_KW         = 'object';    // also PHP 7.2 varType hint
    const PARENT_KW         = 'parent';
    const SELF_KW           = 'self';      // also PHP 5.0 varType hint
    const STATIC_KW         = 'static';
    const THIS_KW           = '$this';
    const TRUE_KW           = 'true';
    const VOID_KW           = 'void';

    /**
     * PhpDoc tags
     */
    const API_T             = 'api';
    const AUTHOR_T          = 'author';
    const CATEGORY_T        = 'category';
    const COPYRIGHT_T       = 'copyright';
    const DEPRECATED_T      = 'deprecated';
    const EXAMPLE_T         = 'example';
    const FILESOURCE_T      = 'filesource';
    const GLOBAL_T          = 'global';
    const IGNORE_T          = 'ignore';
    const INHERITDOC_T      = 'inheritDoc';
    const INTERNAL_T        = 'internal';
    const LICENCE_T         = 'license';
    const LINK_T            = 'link';
    const METHOD_T          = 'method';
    const PACKAGE_T         = 'package';
    const PARAM_T           = 'param';
    const PROPERTY_T        = 'property';
    const PROPERTY_READ_T   = 'property-read';
    const PROPERTY_WRITE_T  = 'property-write';
    const RETURN_T          = 'return';
    const SEE_T             = 'see';
    const SINCE_T           = 'since';
    const SOURCE_T          = 'source';
    const SUBPACKAGE_T      = 'subpackage';
    const THROWS_T          = 'throws';
    const TODO_T            = 'todo';
    const USES_T            = 'uses';
    const USED_BY_T         = 'used-by';
    const VAR_T             = 'var';
    const VERSION_T         = 'version';

    /**
     * Misc
     */
    const BOOLEAN_T         = 'boolean';
    const BOOLEANARRAY_T    = 'boolean[]';
    const CONST_            = 'const';
    const DOUBLE            = 'double';
    const FACTORY           = 'factory';
    const INTEGER           = 'integer';
    const SP0               = '';
    const SP1               = ' ';
    const VARPREFIX         = '$';
}
