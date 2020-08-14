[comment]: # (This file is part of PcGen, PHP Code Generation support package. Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence GPL 3.0)

#### VariableMgr Methods

The ```VariableMgr``` class manages PHP variable code
* supports property/variable/constant define with PHP primitive value, array, closure or callback
* default visibility is ```PUBLIC```  
* default assign operator is ```=```  

Note, assign of (result from) function/method invoke may be set using VariableMgr::setBody() and [FcnInvokeMgr]

###### VariableMgr Methods

---
Inherited [Common methods]

---

```VariableMgr::factory( varDto )```
* ```varDto``` _VarDto_
    * note *VarDto* below
* Return _static_

```VariableMgr::factory( name [, varType [, default [, summary [, description ]]]] )```
* ```name``` _string_, argument name   ( with or without leading '$')
* ```varType``` _string_, argument varType (hint), default null
  * convenient constants found in PcGenInterface
* ```default```, _mixed_, the argument value if null
* ```summary``` _string_
* ```description``` _string_|_array
* For eol and indents, defaults are used
* Return _static_
* Throws _InvalidArgumentException_
---

```VariableMgr::toArray()```
* Return _array_, result code rows (null-bytes removed) no trailing eol
* Throws _RuntimeException_

```VariableMgr::toString()```
* Return _string_ with code rows (extends toArray), each code row with trailing eol
* Throws _RuntimeException_
---

```VariableMgr::setVisibility( [ visibility ] )```
* Convenient constants found in PcGenInterface
* ```visibility``` _string_, null(empty): no visibility, default 'public'  
* Return _static_
---

```VariableMgr::setStatic( [ static ] )```
* ```static``` _bool_, true: static, false not static (default)
  * if true then ```isConst``` is set to false (below)
* Return _static_
---

```VariableMgr::setVarDto( varDto )```
* ```varDto``` _VarDto_  
    * note *VarDto* below
* Return _static_
* Throws _InvalidArgumentException_
---

```VariableMgr::setName( name )```
* ```name``` _string_ variable/property name 
* Return _static_
* Throws _InvalidArgumentException_
---

```VariableMgr::setInitValue( value )```
* ```value``` _mixed_, variable/property init (or default) PHP primitive or array value
  * assoc array are produced with keys and values, non assoc not
* Return _static_
---

```VariableMgr::setIsConst( const )```
* Results in uppercase constant
* ```const``` _bool_, true : constant, false : NOT, default 
  * if true then ```isStatic``` is set to false (above)
* Return _static_
---

```VariableMgr::setCallBack( class [, method ] )```
* ```class``` _string_
* ```method``` _string_, default null
* usage :
  * simple function (set using 'setBody', below)
  * anonymous function (set using 'setBody', below, and, opt, [FcnFrameMgr]/[FcnInvokeMgr])
  * instantiated sourceObject+method, output passed as an array, result : ```[ $sourceObject, 'methodName' ]```
  * class name (fqcn) and static (factory?) method, output passed as an array, result : ```[ FQCN, 'methodName' ]```
  * instantiated sourceObject, class has an (magic) __call method, result : ```$sourceObject```
  * class name (fqcn), class has an (magic) __callStatic method, result : ```FQCN```
  * instantiated sourceObject, class has an (magic) __invoke method, result : ```$sourceObject```
* Return _static_
* Throws InvalidException
---

```VariableMgr::setBody( ...body )```
* ```body``` _string_|_array_, (multiple) (closure?) logic code (chunks) row(s), 
  * note, code without 'baseIndent' 
* Return _static_
---

```VariableMgr::setOperator( operator )```
* Default assign operator is ```=```  
* ```operator``` _string_, one of ```=```, ```+=```, ..., see [operators]
* Return _static_
* Throws InvalidException
---

#### Misc
_VarDto_ instance creation (go to [VarDto])<br><br>
```VarDto::factory( [ varName [, varType [ default [, summary [, description ]]]]] )```
* ```varName``` _string_
* ```varType``` _string_, variable type (type hint), default null
  * convenient constants found in PcGenInterface
* ```default```, _mixed_, the argument value if null
* ```summary``` _string_
* ```description``` _string_|_array_
---

<small>Return to PcGen [README], [Summary]</small> 

[Common methods]:CommonMethods.md
[FcnFrameMgr]:FcnFrameMgr.md
[FcnInvokeMgr]:FcnInvokeMgr.md
[operators]:https://www.php.net/manual/en/language.operators.assignment.php
[README]:../README.md
[Summary]:Summary.md
[VarDto]:VarDto.md
