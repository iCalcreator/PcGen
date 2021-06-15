[comment]: # (This file is part of PcGen, PHP Code Generation support package. Copyright 2020-21 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence GPL 3.0)

#### ArgumentDto

The ```ArgumentDto``` class, extends [VarDto] class, manages function/method arguments base data and has only getter and setter methods, no logic.
Used in [ClassMgr], [FcnFrameMgr] and [FcnInvokeMgr], defining arguments.

###### ArgumentDto Methods

---
Inherited [VarDto] methods

---
```ArgumentDto::__function( varDto )```

* ```varDto``` _VarDto_
* Return _static_
* Static
* Throws _InvalidArgumentException_

---

```ArgumentDto::isByReference()```

* Return _bool_, true, if argument is passed as reference
* Throws _InvalidArgumentException_

```ArgumentDto::setByReference( by-reference )```

* If not set, default, false.
* ```by-reference``` _bool_, if true argument is going to be passed as reference
* Return _static_
---

```ArgumentDto::getUpdClassProp()```

* Return _int_
  ```ArgumentDto::BEFORE``` : argument (value) will update the class property first in method body
   ```ArgumentDto::AFTER``` : argument (value) will update the class property last in method body (but before opt. set return(value))
   ```ArgumentDto::NONE``` : no update
* Throws _InvalidArgumentException_

```ArgumentDto::setUpdClassProperty( updClassProp )```

* If not set, default, no update.
* ```updClassProp``` _int_, default ```ArgumentDto::BEFORE```
   ```ArgumentDto::BEFORE``` : argument (value) will update the class property before opt set method body
  * ```ArgumentDto::AFTER``` : argument (value) will update the class property after opt set method body (but before opt. set return(value))
  * ```ArgumentDto::NONE``` : no update
* Return _static_

---

```ArgumentDto::isNextVarPropIndex()```

* Return _bool_, true, if argument (value) will append the class property array
* Throws _InvalidArgumentException_

```ArgumentDto::setNextVarPropIndex( nextVarPropIndex )```

* only if argument type/value array (and ! ```ArgumentDto::NONE```, above) 
* If not set, default, false.
* ```nextVarPropIndex``` _bool_
  if true, the argument (value) will append the class property array
* Return _static_
---

<small>Return to [README] - [Summary]</small>

[ClassMgr]:ClassMgr.md
[FcnFrameMgr]:FcnFrameMgr.md
[FcnInvokeMgr]:FcnInvokeMgr.md
[README]:../README.md
[Summary]:Summary.md
[VarDto]:VarDto.md
