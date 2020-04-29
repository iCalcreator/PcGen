[comment]: # (This file is part of PcGen, PHP Code Generation support package. Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence GPL 3.0)

#### ReturnClauseMgr

The ```ReturnClauseMgr``` manages **return** code of 
* source class property or variable (value), opt (int/variable) index
* (scalar) fixedSourceValue
* single function/method or chained invokes
* (but not 'body' code)
* ex ```return $this->property[32];```

Note, return (result from) function/method invoke is managed by [VariableMgr]::setBody() (or [PropertyMgr]) and use of [FcnInvokeMgr]

###### ReturnClauseMgr Methods

---
Inherited [Common methods]

---

```ReturnClauseMgr::factory( [ class [, variable, [, index]]] )```
* ```class``` _string_|[EntityMgr] if string, one of null, self, $this, 'otherClass', '$class'
  * convenient constants found in PcGenInterface 
* ```variable``` _string_ variable/property name
  * uppercase is autodetected as CONSTANT
  * variable $-prefixed
* ```index```  _int_|_string_ opt array index, if _string_, index will be $-prefixed 
* Return static
* Throws InvalidException
---

```ReturnClauseMgr::toArray()```
* Return _array_, result code rows (null-bytes removed) no trailing eol
* Throws RuntimeException

```ReturnClauseMgr::toString()```
* Return _string_ with code rows (extends toArray), each code row with trailing eol
* Throws RuntimeException
---

```ReturnClauseMgr::getFixedSourceValue()```
* Return _bool_|_int_|_float_|_string_, scalar

```ReturnClauseMgr::isFixedSourceValueSet()```
* Return _bool_ true if not null

```ReturnClauseMgr::setFixedSourceValue( fixedSourceValue )```
* ```fixedSourceValue``` _bool_|_int_|_float_|_string_, scalar
* Return _static_
* Throws InvalidException
---

```ReturnClauseMgr::getSource()```
* Return [EntityMgr]

```ReturnClauseMgr::isSourceSet()```
* Return _bool_ true if not null

```ReturnClauseMgr::setSource( class [, variable, [, index ]] )```
* ```class``` _string_|[EntityMgr] if string, one of null, self, $this, 'otherClass', '$class'
  * convenient constants found in PcGenInterface 
* ```variable``` _string_ class/variable/property name
  * uppercase is autodetected as CONSTANT
  * variable $-prefixed
* ```index```  _int_|_string_ opt array index, if _string_, index will be $-prefixed 
* Return static
* Throws InvalidArgumentException

```ReturnClauseMgr::getClass()```
* Return _string_ one of null, self, $this, 'otherClass', '$class'

```ReturnClauseMgr::setClass( class )```
* ```class``` _string_, one of null or (entity) self, $this, 'otherClass', '$class'
  * convenient constants found in PcGenInterface 
* Return static
* Throws InvalidArgumentException

```ReturnClauseMgr::getVariable()```
* Return _string_

```ReturnClauseMgr::setVariable( variable )```
* ```variable``` _string_ (entity) variable/property name
  * uppercase is autodetected as CONSTANT
  * variable $-prefixed
* Return static
* Throws InvalidArgumentException

```ReturnClauseMgr::getIndex()```
* Return _int_|_string_

```ReturnClauseMgr::setIndex( index )```
* ```index```  _int_|_string_ (entity) opt variable/property array index
* Return static
* Throws InvalidArgumentException

```ReturnClauseMgr::setIsConst( isConst )```
* ```isConst``` _bool_
  * true : force (entity) ```$class::CONSTANT```
  * false : NOT, (entity) default ```$class->$constant``` 
* Return _static_
---

```AssignClauseMgr::getFcnInvoke()```
* Return [ChainInvokeMgr] (manages single or chained [FcnInvokeMgr]s)

```AssignClauseMgr::isFcnInvokeSet()```
* Return _bool_ true if not null

```AssignClauseMgr::setFcnInvoke( fcnInvoke )```
* ```fcnInvoke``` [FcnInvokeMgr] | [FcnInvokeMgr]\[]  
* Return static
* Throws InvalidArgumentException
---

Return to PcGen [README], [Summary] 

[ChainInvokeMgr]:ChainInvokeMgr.md
[Common methods]:CommonMethods.md
[EntityMgr]:EntityMgr.md
[FcnInvokeMgr]:FcnInvokeMgr.md
[PropertyMgr]:PropertyMgr.md
[README]:../README.md
[Summary]:Summary.md
[VariableMgr]:VariableMgr.md
