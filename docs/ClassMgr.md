[comment]: # (This file is part of PcGen, PHP Code Generation support package. Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence GPL 3.0)

#### ClassMgr

The ```ClassMgr``` class manages PHP class(default), interface, trait 

* namespace
* class use
* extends and interfaces
* opt. construct and factory method
* properties opt. with getter and setter methods 
* using [DocBlockMgr], [FcnFrameMgr], [PropertyMgr] and [FcnInvokeMgr] doing the hard work

###### Methods

---
Inherited [Common methods]

---

```ClassMgr::toArray() ```

* Return _array_, class code rows (null-bytes removed), no trailing eol
* Throws _RuntimeException_


```ClassMgr::toString() ```

* Return _string_ with code rows (extends toArray), each code row with trailing eol
* Throws _RuntimeException_

---

```ClassMgr::getTargetType() ```

* Return _string_  - ```class```/```interface```/```trait```
* Throws _InvalidArgumentException_


```ClassMgr::setClass() ```

* Produce a class (default)
* Return _static_


```ClassMgr::setInterface() ```

* Produce an interface
* Return _static_


```ClassMgr::setTrait()```

* Produce a trait
* Return _static_

---

```ClassMgr::setNamespace( namespace )```

* Set namespace for class
* ```namespace``` _string_  
* Return _static_
* Throws _InvalidArgumentException_

---

```ClassMgr::addUse( fqcn [, alias[, type ]] )```

* ```fqcn``` _string_ (fqcn) class/-constant/-function
* ```alias``` _string_ opt
* ```type``` _string__ one of ```ClassMgr::CLASS_``` (default),
 ```ClassMgr::CONST_```, ```ClassMgr::FUNC_```
* Return _static_
* Throws _InvalidArgumentException_


```ClassMgr::setUses( useSet )```

* ```useSet``` _array_, each array element : array( fqcn \[, alias \[, type ] ] ), se ```ClassMgr::addUse()``` 
* Return _static_
* Throws _InvalidArgumentException_

---

DocBlock is always set up up with

* name (if summary not set)
* package tag (if not set : namespace)


```ClassMgr::getDocBlock()```

* Return _DocBlockMgr_


```ClassMgr::isDocBlockSet()```

* Return _bool_ true if set, false not


```ClassMgr::setDocBlock( docBlock )```

* ```docBlock``` _DocBlockMgr_
* Return _static_
* Throws _InvalidArgumentException_

---

```ClassMgr::isAbstract()```

* Return _bool_ true if class is abstract, false not


```ClassMgr::setAbstract( abstract )```

* ```abstract``` _bool_ true, class is abstract, false, not (default)
* Return _static_


---

```ClassMgr::getExtends()```

* Return _string_


```ClassMgr::isExtendsSet()```

* Return _bool_ true, if set, false not


```ClassMgr::setExtends( extend )```

* ```extend``` _string_
* Return _static_
* Throws _InvalidArgumentException_

---

```ClassMgr::addImplement( implement )```

* ```implement``` _string_
* Return _static_
* Throws _InvalidArgumentException_


```ClassMgr::setImplements( implementSet )```

* ```implementSet``` _array_, string[], fqcn's
* Return _static_
* Throws _InvalidArgumentException_

---

```ClassMgr::setConstruct( construct )```

* Initiated to _false_, no construct method
* ```construct``` _bool_, true (default) : directive to generate (empty) class constructor 
* Return _static_


```ClassMgr::setFactory( factory )```

* Initiated to _false_, no factory method
* ```factory``` _bool_, true (default) : directive to generate class factory
   default no body
   populated depending on property set attribute ```argInFactory```, below 
* Return _static_
* Static

---

```ClassMgr::addProperty( property )```

* ```property``` [PropertyMgr]
   note ```PropertyMgr``` below
   opt with set directives for _getter_, _setter_, _argInFactory_, below 
* Return _static_


```ClassMgr::addProperty( varDto [, getter [, setter [, argInFactory ]]] )```

* ```varDto``` [VarDto]
   note *VarDto* below
* ```getter``` _bool_, default false, directive to generate getter methods for property (below)
   if single array class property, _Iterator_ is implemented (below)
* ```setter``` _bool_, default false, directive to generate setter method(s) for property (below)
* ```argInFactory``` _bool_, default false, directive to use property as argument and value set in class factory method<br> only if ```ClassMgr::setFactory( true )```
* Return _static_
* Throws _InvalidArgumentException_


```ClassMgr::addProperty( name [, varType [, default [, summary [, description [, getter [, setter [, argInFactory ]]]]]]] )```

* ```name``` _string_
* ```varType``` _string_, default null
   convenient constants found in PcGenInterface
* ```default``` _mixed_, default null
   convenient constants found in PcGenInterface
* ```summary``` _string__, default null
* ```description``` _string|array_, default null
* ```getter``` _bool_, default false, directive to generate getter methods for property (below)
   if single array class property, _Iterator_ is implemented (below)
* ```setter``` _bool_, default false, directive to generate setter method(s) for property (below)
* ```argInFactory``` _bool_, default false, directive to use property as argument and value set in class factory method
   only if ```ClassMgr::setFactory( true )```
* Return _static_
* Throws _InvalidArgumentException_


```ClassMgr::setProperties( propertySet )```

* ```propertySet``` _array_, elements any of below 
   _PropertyMgr_  note ```PropertyMgr``` below,   opt with set directives _getter_, _setter_, _argInFactory_, above 
  _array( _VarDto_ [, getter [, setter [, argInFactory ]]] )_, note ```VarDto``` below, _getter_, _setter_, _argInFactory_, above
  _array( name [, varType [, default [, summary [, description [, getter [, setter [, argInFactory ]]]]]]] )_ note ```ClassMgr::addProperty()``` above
* Return _static_
* Throws _InvalidArgumentException_

---

```ClassMgr::setBody( ...body )```

* ```body``` _string|array_, (multiple) class (methods) logic code (chunks) row(s)
   note, code without 'baseIndent' 
* Return _static_

---


#### Misc

For class property with the ```getter``` attribute set (true default), results in (property-)methods

* property type bool
   _is<Property\>_, Return bool
   
* property type non-bool
   _get<Property\>_, Return property value
   _is<Property\>Set_, Return bool true if set
   
* property type array has also
   _count<Property\>_, Return (int) number of array elements

For class with a single array property, in substitute of ```getter```-methods, 
_SeekableIterator_, _Countable_ and _IteratorAggregate_ interfaces are supported 
with property methods

* _count_, Return (int) count of elements
   _Countable_ method
* _current_, Return (mixed) the current element
   _Iterator_ method
* _exists_, Checks if given (arg) position is set, return bool
* _GetIterator_, Retrieve an external iterator, returning _Traversable_ 
   _IteratorAggregate_ method 
* _key_, Return the (int) key of the current element
   _Iterator_ method
* _last_, Move position to last element, return _static_
* _next_, Move position forward to next element, return _static_
   _Iterator_ methods
* _previous_, Move position backward to previous element, return _static_
* _rewind_, Rewind the iterator to the first element, return _static_
   _Iterator_ method
* _seek_, Seeks to a given (arg) position in the iterator, throws OutOfBoundsException
   _SeekableIterator_ method
* _valid_, Checks if current position is valid, return bool
   _Iterator_ method

For class property with the ```setter``` attribute set (true default), results in (property-)methods

* _set<Property\>_, Set property value, return _static_
* _append<Property\>_, Append array property element value, return _static_
  array type property only
  
---
_PropertyMgr_ instance creation ([PropertyMgr])<br><br>
```PropertyMgr::factory( name [, type [, default [, summary [, description ]]]] )```

* ```name``` _string_, argument name ( with or without leading '$')
* ```varType``` _string_, variable type (type hint), default null
   convenient constants found in PcGenInterface
* ```default```, _mixed_, the argument value if null
* ```summary``` _string_, the [phpdoc] summary
* ```description``` _string_|_array_, the [phpdoc] description

_VarDto_ instance creation ([VarDto])<br><br>
```VarDto::factory( [ varName [, varType [ default [, summary [, description ]]]]] )```

* ```varName``` _string_
* ```varType``` _string_, variable type (type hint), default null
   convenient constants found in PcGenInterface
* ```default```, _mixed_, the argument value if null
* ```summary``` _string_, the [phpdoc] summary
* ```description``` _string_|_array_, the [phpdoc] description

---
#### Example 1

```
<?php

$code = ClassMgr::init()
    ->setName( 'TestClass' )
    ->addProperty(
        PropertyMgr::factory( 'propertyName', PropertyMgr::STRING_T )
    )
    ->toString()
```

Result :

```
/**
 * Class TestClass
 */
class TestClass
{
    /**
     * @var string
     */
    private $propertyName = null;

    /**
     * Return string propertyName
     *
     * @return string
     */
    public function getPropertyName() : string
    {
        return $this->propertyName;
    }

    /**
     * Return bool true if  propertyName  is set (i.e. not null)
     *
     * @return bool
     */
    public function isPropertyNameSet() : bool
    {
        return ( null !== $this->propertyName );
    }

    /**
     * Set propertyName
     *
     * @param  string $propertyName
     * @return static
     */
    public function setPropertyName( string $propertyName )
    {
        $this->propertyName = $propertyName;
        return $this;
    }
}
```


#### Example 2

```
<?php

$code = ClassMgr::init()
    ->setNamespace( __NAMESPACE__ )
    ->setName( 'TestClass' )
    ->setProperties(
        PropertyMgr::factory(
            'variable',
            ClassMgr::ARRAY_T,
            ClassMgr::ARRAY2_T,
            'An array of values'
        )
    )
    ->toString();

```

Result :

```

namespace Kigkonsult\PcGen;

use ArrayIterator;
use Countable;
use OutOfBoundsException;
use SeekableIterator;
use Traversable;

/**
 * Class TestClass
 *
 * @package Kigkonsult\PcGen
 */
class TestClass
     implements SeekableIterator, Countable
{
    /**
     * An array of values
     *
     * @var array
     */
    private $variable = [];

    /**
     * Iterator index
     *
     * @var int
     */
    private $position = 0;

    /**
     * Return count of variable elements
     *
     * Required method implementing the Countable interface
     *
     * @return int
     */
    public function count() : int
    {
        return count( $this->variable );
    }

    /**
     * Return the current element
     *
     * Required method implementing the Iterator interface
     *
     * @return mixed
     */
    public function current()
    {
        return $this->variable[$this->position];
    }

    /**
     * Checks if position is set
     *
     * @param  int $position
     * @return bool
     */
    public function exists( int $position ) : bool
    {
        return array_key_exists( $position, $this->variable );
    }

    /**
     * Retrieve an external iterator
     *
     * Method implementing the IteratorAggregate interface,
     * returning Traversable, i.e. makes the class traversable using foreach.
     * Usage : 'foreach( $class as $value ) { .... }'
     *
     * @return Traversable
     */
    public function GetIterator() : Traversable
    {
        return new ArrayIterator( $this->variable );
    }

    /**
     * Return the key of the current element
     *
     * Required method implementing the Iterator interface
     *
     * @return int
     */
    public function key() : int
    {
        return $this->position;
    }

    /**
     * Move position to last element
     *
     * @return static
     */
    public function last()
    {
        $this->position = count( $this->variable ) - 1;
        return $this;
    }

    /**
     * Move position forward to next element
     *
     * Required method implementing the Iterator interface
     *
     * @return static
     */
    public function next()
    {
        $this->position += 1;
        return $this;
    }

    /**
     * Move position backward to previous element
     *
     * @return static
     */
    public function previous()
    {
        $this->position -= 1;
        return $this;
    }

    /**
     * Rewind the Iterator to the first element
     *
     * Required method implementing the Iterator interface
     *
     * @return static
     */
    public function rewind()
    {
        $this->position = 0;
        return $this;
    }

    /**
     * Seeks to a given position in the iterator
     *
     * Required method implementing the SeekableIterator interface
     *
     * @param  int $position
     * @return void
     * @throws OutOfBoundsException
     */
    public function seek( $position )
    {
        static $ERRTXT = "Position %d not found!";
        if( ! array_key_exists( $position, $this->variable )) {
            throw new OutOfBoundsException( sprintf( $ERRTXT, $position ));
        }
        $this->position = $position;
    }

    /**
     * Checks if current position is valid
     *
     * Required method implementing the Iterator interface
     *
     * @return bool
     */
    public function valid() : bool
    {
        return array_key_exists( $this->position, $this->variable );
    }

    /**
     * Append an variable array element
     *
     * @param  mixed $variable
     * @return static
     */
    public function appendVariable( $variable )
    {
        $this->variable[] = $variable;
        return $this;
    }

    /**
     * Set variable
     *
     * @param  array $variable
     * @return static
     */
    public function setVariable( array $variable = [] )
    {
        $this->variable = $variable;
        return $this;
    }
}
```

----

<small>Return to [README] - [Summary]</small>

[ArgumentDto]:ArgumentDto.md
[FcnFrameMgr]:FcnFrameMgr.md
[FcnInvokeMgr]:FcnInvokeMgr.md
[Common methods]:CommonMethods.md
[DocBlockMgr]:DocBlockMgr.md
[phpdoc]:https://phpdoc.org
[PropertyMgr]:PropertyMgr.md
[README]:../README.md
[Summary]:Summary.md
[VarDto]:VarDto.md
