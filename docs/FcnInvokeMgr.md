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

* ```class``` _string_ one of ```null```, ```parent```, ```self```, ```this```, ```otherClass``` (fqcn), ```$class```
   convenient constants found in PcGenInterface
* ```fcnName```   _string_, function/method name
* ```arguments``` _array_, note ```FcnInvokeMgr::setArguments()``` below
* For eol and indents, defaults are used
* Static
* Return _static_


```FcnInvokeMgr::factory( entity [, null, arguments ]  )```

* ```entity``` _EntityMgr_
  note ```EntityMgr``` below
* ```arguments``` _array_, note ```FcnInvokeMgr::setArguments()``` below
* Static
* Return _static_

---

```FcnInvokeMgr::toArray()```

* Return _array_, result code rows (null-bytes removed) no trailing eol
* Throws _RuntimeException_


```FcnInvokeMgr::toString()```
* Return _string_ with code rows (extends toArray), each code row with trailing eol
* Throws _RuntimeException_
---

```FcnInvokeMgr::setName( class [, fcnName ] )```

* The function/method name
* ```class``` _string_
   one of ```null```, ```parent```, ```self```, ```this```, ```otherClass``` (fqcn), ```$class```
  convenient constants found in PcGenInterface
* ```fcnName```  _string_, function/method name
* Return _static_
* Throws _InvalidArgumentException_


```FcnInvokeMgr::setName( entity )```

* The function/method name
* ```entity``` _EntityMgr_
   note ```EntityMgr``` below
* Return _static_
* Throws _InvalidArgumentException_

---

```FcnInvokeMgr::addArgument( argument )```

* ```argument``` _ArgumentDto_
   note ```ArgumentDto``` below
* Return _static_


```FcnInvokeMgr::addArgument( varDto )```

* ```varDto``` _VarDto_
   note ```VarDto``` below
* Return _static_


```FcnInvokeMgr::addArgument( name )```

* ```name``` _string_, argument name
* Return _static_
* Throws _InvalidArgumentException_


```FcnInvokeMgr::setArguments( argumentSets )```

* ```argumentSets``` _array_, elements any of below 
   name, _string_
   _ArgumentDto_, note ```ArgumentDto``` below
  _VarDto_, note ```VarDto``` below
* Return _static_
* Throws _InvalidArgumentException_

---

```FcnInvokeMgr::setIsStatic( isStatic )```

* Only applicable for '$class', ignored by the others
* ```isStatic``` _bool_, (default false)
* Return _static_
* Throws _InvalidArgumentException_
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
* ```class```, _string_, one of ```null```, ```parent```, ```self```, ```this```, ```otherClass``` (fqcn), ```$class```
  * convenient constants found in PcGenInterface
* ```fcnName``` _string_, the name

_VarDto_ instance creation ([VarDto])<br><br>
```VarDto::factory( argName )```
* ```argName``` _string_

---

#### Example 1

```
<?php

$code = FcnInvokeMgr::()
    ->setname( FcnInvokeMgr::THIS_KW,  'method' )
    ->setArgument( [ 'arg1', 'arg2' ] )
    ->toString();

```

Result :

```

$this->method( $arg1, $arg2 )

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

[ArgumentDto]:ArgumentDto.md
[ChainInvokeMgr]:ChainInvokeMgr.md
[Common methods]:CommonMethods.md
[EntityMgr]:EntityMgr.md
[README]:../README.md
[Summary]:Summary.md
[VarDto]:VarDto.md
