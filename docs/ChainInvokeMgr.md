[comment]: # (This file is part of PcGen, PHP Code Generation support package. Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence GPL 3.0)

#### ChainInvokeMgr

The ```ChainInvokeMgr``` class manages chained function/method invokes
* each invoke mastered by [FcnInvokeMgr] 
* used by [AssignClauseMgr] and [ReturnClauseMgr] 


###### ChainInvokeMgr Methods

---
Inherited [Common methods]

---

```ChainInvokeMgr::factory( ...FcnInvoke )```
* ```FcnInvoke``` any number of [FcnInvokeMgr]
* For eol and indents, defaults are used
* Return static
* Throws InvalidArgumentException
---

```ChainInvokeMgr::toArray()```
* Return _array_, result code rows (null-bytes removed) no trailing eol
* Throws RuntimeException

```ChainInvokeMgr::toString()```
* Return _array_, result code rows (null-bytes removed) no trailing eol
* Throws RuntimeException
---

```ChainInvokeMgr::getChainInvokes()```
* Return [FcnInvokeMgr]\[]

```ChainInvokeMgr::isChainInvokesSet()```
* Return _bool_ true if set

```ChainInvokeMgr::appendChainedInvoke( fcnInvoke )```
* fcnInvoke [FcnInvokeMgr]
* Return static
* Throws InvalidArgumentException

```ChainInvokeMgr::setChainInvokes( chainInvokes )```
* ```chainInvokes``` [FcnInvokeMgr]\[]
* Return static
* Throws InvalidArgumentException
---

<small>Return to [README] - [Summary]</small>

[AssignClauseMgr]:AssignClauseMgr.md
[Common methods]:CommonMethods.md
[FcnInvokeMgr]:EntityMgr.md
[README]:../README.md
[ReturnClauseMgr]:ReturnClauseMgr.md
[Summary]:Summary.md


