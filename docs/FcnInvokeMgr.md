[comment]: # (This file is part of PcGen, PHP Code Generation support package. Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence GPL 3.0)

#### FcnInvokeMgr

The ```FcnInvokeMgr``` class manages (single) function/method invoke
* opt with arguments
* ex ```FcnInvokeMgr::factory( FcnInvokeMgr::THIS_KW, 'method, [ 'arg1', 'arg2' ] )->toString()``` 
  * give result ```$this->method( $arg1, $arg2 )``` (+eol)
  
Chained invokes manages by [ChainInvokeMgr].

###### FcnInvokeMgr Methods

---
Inherited [Common methods]

---

```FcnInvokeMgr::factory( class [, fcnName [, arguments ]] )```
* ```class``` _string_ one of ```null```, ```self```, ```this```, ```otherClass``` (fqcn), ```$class```
  * convenient constants found in PcGenInterface
* ```fcnName```   _string_, function/method name
* ```arguments``` _array_, note ```FcnInvokeMgr::setArguments()``` below
* Static
* Return _static_

```FcnInvokeMgr::factory( entity [, null, arguments ]  )```
* ```entity``` _EntityMgr_
  * note ```EntityMgr``` below
* ```arguments``` _array_, note ```FcnInvokeMgr::setArguments()``` below
* Static
* Return _static_
---

```FcnInvokeMgr::toArray()```
* Return _array_, result code rows (null-bytes removed) no trailing eol
* Throws RuntimeException

```FcnInvokeMgr::toString()```
* Return _string_ with code rows (extends toArray), each code row with trailing eol
* Throws RuntimeException
---

```FcnInvokeMgr::setName( class [, fcnName ] )```
* The function/method name
* ```class``` _string_
  * one of ```null```, ```self```, ```this```, ```otherClass``` (fqcn), ```$class```
  * convenient constants found in PcGenInterface
* ```fcnName```  _string_, function/method name
* Return _static_
* Throws InvalidArgumentException

```FcnInvokeMgr::setName( entity )```
* The function/method name
* ```entity``` _EntityMgr_
  *  note ```EntityMgr``` below
* Return _static_
* Throws InvalidArgumentException
---

```FcnInvokeMgr::addArgument( argument )```
* ```argument``` _ArgumentDto_
  * note ```ArgumentDto``` below
* Return _static_

```FcnInvokeMgr::addArgument( varDto )```
* ```varDto``` _VarDto_
  * note ```VarDto``` below
* Return _static_

```FcnInvokeMgr::addArgument( name )```
* ```name``` _string_, argument name
* Return _static_
* throws InvalidArgumentException

```FcnInvokeMgr::setArguments( argumentSets )```
* ```argumentSets``` _array_, elements any of below 
  * name, _string_
  * _ArgumentDto_
    * note ```ArgumentDto``` below
  * _VarDto_
    * note ```VarDto``` below
* Return _static_
* Throws InvalidArgumentException
---

```FcnInvokeMgr::setIsStatic( isStatic )```
* Only applicable for '$class', ignored by the others
* ```isStatic``` _bool_, (default false)
* Return _static_
* Throws InvalidArgumentException
* require (class+)name set

Example : ```FcnInvokeMgr::factory( '$class', 'method' )->toString(); ```<br>
Result : ``` $class->method() ``` (+eol)

Example : ```FcnInvokeMgr::factory( '$class', 'method' )->setIsStatic( true )->toString(); ```<br>
Result : ``` $class::method() ``` (+eol)
---


#### Misc

_ArgumentDto_ instance creation ([ArgumentDto])<br><br>
```PropertyMgr::factory( argName )```
* ```argName``` _string_, argument name

_EntityMgr_ instance creation ([EntityMgr])<br><br>
```EntityMgr::factory( class , fcnName )```
* ```class```, _string_, one of ```null```, ```self```, ```this```, ```otherClass``` (fqcn), ```$class```
  * convenient constants found in PcGenInterface
* ```fcnName``` _string_, the name

_VarDto_ instance creation ([VarDto])<br><br>
```VarDto::factory( argName )```
* ```argName``` _string_
---

<small>Return to PcGen [README], [Summary]</small> 

[ArgumentDto]:ArgumentDto.md
[ChainInvokeMgr]:ChainInvokeMgr.md
[Common methods]:CommonMethods.md
[EntityMgr]:EntityMgr.md
[README]:../README.md
[Summary]:Summary.md
[VarDto]:VarDto.md
