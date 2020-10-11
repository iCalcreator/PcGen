[comment]: # (This file is part of PcGen, PHP Code Generation support package. Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence GPL 3.0)

#### AssignClauseMgr

The ```AssignClauseMgr``` manages the code assign 
of a target class property or variable (value) 

 * from 
   source class property or variable (value), opt (int/variable) index
   (scalar) fixedSourceValue
   ternary or null coalesce expression
   single function/method or chained invokes
   other PHP expression
   
 * default assign operator is ```=```
 * ex result ```$this->property = OtherClass::CONSTANT;```

The result of this class toString()/toArray() methods is used by other classes setBody() method.

###### AssignClauseMgr Methods

---
Inherited [Common methods]

---

```AssignClauseMgr::factory( [ targetClass [, targetVariable, [, targetTndex [, sourceClass [, sourceVariable, [, sourceTndex ]]]]]] )```

* ```targetClass``` _string_ one of null, self, $this, 'otherClass', '$class'
   convenient constants found in PcGenInterface 
* ```targetVariable``` _string_ variable/property name
   uppercase is autodetected as CONSTANT
   variable $-prefixed
* ```targetIndex```  _int_|_string_ opt array index
* ```sourceClass``` _string_ one of null, self,  $this, 'otherClass', '$class'
   convenient constants found in PcGenInterface 
* ```sourceVariable``` _string_ class/variable/property name
   uppercase is autodetected as CONSTANT
   variable $-prefixed
* ```sourceIndex```  _int_|_string_ opt array index
* For eol and indents, defaults are used
* Return _static_

---

```AssignClauseMgr::toArray()```

* Return _array_, result code rows (null-bytes removed) no trailing eol
* Throws _RuntimeException_

```AssignClauseMgr::toString()```

* Return _string_ with code rows (extends toArray), each code row with trailing eol
* Throws _RuntimeException_

---

```AssignClauseMgr::getTarget()```

* Return [EntityMgr]

```AssignClauseMgr::isTargetSet()```

* Return _bool_ true if set, false not

```AssignClauseMgr::setTarget( class [, variable, [, index ]] )```

* ```class``` _string_|[EntityMgr] if string, one of null, self, $this, 'otherClass', '$class'
   convenient constants found in PcGenInterface 
* ```variable``` _string_ class/variable/property name
   uppercase is autodetected as CONSTANT
   variable $-prefixed
* ```index```  _int_|_string_ opt array index
* Return _static_
* Throws _InvalidArgumentException_


```AssignClauseMgr::setThisPropertyTarget( property [, index ] )```

* convenient shortcut for ```AssignClauseMgr::setTarget()```
* Give target result ```$this->property```
* ```property``` _string_
* ```index```  _int_|_string_ opt array index
* Return _static_
* Throws _InvalidArgumentException_


```AssignClauseMgr::setVariableTarget( variable [, index ] )```

* convenient shortcut for ```AssignClauseMgr::setTarget()```
* Give target result ```$variable```
* ```variable``` _string_
* ```index```  _int_|_string_ opt array index
* Return _static_
* Throws _InvalidArgumentException_


```AssignClauseMgr::setForceTargetAsInstance( forceTargetAsInstance )```

* Only applicable for '$targetClass', ignored by the others
* ```forceTargetAsInstance``` _bool_
   true : force ```$targetClass->property```
   false : NOT, default (```$targetClass::$property```)
* Return _static_

---

```AssignClauseMgr::getScalar()```

* Return _bool_|_int_|_float_|_string_, scalar


```AssignClauseMgr::isScalarSet()```

* Return _bool_ true if set, false not


```AssignClauseMgr::setScalar( fixedSourceValue )```

* Set a fixed (scalar) source
* ```fixedSourceValue``` _bool_|_int_|_float_|_string_, scalar
* Return _static_
* Throws InvalidException

---

```AssignClauseMgr::setSourceExpression( expression )```

* Set a PHP expression
* ```expression``` _string_  any PHP expression
* Return _static_
* Throws InvalidException

---

```AssignClauseMgr::getSource()```

* Return [EntityMgr]


```AssignClauseMgr::isSourceSet()```

* Return _bool_ true if set, false not


```AssignClauseMgr::setSource( class [, variable [, index ]] )```

* ```class``` _string_ if string, one of null, self, $this, 'otherClass', '$class'
   convenient constants found in PcGenInterface 
* ```variable``` _string_ class/variable/property/constant name
   variable $-prefixed
* ```index```  _int_|_string_ opt array index
* Return _static_
* Throws _InvalidArgumentException_


```AssignClauseMgr::setSource( source )```

* ```source``` [EntityMgr]
    note ```EntityMgr``` below
* Return _static_
* Throws _InvalidArgumentException_


```AssignClauseMgr::setThisPropertySource( property [, index ] )```

* convenient shortcut for ```AssignClauseMgr::setSource()```
* Give source result ```$this->property```
* ```property``` _string_
* ```index```  _int_|_string_ opt array index
* Return _static_
* Throws _InvalidArgumentException_


```AssignClauseMgr::setVariableSource( variable [, index ] )```

* convenient shortcut for ```AssignClauseMgr::setSource()```
* Give source result ```$variable```
* ```variable``` _string_
* ```index```  _int_|_string_ opt array index
* Return _static_
* Throws _InvalidArgumentException_


```AssignClauseMgr::setSourceIsConst( isConst )```

* ```isConst``` _bool_
   true : force ```$class::CONSTANT```
   false : NOT, (default) ```$class->$constant``` 
* Return _static_


```AssignClauseMgr::setSourceIsStatic( isStatic )```

* ```isConst``` _bool_
   true : force ```$class::variable```
   false : NOT, default, ```$class->$variable``` 
* Return _static_

---

```AssignClauseMgr::getTernaryNullCoalesceExpr()```

* Return [TernaryNullCoalesceMgr]


```AssignClauseMgr::isTernaryNullCoalesceExprSet()```

* Return _bool_ true if set, false not


```AssignClauseMgr::setTernaryNullCoalesceExpr( expr1 [, expr2 [, expr3 [, ternaryOperator ]]])```

* ```expr1``` _string_|[EntityMgr]|[FcnInvokeMgr]|[TernaryNullCoalesceMgr]
* ```expr2``` _string_|[EntityMgr]|[FcnInvokeMgr]
* ```expr3``` _string_|[EntityMgr]|[FcnInvokeMgr]
* ```ternaryOperator``` _bool_ true (default) : ternary expr, false : null coalesce expr
* Return _static_
* Throws _InvalidArgumentException_

---

```AssignClauseMgr::getFcnInvoke()```

* Return [ChainInvokeMgr]  (manages single or chained [FcnInvokeMgr]s)


```AssignClauseMgr::isFcnInvokeSet()```

* Return _bool_ true if set, false not


```AssignClauseMgr::appendInvoke( fcnInvoke )```

* ```fcnInvoke``` [FcnInvokeMgr]
* Return _static_
* Throws _InvalidArgumentException_


```AssignClauseMgr::setFcnInvoke( fcnInvoke )```

* ```fcnInvoke``` [FcnInvokeMgr]\[] 
* Return _static_
* Throws _InvalidArgumentException_


Note on chained invokes

* The first must have a "class" : parent, self, $this, 'otherClass', '$class' when next is set
* All but first must have $this, 'otherClass', '$class'

Ex on _AssignClauseMgr::setFcnInvoke_ input
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

```AssignClauseMgr::setOperator( operator )```

* Set assign operator, default  ```=``` 
* ```operator``` _string_, one of ```=```, ```+=```, ..., see [operators]
* Return _static_
* Throws InvalidException

---

#### Misc

_EntityMgr_ instance creation ([EntityMgr])<br><br>
```EntityMgr::factory( class , fcnName )```

* ```class```, _string_, one of ```null```, ```self```, ```this```, ```otherClass``` (fqcn), ```$class```
   convenient constants found in PcGenInterface
   
* ```fcnName``` _string_, the name

---

#### Example

```
<?php

$code = AssignClauseMgr::init()
    ->setVariableTarget( 'target' )
    ->setVariableSource( 'source' )
    ->toString;

$code .= AssignClauseMgr::init()
    ->setThisPropertyTarget( 'target' )
    ->setThisPropertySource( 'source', 0 )
    ->toString

$code .= AssignClauseMgr::init()
    ->setVariableTarget( 'target' )
    ->setFcnInvoke(
        [
            FcnInvokeMgr::factory( FcnInvokeMgr::THIS_KW, 'function', [ 'argument' ] ),
            FcnInvokeMgr::factory( FcnInvokeMgr::THIS_KW, 'testMethod1', [ 'argument1' ] ),
            FcnInvokeMgr::factory( FcnInvokeMgr::THIS_KW, 'testMethod2' )
        ]
    )
    ->toString



```

Result :

```

$target = $source;
$this->target = $this->source[0];
$target = $this->function( $argument )
    ->testMethod1( $argument1 )
    ->testMethod2();

```

---

<small>Return to [README] - [Summary]</small>

[ChainInvokeMgr]:ChainInvokeMgr.md
[Common methods]:CommonMethods.md
[EntityMgr]:EntityMgr.md
[FcnInvokeMgr]:FcnInvokeMgr.md
[operators]:https://www.php.net/manual/en/language.operators.assignment.php
[PropertyMgr]:PropertyMgr.md
[README]:../README.md
[Summary]:Summary.md
[TernaryNullCoalesceMgr]:TernaryNullCoalesceMgr.md
[VariableMgr]:VariableMgr.md
