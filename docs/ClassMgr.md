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

```ClassMgr::toArray()```
* Return _array_, class code rows (null-bytes removed), no trailing eol
* Throws RuntimeException

```ClassMgr::toString()```
* Return _string_ with code rows (extends toArray), each code row with trailing eol
* Throws RuntimeException
---

```ClassMgr::getTargetType()```
* Return _string_  - ```class```/```interface```/```trait```
* Throws InvalidArgumentException

```ClassMgr::setClass()```
* Produce a class (default)
* Return _static_

```ClassMgr::setInterface()```
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
* Throws InvalidArgumentException
---

```ClassMgr::addUse( fqcn [, alias ] )```
* ```fqcn``` _string_
* ```alias``` _string_
* Return _static_
* Throws InvalidArgumentException

```ClassMgr::setUses( useSet )```
* ```useSet``` _array_, each array element : array( fqcn [, alias ] )
* Return _static_
* Throws InvalidArgumentException
---
DocBlock is always set up up with
* name (if summary not set)
* package tag (if not set : namespace)

```ClassMgr::getDocBlock()```
* Return _DocBlockMgr_

```ClassMgr::isDocBlockSet()```
* Return _bool_ true, if docBlock is set, false, not

```ClassMgr::setDocBlock( docBlock )```
* ```docBlock``` _DocBlockMgr_
* Return _static_
* Throws InvalidArgumentException
---

```ClassMgr::isAbstract()```
* Return _bool_ true, if class is abstract, false, not

```ClassMgr::setAbstract( abstract )```
* ```abstract``` _bool_ true, class is abstract, false, not (default)
* Return _static_
---

```ClassMgr::getExtends()```
* Return _string_

```ClassMgr::isExtendsSet()```
* Return _bool_ true, if extends is set, false, not

```ClassMgr::setExtends( extend )```
* ```extend``` _string_
* Return _static_
* Throws InvalidArgumentException
---

```ClassMgr::addImplement( implement )```
* ```implement``` _string_
* Return _static_
* Throws InvalidArgumentException

```ClassMgr::setImplements( implementSet )```
* ```implementSet``` _array_, string[], fqcn's
* Return _static_
* Throws InvalidArgumentException
---

```ClassMgr::setConstruct( construct )```
* Initiated to _false_, no construct method
* ```construct``` _bool_, true (default) : directive to generate (empty) class constructor 
* Return _static_

```ClassMgr::setFactory( factory )```
* Initiated to _false_, no factory method
* ```factory``` _bool_, true (default) : directive to generate class factory
  * default no body
  * populated depending on property set attribute ```argInFactory```, below  
* Return _static_
* Static
---

```ClassMgr::addProperty( property )```
* ```property``` [PropertyMgr]
    * note ```PropertyMgr``` below
    * opt with set directives for _getter_, _setter_, _argInFactory_, below 
* Return _static_

```ClassMgr::addProperty( varDto [, getter [, setter [, argInFactory ]]] )```
* ```varDto``` [VarDto]
  * note *VarDto* below
* ```getter``` _bool_, default false, directive to generate getter methods for property (below)
  * if single array class property, _Iterator_ is implemented (below)
* ```setter``` _bool_, default false, directive to generate setter method(s) for property (below)
* ```argInFactory``` _bool_, default false, directive to use property as argument and value set in class factory method
  * only if ```ClassMgr::setFactory( true )```
* Return _static_
* Throws InvalidArgumentException

```ClassMgr::addProperty( name [, varType [, default [, summary [, description [, getter [, setter [, argInFactory ]]]]]]] )```
* ```name``` _string_
* ```varType``` _string_, default null
  * convenient constants found in PcGenInterface
* ```default``` _mixed_, default null
  * convenient constants found in PcGenInterface
* ```summary``` _string__, default null
* ```description``` _string|array_, default null
* ```getter``` _bool_, default false, directive to generate getter methods for property (below)
  * if single array class property, _Iterator_ is implemented (below)
* ```setter``` _bool_, default false, directive to generate setter method(s) for property (below)
* ```argInFactory``` _bool_, default false, directive to use property as argument and value set in class factory method
  * only if ```ClassMgr::setFactory( true )```
* Return _static_
* Throws InvalidArgumentException

```ClassMgr::setProperties( propertySet )```
* ```propertySet``` _array_, elements any of below 
  * _PropertyMgr_
    * note ```PropertyMgr``` below
    * opt with set directives _getter_, _setter_, _argInFactory_, above 
  * array( _VarDto_ [, getter [, setter [, argInFactory ]]] )
    * note ```VarDto``` below, _getter_, _setter_, _argInFactory_, above
  * array( name [, varType [, default [, summary [, description [, getter [, setter [, argInFactory ]]]]]]] )
    * note ```ClassMgr::addProperty()``` above
* Return _static_
* Throws InvalidArgumentException
---

```ClassMgr::setBody( ...body )```
* ```body``` _string|array_, (multiple) class (methods) logic code (chunks) row(s)
  * note, code without 'baseIndent' 
* Return _static_
---


#### Misc

For class property with the ```getter``` attribute set (true default), results in (property-)methods
* property type bool
  * _is\<Property>_, Return bool
* property type non-bool
  * _get\<Property>_, Return property value
  * _is\<Property>Set_, Return bool true if set
* property type array has also
  * _count\<Property>_, Return (int) number of array elements

For class with a single array property, in substitute of ```getter```-methods, 
_SeekableIterator_, _Countable_ and _IteratorAggregate_ interfaces are supported 
with property methods
* _count_, Return (int) count of elements
  * _Countable_ method
* _current_, Return (mixed) the current element
  * _Iterator_ method
* _exists_, Checks if given (arg) position is set, return bool
* _GetIterator_, Retrieve an external iterator, returning _Traversable_ 
  * _IteratorAggregate_ method  
* _key_, Return the (int) key of the current element
  * _Iterator_ method
* _last_, Move position to last element, return static
* _next_, Move position forward to next element, return static
  * _Iterator_ method
* _previous_, Move position backward to previous element, return static
* _rewind_, Rewind the iterator to the first element, return static
  * _Iterator_ method
* _seek_, Seeks to a given (arg) position in the iterator, throws OutOfBoundsException
  * _SeekableIterator_ method
* _valid_, Checks if current position is valid, return bool
  * _Iterator_ method

For class property with the ```setter``` attribute set (true default), results in (property-)methods
* _set\<Property>_, Set property value, return _static_
* _append\<Property>_, Append array property element value, return _static_
  * array type property only
---
_PropertyMgr_ instance creation ([PropertyMgr])<br><br>
```PropertyMgr::factory( name [, type [, default [, summary [, description ]]]] )```
* ```name``` _string_, argument name ( with or without leading '$')
* ```varType``` _string_, variable type (type hint), default null
  * convenient constants found in PcGenInterface
* ```default```, _mixed_, the argument value if null
* ```summary``` _string_, the [phpdoc] summary
* ```description``` _string_|_array_, the [phpdoc] description

_VarDto_ instance creation ([VarDto])<br><br>
```VarDto::factory( [ varName [, varType [ default [, summary [, description ]]]]] )```
* ```varName``` _string_
* ```varType``` _string_, variable type (type hint), default null
  * convenient constants found in PcGenInterface
* ```default```, _mixed_, the argument value if null
* ```summary``` _string_, the [phpdoc] summary
* ```description``` _string_|_array_, the [phpdoc] description

---
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
