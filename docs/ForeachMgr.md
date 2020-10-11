[comment]: # (This file is part of PcGen, PHP Code Generation support package. Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence GPL 3.0)

#### ForeachMgr

The ```ForeachMgr``` manages control structure foreach

* Accepts variable, classProperty and class function as array_expression
* No reference iterValues 
* The foreach logic body is set using ForeachMgr::setBody()

###### ForeachMgr Methods

---
Inherited [Common methods]

---

```ForeachMgr::factory( iterator [, key [, iterValue ]] ) ```

* ```iterator``` _string_|_EntityMgr_|_FcnInvokeMgr_
* ```key``` _string_
* ```iterValue``` _string_  default 'value'
* Return _static_
* Throws _InvalidArgumentException_

---

```ForeachMgr::toArray() ```

* Return _array_, result code rows (null-bytes removed) no trailing eol
* Throws _RuntimeException_


```ForeachMgr::toString() ```

* Return _array_, result code rows (null-bytes removed) no trailing eol
* Throws _RuntimeException_

---

```ForeachMgr::getIterator() ```

* Return _string_|_EntityMgr_|_FcnInvokeMgr_


```ForeachMgr::isIteratorSet() ```

* Return _bool_ true if set, false not


```ForeachMgr::setIterator( iterator ) ```

* ```iterator``` _string_|_EntityMgr_|_FcnInvokeMgr_
* Return _static_
* Throws _InvalidArgumentException_

---

```ForeachMgr::getKey() ```

* Return _string_


```ForeachMgr::isKeySet() ```

* Return _bool_ true if set, false not


```ForeachMgr::setKey( key ) ```

* ```key``` _string_
* Return _static_
* Throws _InvalidArgumentException_

---

```ForeachMgr::getIterValue() ```

* Return _string_


```ForeachMgr::setIterValue( iterValue ) ```

* ```iterValue``` _string_
* Return _static_
* Throws _InvalidArgumentException_

---

#### Example

```
<?php

$code = FcnFrameMgr::init()
    ->setName( 'someFunction' )
    ->addArgument( 'iterator', FcnFrameMgr::ARRAY ) 
    ->setBody(
        ForeachMgr::factory( 'iterator' )
            ->setBody(
                ' // this is the foreach body'
            )
            ->toArray()
    )
    ->toString();
```

Result :

```
    public function someFunction( array $iterator )
    {
        foreach( $iterator as $value ) {
            // this is the foreach body
        } // end foreach
    }
```

---

<small>Return to [README] - [Summary]</small>

[ClassMgr]:ClassMgr.md
[Common methods]:CommonMethods.md
[DocBlockMgr]:DocBlockMgr.md
[README]:../README.md
[Summary]:Summary.md
