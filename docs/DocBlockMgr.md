[comment]: # (This file is part of PcGen, PHP Code Generation support package. Copyright 2020-21 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence GPL 3.0)

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
* For eol and indents, defaults are used
* Static
* Return _static_

---

```DocBlockMgr::toArray()```

* Return _array_, docBlock code rows (null-bytes removed) no trailing eol


```DocBlockMgr::toString()```

* Return _string_ with code rows (extends toArray), each code row with trailing eol

---


```DocBlockMgr::isSummarySet()```

* Return _bool_ true, if set, false not


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

```DocBlockMgr::isTagSet( tagName )```

* ```tagName``` _string_, convenient constants found in PcGenInterface 
* Return _bool_ true if ```tagName``` set, false not


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
* Throws _InvalidArgumentException_ on not accepted tag
* Static


---
#### Example

```
<?php

$code = DocBlockMgr::init()
    ->setSummary( 'Summary' )
    ->setDescription( 'Decription 1' )
    ->setDescription( [ 'Description 2', 'some text here...'] )
    ->setTag(
        DocBlockMgr::PARAM_T,
        [ DocBlockMgr::STRING_T, DocBlockMgr::STRINGARRAY_T ],
        'parameter'
    )
    ->setTag(
        DocBlockMgr::PARAM_T,
        DocBlockMgr::INT_T,
        'quantity'
    )
    ->setTag( DocBlockMgr::RETURN_T, DocBlockMgr::ARRAY_T )
    ->toString();

```

Result :

```
    /**
     * Summary
     *
     * Decription 1
     *
     * Description 2
     * some text here...
     *
     * @param   string|string[] $parameter
     * @param   int             $quantity
     * @return  array
     */

```

---

<small>Return to [README] - [Summary]</small>

[Common methods]:CommonMethods.md
[phpdoc]:https://phpdoc.org
[README]:../README.md
[Summary]:Summary.md
