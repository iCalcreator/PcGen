[comment]: # (This file is part of PcGen, PHP Code Generation support package. Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence GPL 3.0)

#### ChainInvokeMgr

The ```ChainInvokeMgr``` class manages chained function/method invokes
* each invoke mastered by [FcnInvokeMgr] 
* used by [AssignClauseMgr] and [ReturnClauseMgr] 
* ex result ```aClass::factory( $arg1, arg2 )->someMethod( $arg3, arg4 );```


###### ChainInvokeMgr Methods

---
Inherited [Common methods]

---

```ChainInvokeMgr::factory( ...FcnInvoke )```
* ```FcnInvoke``` any number of [FcnInvokeMgr]
* For eol and indents, defaults are used
* Return _static_
* Throws _InvalidArgumentException_
---

```ChainInvokeMgr::toArray()```
* Return _array_, result code rows (null-bytes removed) no trailing eol
* Throws _RuntimeException_

```ChainInvokeMgr::toString()```
* Return _array_, result code rows (null-bytes removed) no trailing eol
* Throws _RuntimeException_
---

```ChainInvokeMgr::getInvokes()```
* Return [FcnInvokeMgr]\[]

```ChainInvokeMgr::isInvokesSet()```
* Return _bool_ true if set

```ChainInvokeMgr::appendInvoke( fcnInvoke )```
* ```fcnInvoke``` [FcnInvokeMgr]
* Return _static_
* Throws _InvalidArgumentException_

```ChainInvokeMgr::setInvokes( invokes )```
* ```invokes``` [FcnInvokeMgr]\[]
* Return _static_
* Throws _InvalidArgumentException_
---

<small>Return to [README] - [Summary]</small>

[AssignClauseMgr]:AssignClauseMgr.md
[Common methods]:CommonMethods.md
[FcnInvokeMgr]:FcnInvokeMgr.md
[README]:../README.md
[ReturnClauseMgr]:ReturnClauseMgr.md
[Summary]:Summary.md


