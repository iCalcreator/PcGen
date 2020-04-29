[comment]: # (This file is part of PcGen, PHP Code Generation support package. Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence GPL 3.0)

#### Common Methods Methods

These methods are shared by all :

---

```Class::__construct( [ eol [, indent ]] )```
* ```eol```     _string_, default PHP_EOL
* ```indent```  _string_, default four spaces, for no indent, use '' 
* Note, eol/indent, set here, will affect the whole package

```Class::init( [ eol [, indent ]] )```
* ```eol```     _string_, default PHP_EOL
* ```indent```  _string_, default four spaces, for no indent, use ''
* Note, eol/indent, set here, will affect the whole package
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

```Class::setEol( eol )```
* ```eol``` _string_, eol chars
* Note, eol, set here, will affect the whole package
* Return _static_

```Class::setIndent( indent )```
* ```indent``` _string_, default four spaces, indentations after baseIndent
* Note, indent, set here, will affect the whole package
* Return _static_

```Class::setbaseIndent( indent )```
* The base, leftmost, indent
* ```indent``` _string_, default four spaces
* Note, baseIndent, set here, will affect the whole package
* Return _static_
---

<small>Return to [README] - [Summary]</small>

[README]:../README.md
[Summary]:Summary.md
