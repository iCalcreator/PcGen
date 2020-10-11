[comment]: # (This file is part of PcGen, PHP Code Generation support package. Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence GPL 3.0)

#### TernaryNullCoalesceMgr

The ```TernaryNullCoalesceMgr``` class manages 

 * The ternary operator expression 'expr1 ? expr2 : expr3'
   evaluates to expr2 if  expr1 evaluates to TRUE  and expr3 if expr1 evaluates to FALSE.
 * The ternary operator expression 'expr1 ?: expr3'
    returns expr1 if expr1 evaluates to TRUE and expr3 otherwise.

 * The (PHP7+) null coalescing operator expression 'expr1 ?? expr2'
    evaluates to expr2 if expr1 is NULL, and expr1 otherwise.
   In particular, this operator does not emit a notice
   if the left-hand side value does not exist, just like isset().
   This is especially useful on array keys.
 
 * Expression defind as one of
   simple expression i.e. constant, variable or class property (array)
  method/function invoke, opt with arguments,
  no support for dynamic methodNames, $this->{$method}
  
* used by [AssignClauseMgr] and [ReturnClauseMgr]

* The result expression is enclosed in parenthesis.

###### TernaryNullCoalesceMgr Methods

---
Inherited [Common methods]

---

```TernaryNullCoalesceMgr::factory( expr1 [, expr2, [, expr3 ]] )```

* ```expr1``` _string_|[EntityMgr]|[FcnInvokeMgr] 
* ```expr2``` _string_|[EntityMgr]|[FcnInvokeMgr] 
* ```expr3``` _string_|[EntityMgr]|[FcnInvokeMgr] 
* Return _static_
* Throws _InvalidArgumentException_

---


```TernaryNullCoalesceMgr::toArray()```
* Return _array_, result code rows (null-bytes removed) no trailing eol
* Throws _RuntimeException_


```TernaryNullCoalesceMgr::toString()```

* Return _string_ with code rows (extends toArray), each code row with trailing eol
* Throws _RuntimeException_

---

```TernaryNullCoalesceMgr::setTernaryOperator( ternaryOperator )```

* ```ternaryOperator``` _bool_ true = ternary expr (default), false = null coalesce expr 
* Return _static_

---

```TernaryNullCoalesceMgr::getExpr1()```

* Return [EntityMgr]|[FcnInvokeMgr]


```TernaryNullCoalesceMgr::setExpr1( expr1 )```

* ```expr1``` _string_|[EntityMgr]|[FcnInvokeMgr]
  * variable will be $-prefixed
* Return _static_
* Throws _InvalidArgumentException_

---

```TernaryNullCoalesceMgr::getExpr2()```

* Return [EntityMgr]|[FcnInvokeMgr]


```TernaryNullCoalesceMgr::setExpr2( expr2 )```

* ```expr2``` _string_|[EntityMgr]|[FcnInvokeMgr]
  * variable will be $-prefixed
* Return _static_
* Throws _InvalidArgumentException_

---

```TernaryNullCoalesceMgr::getExpr3()```

* Return [EntityMgr]|[FcnInvokeMgr]


```TernaryNullCoalesceMgr::setExpr3( expr3 )```

* ```expr3``` _string_|[EntityMgr]|[FcnInvokeMgr]
  * variable will be $-prefixed
* Return _static_
* Throws _InvalidArgumentException_

---


Example : 

```
TernaryNullCoalesceMgr::factory( 'var1', 'var2', 'var3' )->toString(); 
```

Result : 

``` ( $var1 ? $var2 : $var3 ) ```


Example : 
```
TernaryNullCoalesceMgr::factory( 'var1', null, 'var3' )->toString(); 
```

Result : 
```( $var1 ?: $var3 )```

Example : 

```
TernaryNullCoalesceMgr::factory( 'var1', 'var2' )
    ->setTernaryOperator( false )
    ->toString(); 
```

Result : 
``` ( $var1 ?? $var2 )```

---

<small>Return to [README] - [Summary]</small>

[AssignClauseMgr]:AssignClauseMgr.md
[EntityMgr]:EntityMgr.md
[Common methods]:CommonMethods.md
[FcnInvokeMgr]:FcnInvokeMgr.md
[README]:../README.md
[ReturnClauseMgr]:ReturnClauseMgr.md
[Summary]:Summary.md
