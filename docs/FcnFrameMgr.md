[comment]: # (This file is part of PcGen, PHP Code Generation support package. Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence GPL 3.0)

#### FcnFrameMgr

The ```FcnFrameMgr``` class manages PHP function/method/closure frame (shell) code
* arguments, closures use variables
* method property(/variable) set code
* input of body (logic)
* method return (this, parent, self, constant, property, variable, scalar) code
* default visibility is ```PUBLIC```  

Note, invoke of functions/methods is managed by [FcnInvokeMgr]/[ChainInvokeMgr]

###### FcnFrameMgr Methods

---
Inherited [Common methods]

---

```FcnFrameMgr::factory( name [, arguments ] )```
* ```name```       _string_, function/method name
* ```arguments``` _array_, note ```FcnFrameMgr::setArguments()``` below
* For eol and indents, defaults are used
* Static
* Return _static_
---

```FcnFrameMgr::toArray()```
* Return _array_, result code rows (null-bytes removed) no trailing eol
* Throws RuntimeException

```FcnFrameMgr::toString()```
* Return _array_, result code rows (null-bytes removed) no trailing eol
* Throws RuntimeException
---

```FcnFrameMgr::setVisibility( [ visibility ] )```
* Convenient constants found in PcGenInterface
* ```visibility``` _string_, null(empty): no visibility, default 'public'  
* Return _static_
---

```FcnFrameMgr::setStatic( [ static ] )```
* Set method as static
* ```static``` _bool_, true: static, false not static (default)
* Return _static_
---

```FcnFrameMgr::setName( name )```
* Function/method name, note, not for closures
* ```name``` _string_ 
* Return _static_
* Throws InvalidArgumentException
---

```FcnFrameMgr::addArgument( argument )```
* ```argument``` _ArgumentDto_
  * note ```ArgumentDto``` below
  * opt with set directives for _by-reference_, _updClassProp_, _nextVarPropIndex_, below 
* Return _static_

```FcnFrameMgr::addArgument( varDto [, by-reference [, updClassProp [, nextVarPropIndex ]]] )```
* ```varDto``` _VarDto_
  * note ```VarDto``` below
* ```by-reference``` _bool_, 
  * if true argument is going to be passed as reference
  * default false
* ```updClassProp``` _int_, default ```ArgumentDto::NONE```
  * ```ArgumentDto::BEFORE``` : argument (value) will update class instance property before opt set method body
  * ```ArgumentDto::AFTER``` : argument (value) will update class instance property after opt set method body (but before opt. set return(value))
  * ```ArgumentDto::NONE``` : no update
  * default no update
* ```nextVarPropIndex``` _bool_
  * only if argument type/value array (and ! ```ArgumentDto::NONE```, above) 
  * If not set, default, false.
  * if true, the argument (value) will append the class property array
* Return _static_

```FcnFrameMgr::addArgument( name [, varType [, default [, by-reference [, updClassProp [, nextVarPropIndex ]]]]] )```
* ```name``` _string_, argument name   ( with or without leading '$')
* ```varType``` _string_, argument varType (hint), default null
  * convenient constants found in PcGenInterface
* ```default```, _mixed_, the argument value if null
* ```by-reference``` _bool_, above
* ```updClassProp``` _bool_, above 
* ```nextVarPropIndex``` _bool_ above
* Return _static_
* throws InvalidArgumentException

```FcnFrameMgr::setArguments( argumentSets )```
* ```argumentSets``` _array_, elements any of below 
  * name, _string_, argument name
  * _ArgumentDto_
    * note ```ArgumentDto``` below
  * array( varDto [, by-reference [, updClassProp [, nextVarPropIndex ]]] (above)
    * note ```VarDto``` below
  * array( name [, varType [, default [, by-reference [, updClassProp [, nextVarPropIndex ]]]]] ) (above)
* Return _static_
* Throws InvalidArgumentException
---

```FcnFrameMgr::addVarUse( argumentDto )```
* Add closure use, single variable
* ```argumentDto``` _ArgumentDto_
  * note ```ArgumentDto``` below
* Return _static_

```FcnFrameMgr::addVarUse( varDto [, by-reference ] )```
* Add closure use, single variable
* ```varDto``` __VarDto_
  * note ```VarDto``` below
* ```by-reference``` _bool_, passing variable by reference or not, default false
* Return _static_

```FcnFrameMgr::addVarUse( name [, by-reference ] )```
* Add closure use, single variable
* ```name``` _string_, variable name, with or without leading '$'
* ```by-reference``` _bool_, passing variable by reference or not, default false
* Return _static_
* Throws InvalidArgumentException

```FcnFrameMgr::setVarUse( useVariableSets )```
* Set sets of closure use variables
* ```useVariableSets``` _array_, items any of below 
  * ```argumentDto``` _ArgumentDto_
    * note ```ArgumentDto``` below
  * ```varDto``` __VarDto_
    * note ```VarDto``` below
  * ```name``` _string_, variable name, with or without leading '$'
  * array( VarDto [, by-reference ] ), above
  * array( name [, by-reference ] ), above
* Return _static_
* Throws InvalidArgumentException
---

```FcnFrameMgr::setBody( ...body )```
* ```body``` _string|array_, (multiple) logic code (chunks) row(s), 
  * note, code without 'baseIndent' 
* Return _static_
---

```FcnFrameMgr::setReturnValue( class [, source [, index ]] )```
* Set directive for method/function end-up return code, aliases below
* ```class``` _string_ one of null, 'parent', 'self', 'static', 'this', fqcn, '$class'
  * Convenient constants found in PcGenInterface
* ```source``` _string_
* ```index``` _int_|string_ array index
* Return _static_
* Throws InvalidArgumentException

```FcnFrameMgr::setReturnFixedValue( returnArg )```
* ```FcnFrameMgr::setReturnValue()``` alias
* Set directive for method/function end-up scalar return code (ex 'return true;')
* ```returnArg``` _bool_|_int_|_float_|_string_
* Return _static_
* Throws InvalidArgumentException

```FcnFrameMgr::setReturnThis()```
* ```FcnFrameMgr::setReturnValue()``` alias
* Set directive for method end-up class return code ('return $this;')
* Return _static_

```FcnFrameMgr::setReturnProperty( returnArg, returnArg2 )```
* ```FcnFrameMgr::setReturnValue()``` alias
* Set directive for method end-up class return code (ex 'return $this->returnArg;')
* Dynamic variables (ex '{$varDto}') not supported
* ```returnArg``` _string_ 
* ```returnArg2``` _int_|_string_
  * 'return $this->returnArg\[$returnArg2];' (if string)
* Return _static_
* Throws InvalidArgumentException

```FcnFrameMgr::setReturnVariable( returnArg, returnArg2 )```
* ```FcnFrameMgr::setReturnValue()``` alias
* Set directive for method/function end-up return code (ex 'return $returnArg;')
* Dynamic variables (ex '{$varDto}') not supported
* ```returnArg``` _string_
* ```returnArg2``` _int_|_string_, 
  * 'return $returnArg\[$returnArg2];' (if string)
* Return _static_
* Throws InvalidArgumentException
---

###### ArgumentDto

_ArgumentDto_ instance creation ([ArgumentDto])<br><br>
```ArgumentDto::factory( name [, type [, default [, summary [, description ]]]] )```
* ```name``` _string_, argument name   ( with or without leading '$')
* ```varType``` _string_, variable type (type hint), default null
  * convenient constants found in PcGenInterface
* ```default```, _mixed_, the argument value if null
* ```summary``` _string_, the [phpdoc] summary
* ```description``` _string_|_array_, the [phpdoc] description
<br><br>


###### VarDto
 
_VarDto_ instance creation ([VarDto])<br><br>
```VarDto::factory( [ varName [, varType [ default [, summary [, description ]]]]] )```
* ```varName``` _string_
* ```varType``` _string_, variable type (type hint), default null
  * convenient constants found in PcGenInterface
* ```default```, _mixed_, the argument value if null
* ```summary``` _string_, the [phpdoc] summary
* ```description``` _string_|_array_, the [phpdoc] description
---

<small>Return to PcGen [README], [Summary]</small> 

[ArgumentDto]:ArgumentDto.md
[ChainInvokeMgr]:ChainInvokeMgr.md
[Common methods]:CommonMethods.md
[FcnInvokeMgr]:FcnInvokeMgr.md
[phpdoc]:https://phpdoc.org
[README]:../README.md
[Summary]:Summary.md
[VarDto]:VarDto.md
