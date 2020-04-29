[comment]: # (This file is part of PcGen, PHP Code Generation support package. Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence GPL 3.0)

#### PropertyMgr

The ```PropertyMgr``` class, extends [VariableMgr] class, manages class/interface/trait property code

 * supports class property/constant define with PHP primitive value, array,  closure or callback 
 * default visibility is ```PRIVATE```, but for class constants : ```PUBLIC```, static variables : ```PROTECTED```
 * extends [VariableMgr]  with specific class directives
 * set directive to produce getter method for property
   * if single array class property, _Iterator_ is implemented
 * set directive to produce setter method for property
 * set directive to use property as argument in (opt) factory method 
<br><br>

###### PropertyMgr Methods

---

Inherited [Common methods]

---

Inherited [VariableMgr] methods

---

```PropertyMgr::setMakeGetter( makeGetter )```

 * For the class property a get-method is produced, default true
 * ```makeGetter``` _bool_, true: produce, false no produce
   * if true and single array class property, _Iterator_ is implemented
 * Note, not applicable where property is defind as constant or as (class) static property
* Return _static_
---

```PropertyMgr::setMakeSetter( makeSetter )```

 * For the class property a set-method is produced, default true
 * ```makeSetter``` _bool_, true: produce, false no produce
 * Note, not applicable where property is defind as constant or as (class) static property
 * Return _static_
---

```PropertyMgr::setArgInFactory( argInFactory )```

 * The property will act as class::factory() argument
 * ```argInFactory``` _bool_, true: argument, false no class::factory() argument
 * Note, not applicable where property is defind as constant or as (class) static property
 * Return _static_
---

```PropertyMgr::setStatic( [ static ] )```

 * ```static``` _bool_, true: static, false not static (default)
 * Note, if true, makeGetter/makeSetter alters to false, visibility to _PROTECTED_
 * if required, use the ```setVisibility()```-method after this one
 * Return _static_
---

```PropertyMgr::setIsConst( const )```

 * ```const``` _bool_, true : constant, false : no constant, default 
 * Note, if true, makeGetter/makeSetter alters to false, visibility to _PUBLIC_
 * if required, use the ```setVisibility()```-method after this one
 * Return _static_
---

Return to PcGen [README], [Summary] 

[Common methods]:CommonMethods.md
[README]:../README.md
[Summary]:Summary.md
[VariableMgr]:VariableMgr.md
