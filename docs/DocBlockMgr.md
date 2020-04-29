[comment]: # (This file is part of PcGen, PHP Code Generation support package. Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence GPL 3.0)

#### DocBlockMgr

DocBlockMgr manages [phpdoc] docBlocks.

###### Methods

---
Inherited [Common methods]

---

```DocBlockMgr::factory(  [  tagName [, tagType [, tagText [, tagComment [, tagExt ]]]] )```
* Convenient method for single variable/property/constant/.. (tag) docBlock
* ```tagName``` _string_, convenient constants found in PcGenInterface 
* ```tagType``` _string_, convenient constants found in PcGenInterface
* ```tagText``` _string_
* ```tagComment``` _string_
* ```tagExt``` _string_
* Static
* Return _static_
---

```DocBlockMgr::toArray()```
* Return _array_, docBlock code rows (null-bytes removed) no trailing eol

```DocBlockMgr::toString()```
* Return _string_ with code rows (extends toArray), each code row with trailing eol
---


```DocBlockMgr::setSummary( summary )```
* ```summary``` _string_, short (top) description
* Return _static_

```DocBlockMgr::setDescription( longDescr )```
* ```longDescr``` _string|array_, will have a leading emptyline
* Return _static_

```DocBlockMgr::setInfo( summary [, longDescr ] )```
* ```summary``` _string_, short (top) description
* ```longDescr``` _string|array_, will have a leading emptyline
* Return _static_
---

```DocBlockMgr::setTag( tagName [, tagType [, tagText [, tagComment [, tagExt ]]]] )```
* Note, annotations are not supported, only [phpdoc] tags
* ```tagName``` _string_, convenient constants found in PcGenInterface 
* ```tagType``` _string_, convenient constants found in PcGenInterface
* ```tagText``` _string_
* ```tagComment``` _string_
* ```tagExt``` _string_
* Return _static_
---

```DocBlockMgr::isValidTagName( tag )```
* ```tag``` _string_
* Return ```bool``` true if tag is a [phpdoc] valid tag
* Static

```DocBlockMgr::assertTagName( tag )```
* ```tag``` _string_
* Throws InvalidArgumentException on not accepted tag
* Static
---

<small>Return to [README] - [Summary]</small>

[Common methods]:CommonMethods.md
[phpdoc]:https://phpdoc.org
[README]:../README.md
[Summary]:Summary.md
