[comment]: # (This file is part of PcGen, PHP Code Generation support package. Copyright 2020-21 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence GPL 3.0)

#### VarDto

The ```VarDto``` class manages base variable data and has only getter and setter methods, no logic.
Used in [ClassMgr], [FcnFrameMgr] and [FcnInvokeMgr], defining (simpler) arguments.

###### VarDto Methods

---

```VarDto::__construct( [ varName [, varType [ default [, summary [, description ]]]]] )```

* ```varName``` _string_ 
* ```varType``` _string_, variable type (type hint), default null
   convenient constants found in PcGenInterface
* ```default```, _mixed_, the argument (init-)value if null
* ```summary``` _string_, the [phpdoc] summary
* ```description``` _string_|_array_, the [phpdoc] description
* Throws _InvalidArgumentException_


```VarDto::factory( [ varName [, varType [ default [, summary [, description ]]]]] )```

* ```varName``` _string_ 
* ```varType``` _string_, variable type (type hint), default null
   convenient constants found in PcGenInterface
* ```default```, _mixed_, the argument (init-)value if null
* ```summary``` _string_, the [phpdoc] summary
* ```description``` _string_|_array_, the [phpdoc] description
* Return _static_
* Static
* Throws _InvalidArgumentException_

---

```VarDto::getName()```

* Return _string_
* Throws _InvalidArgumentException_


```VarDto::isNameSet()```

* Return _bool_, true if varName is set 


```VarDto::setName( varName )```

* ```varName``` _string_ 
* Return _static_
* Throws _InvalidArgumentException_

---

```VarDto::getVarType()```

* Return _string_


```VarDto::getParamTagVarType()```

* Return _string_, the [phpdoc] param tag value type, if set, otherwise 'mixed' 


```VarDto::isTypedArray()```

* Return _bool_, true if varType is of array type


```VarDto::isTypeHint( [ phpVersion ] )```

* ```phpVersion``` _string_, expected PHP version, if null current (constant) PHP_VERSION is used 
* Return _bool_, true if varType can be used as type hint (dep. on PHP version) 


```VarDto::isVarTypeSet()```

* Return _bool_, true if varType is set 


```VarDto::setVarType( varType )```

* ```varType``` _string_, variable type (type hint), default null
  * convenient constants found in PcGenInterface
* Return _static_

---

```VarDto::getDefault()```

* Return _mixed_


```VarDto::isDefaultArray()```

* Return _bool_, true if default value is array 


```VarDto::isDefaultSet()```

* Return _bool_, true if default is set 


```VarDto::isDefaultTypedArray()```

* Return _bool_, true if default is typed as array 


```VarDto::setDefault( default )```

* ```default``` _mixed_
* Return _static_

---

```VarDto::getSummary()```

* Return _string_


```VarDto::isSummarySet()```

* Return _bool_, true if summary is set 


```VarDto::setSummary( summary )```

* ```summary``` _string_, the [phpdoc] summary
* Return _static_

---

```VarDto::getDescription()```
* Return _array_


```VarDto::isDescriptionSet()```

* Return _bool_, true if description is set 


```VarDto::setDescription( description )```

* ```description``` _string_|_array_, the [phpdoc] description
* Return _static_

---

<small>Return to PcGen [README], [Summary]</small> 

[ClassMgr]:ClassMgr.md
[FcnFrameMgr]:FcnFrameMgr.md
[FcnInvokeMgr]:FcnInvokeMgr.md
[phpdoc]:https://phpdoc.org
[README]:../README.md
[Summary]:Summary.md
