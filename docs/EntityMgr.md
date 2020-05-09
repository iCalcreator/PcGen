[comment]: # (This file is part of PcGen, PHP Code Generation support package. Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence GPL 3.0)

#### EntityMgr

The ```EntityMgr``` class manages PHP Entity set
* used by [AssignClauseMgr] and [ReturnClauseMgr]
* the Entity set has
  * ```class``` - one of null, self, $this, 'otherClass', '$class'
  * ```variable``` - constant/variable/property name
  * ```index``` - opt array index

Note, invoke of function/method is managed by [FcnInvokeMgr]

###### EntityMgr Methods

---
Inherited [Common methods]

---

```EntityMgr::factory( class [, variable, [, index ]] )```
* ```class``` _string_ one of null, self, $this, 'otherClass', '$class'
  * convenient constants found in PcGenInterface 
* ```variable``` _string_ constant/variable/property name
  * for CONSTANT use ```EntityMgr::setIsConst()``` below
  * variable will be $-prefixed
* ```index```  _int_|_string_ opt array index
* For eol and indents, defaults are used
* Return static
* Throws InvalidArgumentException
---


```EntityMgr::toArray()```
* Return _array_, result code rows (null-bytes removed) no trailing eol
* Throws RuntimeException

```EntityMgr::toString()```
* Return _string_ with code rows (extends toArray), each code row with trailing eol
* Throws RuntimeException
---

```EntityMgr::getClass()```
* Return string

```EntityMgr::setClass( class )```
* ```class``` _string_ one of null, self, this, 'otherClass', '$class'
  * convenient constants found in PcGenInterface
* Return _static_
* Throws InvalidArgumentException
---

```EntityMgr::getVariable()```
* Return _string_

```EntityMgr::setVariable( variable )```
* ```variable``` _string_ constant/variable/property name
  * for CONSTANT use ```EntityMgr::setIsConst()``` below
  * variable will be $-prefixed
* Return _static_
* Throws InvalidArgumentException
---

```EntityMgr::getIndex()```
* Return _int|_string_

```EntityMgr::setIndex( index )```
* ```index```  _int_|_string_ opt array index
* Return _static_
* Throws InvalidArgumentException
---

```EntityMgr::setIsConst( const )```
* Results in uppercase constant
* ```const``` _bool_, true : constant, false : NOT, default 
* Return _static_
---

```EntityMgr::setIsStatic( isStatic )```
* Applicable only when class matches '$class' (i.e. $-prefixed string)
* ```isStatic``` _bool_, (false default) 
* Return _static_

Example : ```EntityMgr::factory( '$class', 'property' )->toString(); ```<br>
Result : ``` $class->property ``` (+eol)

Example : ```EntityMgr::factory( '$class', 'property' )->setIsStatic( true )->toString(); ```<br>
Result : ``` $class::$property ``` (+eol)
---

<small>Return to [README] - [Summary]</small>

[AssignClauseMgr]:AssignClauseMgr.md
[Common methods]:CommonMethods.md
[FcnInvokeMgr]:FcnInvokeMgr.md
[README]:../README.md
[ReturnClauseMgr]:ReturnClauseMgr.md
[Summary]:Summary.md
