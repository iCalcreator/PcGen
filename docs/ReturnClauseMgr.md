[comment]: # (This file is part of PcGen, PHP Code Generation support package. Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence GPL 3.0)

#### ReturnClauseMgr

The ```ReturnClauseMgr``` manages **return** code of 
* source class property or variable (value), opt (int/variable) index
* (scalar) fixedSourceValue
* PHP expression
* single function/method or chained invokes
* ex ```return $this->property[32];```

Note, return (result from) function/method invoke is managed by [VariableMgr]::setBody() (or [PropertyMgr]) and use of [FcnInvokeMgr]

###### ReturnClauseMgr Methods

---
Inherited [Common methods]

---

```ReturnClauseMgr::factory( [ class [, variable, [, index]]] )```
* ```class``` _string_ if string, one of null, self, $this, 'otherClass', '$class'
  * convenient constants found in PcGenInterface 
* ```variable``` _string_ variable/property name
  * uppercase is autodetected as CONSTANT
  * variable $-prefixed
* ```index```  _int_|_string_ opt array index, if _string_, index will be $-prefixed 
* For eol and indents, defaults are used
* Return _static_
* Throws InvalidException

```ReturnClauseMgr::factory( entity )```
* ```entity``` [EntityMgr]
  *  note ```EntityMgr``` below
* Return _static_
* Throws InvalidException
---

```ReturnClauseMgr::toArray()```
* Return _array_, result code rows (null-bytes removed) no trailing eol
* Throws _RuntimeException_

```ReturnClauseMgr::toString()```
* Return _string_ with code rows (extends toArray), each code row with trailing eol
* Throws _RuntimeException_
---

```ReturnClauseMgr::getScalar()```
* Return _bool_|_int_|_float_|_string_, scalar

```ReturnClauseMgr::isScalarSet()```
* Return _bool_ true if not null

```ReturnClauseMgr::setScalar( fixedSourceValue )```
* ```fixedSourceValue``` _bool_|_int_|_float_|_string_, scalar
* Return _static_
* Throws InvalidException
---

```ReturnClauseMgr::setSourceExpression( expression )```
* Set a PHP expression
* ```expression``` _string_
* Return _static_
* Throws InvalidException
---

```ReturnClauseMgr::getSource()```
* Return [EntityMgr]

```ReturnClauseMgr::isSourceSet()```
* Return _bool_ true if not null

```ReturnClauseMgr::setSource( class [, variable, [, index ]] )```
* ```class``` _string_ one of null, self, $this, 'otherClass', '$class'
  * convenient constants found in PcGenInterface 
* ```variable``` _string_ class/variable/property name
  * uppercase is autodetected as CONSTANT
  * variable $-prefixed
* ```index```  _int_|_string_ opt array index, if _string_, index will be $-prefixed 
* Return _static_
* Throws _InvalidArgumentException_

```ReturnClauseMgr::setSource( entity )```
* ```entity``` [EntityMgr]
  *  note ```EntityMgr``` below
* Return _static_
* Throws _InvalidArgumentException_

```ReturnClauseMgr::setThisPropertySource( property [, index ] )```
* convenient shortcut for ```ReturnClauseMgr::setSource()```
* Give source result ```$this->property```
* ```property``` _string_
* ```index```  _int_|_string_ opt array index
* Return _static_
* Throws _InvalidArgumentException_

```ReturnClauseMgr::setVariableSource( variable [, index ] )```
* convenient shortcut for ```ReturnClauseMgr::setSource()```
* Give source result ```$variable```
* ```variable``` _string_
* ```index```  _int_|_string_ opt array index
* Return _static_
* Throws _InvalidArgumentException_

```ReturnClauseMgr::setSourceIsConst( isConst )```
 * Results in uppercase constant
* ```isConst``` _bool_
  * true : force ```$class::CONSTANT```
  * false : NOT, (default) ```$class->$constant``` 
* Return _static_


```ReturnClauseMgr::setSourceIsStatic( isStatic )```
 * Results in uppercase constant
* ```isConst``` _bool_
  * true : force ```$class::variable```
  * false : NOT, default, ```$class->$variable``` 
* Return _static_
---

```ReturnClauseMgr::getFcnInvoke()```
* Return [ChainInvokeMgr] (manages single or chained [FcnInvokeMgr]s)

```ReturnClauseMgr::isFcnInvokeSet()```
* Return _bool_ true if not null

```ReturnClauseMgr::appendInvoke( fcnInvoke )```
* ```fcnInvoke``` [FcnInvokeMgr]
* Return _static_
* Throws _InvalidArgumentException_

```ReturnClauseMgr::setFcnInvoke( fcnInvoke )```
* ```fcnInvoke``` [FcnInvokeMgr]\[]
* Return _static_
* Throws _InvalidArgumentException_

Note on chained invokes
* The first must have a "class" : parent, self, $this, 'otherClass', '$class' when next is set
* All but first must have $this, 'otherClass', '$class'

Ex on _ReturnClauseMgr::setFcnInvoke_ input
```
[
    FcnInvokeMgr::factory( 'aClass', 'factory', [ 'arg1', 'arg2' ] ),
    FcnInvokeMgr::factory( 'aClass', 'someMethod', [ 'arg3', 'arg4' ] )
]
``` 
results in 
```
aClass::factory( $arg1, arg2 )
    ->someMethod( $arg3, arg4 );
```
---


#### Misc

_EntityMgr_ instance creation ([EntityMgr])<br><br>
```EntityMgr::factory( class , fcnName )```
* ```class```, _string_, one of ```null```, ```self```, ```this```, ```otherClass``` (fqcn), ```$class```
  * convenient constants found in PcGenInterface
* ```fcnName``` _string_, the name
---

#### Example 1

```
<?php

$code = ReturnClauseMgr::factory( null, 'variable' )
    ->toString();

$code .= ReturnClauseMgr::factory( ReturnClauseMgr::THIS_KW, 'variable' )
    ->toString();

$code .= ReturnClauseMgr::factory( ReturnClauseMgr::THIS_KW )
    ->toString();

```

Result :

```
    return $variable;
    return $this->variable;
    return $this;
```

#### Example 2

```
<?php

$rcm = ReturnClauseMgr::init()
    ->setBaseIndent()
    ->setFcnInvoke( 
        FcnInvokeMgr::factory( 'SourceClass', FcnInvokeMgr::FACTORY, [ 'arg11', 'arg12' ] ),
        FcnInvokeMgr::factory( 'SourceClass', 'method2', [ 'arg21', 'arg22' ] ),
        FcnInvokeMgr::factory( 'SourceClass', 'method3', [ 'arg31', 'arg32' ] ),
        FcnInvokeMgr::factory( 'SourceClass', 'method4', [ 'arg41', 'arg42' ] ),
        FcnInvokeMgr::factory( 'SourceClass', 'method5' )
    )
    ->toString());

```

Result :

```
return SourceClass::factory( $arg11, $arg12 )
    ->method2( $arg21, $arg22 )
    ->method3( $arg31, $arg32 )
    ->method4( $arg41, $arg42 )
    ->method5();

```

---


<small>Return to PcGen [README], [Summary]</small> 

[ChainInvokeMgr]:ChainInvokeMgr.md
[Common methods]:CommonMethods.md
[EntityMgr]:EntityMgr.md
[FcnInvokeMgr]:FcnInvokeMgr.md
[PropertyMgr]:PropertyMgr.md
[README]:../README.md
[Summary]:Summary.md
[VariableMgr]:VariableMgr.md
