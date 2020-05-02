[comment]: # (This file is part of PcGen, PHP Code Generation support package. Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence GPL 3.0)

#### Common Methods Methods

These methods are shared by all but *Dto :

---

```Class::__construct( [ eol [, indent [, baseIndent ]]] )```
* ```eol```     _string_, default PHP_EOL
* ```indent```  _string_, default four spaces, for no indent, use '' 
* ```baseIndent```  _string_, default four spaces, for no indent, use '' 
  * The base, leftmost, indent

```Class::init( [ eol [, indent [, baseIndent ]]] )```
* ```eol```     _string_, default PHP_EOL
* ```indent```  _string_, default four spaces, for no indent, use ''
* ```baseIndent```  _string_, default four spaces, for no indent, use '' 
  * The base, leftmost, indent
* Static
* Return _static_

```Class::init( baseClass )```
* ```baseClass``` _BaseA_ any *Mgr class
  * updates eol, indent, baseIndent from ```baseClass```
* Static
* Return _static_
---

```Class::toArray()```
* Return _array_, result code rows (null-bytes removed) no trailing eol
* Throws RuntimeException

```Class::toString()```
* Return _string_ with code rows (extends toArray), each code row with trailing eol
* Throws RuntimeException
---

```Class::getEol()```
* Return _string_

```Class::setEol( eol )```
* ```eol``` _string_, eol chars
* Return _static_
---

```Class::getIndent()```
* Return _string_

```Class::setIndent( indent )```
* ```indent``` _string_, default four spaces, indentations after baseIndent
---

```Class::getBaseIndent()```
* Return _string_

```Class::setBaseIndent( indent )```
* The base, leftmost, indent
* ```indent``` _string_, default four spaces
* Return _static_
---

```Class::setDefaultEol( eol )```
* ```eol``` _string_, eol chars
* Note, eol, set here, will affect the whole package
* Static

```Class::setDefaultIndent( indent )```
* ```indent``` _string_, default four spaces, indentations after baseIndent
* Note, indent, set here, will affect the whole package
* Static

```Class::setDefaultBaseIndent( indent )```
* The base, leftmost, indent
* ```indent``` _string_, default four spaces
* Note, indent, set here, will affect the whole package
* Static

```Class::setTargetPhpVersion( phpVersion )```
* Alter the target PHP version, default (PHP constant) _PHP_VERSION_
  * as for now, used for type hints
* ```phpVersion``` _string_
* Note, phpVersion, set here, will affect the whole package
* Static
---

<small>Return to [README] - [Summary]</small>

[README]:../README.md
[Summary]:Summary.md
