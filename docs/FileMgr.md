[comment]: # (This file is part of PcGen, PHP Code Generation support package. Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence GPL 3.0)

#### FileMgr

The ```FileMgr``` class manages file

* docBlock and (class/interface/trait) body
* Allows other body using FileMgr::setBody()

###### FileMgr Methods

---
Inherited [Common methods]

---

```FileMgr::toArray() ```

* Return _array_, result code rows (null-bytes removed) no trailing eol
* Throws _RuntimeException_


```FileMgr::toString() ```

* Return _array_, result code rows (null-bytes removed) no trailing eol
* Throws _RuntimeException_

---

```FileMgr::setDocBlock( docBlocks ) ```

*  Set one or more docBlocks
* ```docBlocks``` [DocBlockMgr]|[DocBlockMgr]\[]
* Throws _InvalidArgumentException_
* Return _static_

---

```FileMgr::setInfo( summary, description ) ```

* Set file docBlock summary AND (one) description in FIRST docBlock
* ```summary``` _string_
* ```description``` _string_|_array_
* Return _static_

---

```FileMgr::isFileBodySet() ```

* Return _bool_ true if set, false not

```FileMgr::setFileBody( fileBody ) ```

* Set file body
* ```fileBody``` [ClassMgr]
* Return _static_

---
#### Example

```
<?php

$code = FileMgr::init()
    ->setInfo( 'File summary', [ 'file description1', 'file description2' ] );
    ->setFileBody(
        ClassMgr::init()
            ->setName( 'TestClass' )
            ->addProperty(
                PropertyMgr::factory( 'propertyName', PropertyMgr::STRING_T )
            )
    )
    ->toString();
```

Result :

```
/**
 * file summary
 *
 * file description1
 * file description2
 *
 */

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

---
<small>Return to [README] - [Summary]</small>

[ClassMgr]:ClassMgr.md
[Common methods]:CommonMethods.md
[DocBlockMgr]:DocBlockMgr.md
[README]:../README.md
[Summary]:Summary.md
