[comment]: # (This file is part of PcGen, PHP Code Generation support package. Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence GPL 3.0)
<a name="top"></a>

#### PcGen summary

The PHP Code Generation support package

[ClassMgr] generate PHP class, interface and trait code
* with namespace, use, extends, implements 
* with constuctor and factory methods 
* with constants and properties with opt. getter(+iterator) and setter methods
* allow insert of (pre-produced, logic) code
* [ClassMgr example usage](#classmgr-example-usage) 

[DocBlockMgr] generates docBlocks
* in compliance with [phpdoc]
* [DocBlockMgr example usage](#docblockmgr-example-usage)

[FcnFrameMgr] generate PHP function/method frame (shell) code
* with arguments and closure use variables
* with property(/variable) set code
* with method return code
* allow insert of (pre-produced, logic) code 
* [FcnFrameMgr example usage](#fcnframemgr-example-usage)

For function/method invoke, opt with argument
* [FcnInvokeMgr] master single function/method invoke
  * sets of chained invokes by [ChainInvokeMgr]
* [FcnInvokeMgr example usage](#fcninvokemgr-example-usage)
  
[VariableMgr] generate PHP variable (and [PropertyMgr] property) code
* supports property/variable/constant define with PHP primitive value, array, closure or callback 
* allow insert of closure (pre-produced, logic) code 

[VarDto] holds variable base data, [ArgumentMgr] extends [VarDto], used for function/method base arguments.
   
[AssignClauseMgr] assign target (variable/property) value from
* variable/property value
* (scalar) fixedSourceValue
* PHP expression
* constant
* function/method invoke(s)
    
[ReturnClauseMgr] manages function/method return of 
* variable/property value
* (scalar) fixedSourceValue
* PHP expression
* constant  
* function/method invoke(s)
   
[PcGenInterface]
* provide convenient constants
  
[Misc](#misc) - [Tests](#tests) - [Support](#support) - [Install](#install) - [License]("license)
 
---

<a name="classmgr-example-usage"></a>

###### ClassMgr example usage
 
[ClassMgr] generate PHP class/interface/trait code
``` php
<?php
namespace Kigkonsult\PcGen;

$result = ClassMgr::init( PHP_EOL, '    ' ) // defaults

    // set namespace
    ->setNamespace( 'AcmeCorp' )

    // set class use clauses
    ->setUses(
        [
            [ 'some\name\space\SomeClass', 'alias1' ],
            [ 'another\name\space\AnotherClass' ]
        ]
    )

    // set class name
    ->setName( 'HalloWorld' )

    // set class extends
    ->setExtend( 'alias1' )

    // set class implements
    ->setImplements(
        [
            'Interface1',
            'Interface2'
        ]
    )

    // produce a class constructor
    ->setConstruct( true )

    // produce a class (static) factory method
    ->setFactory( true )

    // set some properties, each with name, varType, initValue, summary, description, makeGetter, makeSetter, argInFactory
    ->setProperties(
        [
            PropertyMgr::factory( 'constant1' )
                ->setInitValue( 'constant1' ),
                ->setIsConst( true )

            PropertyMgr::factory( 'static2' )
                ->setInitValue( [ 'one', 'two' ] ),
                ->setStatic( true )
                ->setVisibility( ClassMgr::PRIVATE_ )

            PropertyMgr::factory( 'prop1', 'AnotherClass', null, 'prop1 summary', 'prop1 description' )
                ->setMakeGetter( true ),
                ->setMakeSetter( true ),
                ->setArgInFactory( true )

            [ 'prop2', ClassMgr::ARRAY_T, ClassMgr::ARRAY2_T, 'prop2 summary', 'prop2 description', true, true, true ],

        ]
    )

    // set some (pre-produced) code 
    ->setBody(
        ' /* body row 1 */',
        [
            ' /* body row 2 */',
            ' /* body row 3 */',
            ' /* body row 4 */',
        ]
    )

    // string output (with row trailing eols)
    ->toString();
```
Result :
``` php
namespace AcmeCorp;

use some\name\space\SomeClass as alias1;
use another\name\space\AnotherClass;

/**
 * Class HalloWorld
 *
 * @package AcmeCorp
 */
class HalloWorld
     extends alias1
     implements Interface1, Interface2
{
    /**
     * const string
     */
    const CONSTANT1 = 'constant1';

    /**
     * @var array
     */
    private static $static2 = [
        'one',
        'two',
    ];

    /**
     * prop1 summary
     *
     * prop1 description
     *
     * @varDto AnotherClass $prop1
     */
    private $prop1 = null;

    /**
     * prop2 summary
     *
     * prop2 description
     *
     * @varDto array $prop2
     */
    private $prop2 = [];

    /**
     * Class HalloWorld constructor
     */
    public function __construct() {
    }

    /**
     * Class HalloWorld factory method
     *
     * @param AnotherClass $prop1
     * @param array $prop2
     * @return static
     */
    public static function factory( AnotherClass $prop1, array $prop2 = [] ) {
        $instance = new static();
        $instance->setProp1( $prop1 );
        $instance->setProp2( $prop2 );
        return $instance;
    }

     /* body row 1 */
     /* body row 2 */
     /* body row 3 */
     /* body row 4 */

    /**
     * @return AnotherClass
     */
    public function getProp1() {
        return $this->prop1;
    }

    /**
     * @param  AnotherClass $prop1
     * @return static
     */
    public function setProp1( AnotherClass $prop1 ) {
        $this->prop1 = $prop1;
        return $this;
    }

    /**
     * @return array
     */
    public function getProp2() {
        return $this->prop2;
    }

    /**
     * @param  mixed $prop2
     * @return static
     */
    public function appendProp2( $prop2 ) {
        $this->prop2[] = $prop2;
        return $this;
    }

    /**
     * @param  array $prop2
     * @return static
     */
    public function setProp2( array $prop2 = [] ) {
        $this->prop2 = $prop2;
        return $this;
    }
}
```
Methods in details are found in [ClassMgr] docs.
ClassMgr uses [DocBlockMgr], [FcnFrameMgr] and ([VariableMgr]/)[PropertyMgr] and for data, [VarDto]/[ArgumentDto], below.
You will find more examples in test/ClassMgrTest.php.

<small>Go to [Top](#top).</small>

---

<a name="docblockmgr-example-usage"></a>

###### DocBlockMgr example usage

[DocBlockMgr] generates docBlocks
``` php
<?php
namespace Kigkonsult\PcGen;

$result = DocBlockMgr::init()

    // set indent to 0, default four spaces
    ->setIndent()

    // set eol, default PHP_EOL
    ->setEol( PHP_EOL )

    // set top summary
    ->setSummary( 'This is a top (shorter) summary' )

    // set longer description (string)
    ->setDescription( 'This is a longer description' )

    // set another longer description (array)
    ->setDescription( 
        [
            'This is another longer description', 
            'with more info on the next row'
        ]
    ) 

    // set tags using PcGenInterface constants
    ->setTag( DocBlockMgr::RETURN_T, DocBlockMgr::ARRAY_T )
    ->setTag( DocBlockMgr::PACKAGE_T, __NAMESPACE__ )

    // string output (with row trailing eols)
    ->toString();

    // array output (no row trailing eols)
    // ->toArray();

```
The same as above but shorter :
``` php
<?php
namespace Kigkonsult\PcGen;
 
$result = DocBlockMgr::init()
    ->setIndent()
    ->setSummary( 'This is a top (shorter) summary' )
    ->setDescription( 'This is a longer description' )
    ->setDescription( 
        [
            'This is another longer description', 
            'with more info on the next row'
        ]
    ) 
    ->setTag( DocBlockMgr::RETURN_T, DocBlockMgr::ARRAY_T )
    ->setTag( DocBlockMgr::PACKAGE_T, __NAMESPACE__ )
    ->toString();
```
Result :
``` php
/**
 * This is a top (shorter) summary
 *
 * This is a longer description
 *
 * This is another longer description
 * with more info on the next row
 *
 * @return  array
 * @package Kigkonsult\PcGen
 */
```

Methods in details are found in [DocBlockMgr].

You will find more usage examples of DocBlockMgr and [FcnFrameMgr] in src/ClassMethodFactory.php (and test/DocBlockMgrTest.php).

<small>Go to [Top](#top).</small>

---

<a name="fcnframemgr-example-usage"></a>

###### FcnFrameMgr example usage
 
[FcnFrameMgr] generate PHP function/method frame (shell) code
``` php
<?php
namespace Kigkonsult\PcGen;

$result = FcnFrameMgr::init() // eol/indent default

    // set visibility, public default
    ->setVisibility( FcnFrameMgr::PUBLIC_ )

    // set function name (all but closure)
    ->setName( 'theFunctionName' )
    
    // add a method argument
    ->addArgument( 'argument' )
    // add another method argument, name, varType, default, by-reference
    ->addArgument( 'argument2', FcnFrameMgr::ARRAY_T, FcnFrameMgr::ARRAY2_T, true )

    // set some logic
    ->setBody( ' /* her comes some logic */' );

    // set method return 'return $this;'
    ->setReturnThis();

    // set method return 'return $this->argument;'
    // ->setReturnProperty( 'argument' );

    // set method return 'return true;'
    // ->setReturnFixedValue( true );

    // set method return 'return $argument;'
    // ->setReturnVariable( 'argument' );

    // string output (with row trailing eols)
    >toString();

    // array output (no row trailing eols)
    // ->toArray();

```
The same as above but shorter :
``` php
<?php
namespace Kigkonsult\PcGen;
 
$result = FcnFrameMgr::init()
    ->setName( 'theFunctionName' )
    ->setArguments(
        [
            'argument',
            [ 'argument2', FcnFrameMgr::ARRAY_T, FcnFrameMgr::ARRAY2_T, true ]
        ]
    ->setBody( ' /* her comes some logic */' );
    ->setReturnThis( true );
    >toString();
```
Result :
``` php
    public function theFunctionName( $argument, array & $argument2 = [] ) {
         /* her comes some logic */
        return $this;
    }
```

Methods in details are found in [FcnFrameMgr].
FcnFrameMgr uses for data [VarDto]/[ArgumentDto].
You will find more usage examples of [DocBlockMgr] and FcnFrameMgr in src/ClassMethodFactory.php (and test/FcnFrameMgrTest.php).

<small>Go to [Top](#top).</small>

---

<a name="fcninvokemgr-example-usage"></a>

###### FcnInvokeMgr example usage
 
[FcnInvokeMgr] master single function/method invokes, for chained invokes, use [ChainInvokeMgr].
``` php
<?php
namespace Kigkonsult\PcGen;
 
$output = FcnInvokeMgr::()
    ->setname( FcnInvokeMgr::THIS_KW,  'method' )
    ->setArgument( [ 'arg1', 'arg2' ] )
    ->toString();
```

Result :
``` php
$this->method( $arg1, $arg2 )
```

Methods in details are found in [FcnInvokeMgr] and [ChainInvokeMgr],
used by [AssignClauseMgr]/[ReturnClauseMgr]. 
FcnInvokeMgr is supported by [EntityMgr] with source management.
You will find more examples in test/FcnInvokeMgrTest.php.

<small>Go to [Top](#top).</small>

---

<a name="variablemgr-example-usage"></a>

###### VariableMgr example usage
 
[VariableMgr] generate PHP variable (and [PropertyMgr] property) define code 
``` php
<?php
namespace Kigkonsult\PcGen;
 
$output = VariableMgr::init() // eol/indent default

    // visibility, default public
    ->setVisibility( VariableMgr::PRIVATE_ )

    // default false
    ->setStatic( true )

    // valid name
    ->setName( 'variable' )

    // value to initialize variable/property/constant
    ->setInitValue( [ 'one', 'two' ] )

    // string output (with row trailing eols)
    >toString();

    // array output (no row trailing eols)
    // ->toArray();
```
The same as above but shorter :
``` php
<?php
namespace Kigkonsult\PcGen;
 
$output = VariableMgr::init()
    ->setVisibility( VariableMgr::PRIVATE_ )
    ->setStatic( true )
    ->setName( 'variable' )
    ->setInitValue( [ 'one', 'two' ] )
    ->toString();
```

Result :
``` php
    private static $variable = [
        'one',
        'two',
    ];
```
Methods in details are found in [VariableMgr].
[PropertyMgr] extends [VariableMgr], used in [ClassMgr], managing properties.
[VariableMgr] and [PropertyMgr] uses for data, [VarDto]/[ArgumentDto].
You will find more examples in test/VariableMgrTest.php.

VarDto methods in details are found in [VarDto], ArgumentDto methods in [ArgumentDto].

<small>Go to [Top](#top).</small>

--- 

<a name="misc"></a>

###### Misc

The target PHP version code is, for now, the current PHP version. 

Using a PHP reserved name as _name_ (ex FQCN/className) will thow an InvalidArgumentException. 

You may need to readjust result output code style and indents.

<a name="tests"></a>

###### Tests

Tests are executed in ```DISPLAY``` mode, to alter, update _PHP_ const in top of ```phpunit.xml```.  

<a name="support"></a>

###### Support

For support use [github.com PcGen]. Non-emergence support issues are, unless sponsored, fixed in due time.

<a name="sponsorship"></a>

###### Sponsorship

Donation using <a href="https://paypal.me/kigkonsult?locale.x=en_US" rel="nofollow">paypal.me/kigkonsult</a> are appreciated. 
For invoice, <a href="mailto:ical@kigkonsult.se">please e-mail</a>.

<a name="install"></a>

###### Install

``` php
composer require kigkonsult/pcgen:dev-master
```

Composer, in your `composer.json`:

``` json
{
    "require": {
        "kigkonsult/pcgen": "dev-master"
    }
}
```

Otherwise , download and acquire..

``` php
namespace Kigkonsult\PcGen;
...
include 'pathToSource/Kigkonsult/PcGen/autoload.php';
```

<a name="license"></a>

###### License

This project is licensed under the GPLv3 License

---
<small>Return to [README]</small>

[ArgumentDto]:ArgumentDto.md
[AssignClauseMgr]:AssignClauseMgr.md
[ChainInvokeMgr]:ChainInvokeMgr.md
[ClassMgr]:ClassMgr.md
[Composer]:https://getcomposer.org/
[DocBlockMgr]:DocBlockMgr.md
[EntityMgr]:EntityMgr.md
[FcnFrameMgr]:FcnFrameMgr.md
[FcnInvokeMgr]:FcnInvokeMgr.md
[github.com PcGen]:https://github.com/iCalcreator/PcGen
[PcGenInterface]:src/PcGenInterface.php
[phpdoc]:https://phpdoc.org
[PropertyMgr]:PropertyMgr.md
[README]:../README.md
[ReturnClauseMgr]:ReturnClauseMgrmd   
[VarDto]:VarDto.md
[VariableMgr]:VariableMgr.md
