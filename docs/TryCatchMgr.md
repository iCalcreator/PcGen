[comment]: # (This file is part of PcGen, PHP Code Generation support package. Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence GPL 3.0)

#### TryCatchMgr

The ```TryCatchMgr``` manages try-catch expression
* try-body code is set using TryCatchMgr::setBody()
* catch-bodies code are set using 
  * (single) TryCatchMgr::appendCatch()
  * (array) TryCatchMgr::setCatch()

Constants :
* CatchMgr::EXCEPTION
* CatchMgr::RUNTIMEEXCEPTION
* CatchMgr::INVALIDARGUMENTEXCEPTION

But any string accepted...

###### TryCatchMgr Methods

---
Inherited [Common methods]

---
```TryCatchMgr::factory( tryBody, catchBody ) ```
* Set trybody with 'Exception'-catch and body
* ```tryBody``` _string_|_string[]_   code
* ```catchBody``` _string_|_string[]_ code
* Return _static_
* Throws _InvalidArgumentException_
---

```TryCatchMgr::toArray() ```

* Return _array_, result code rows (null-bytes removed) no trailing eol
* Throws _RuntimeException_

```TryCatchMgr::toString() ```
* Return _array_, result code rows (null-bytes removed) no trailing eol
* Throws _RuntimeException_

---

```TryCatchMgr::isCatchSet() ```
* Return _bool_ true if catch-body is set

```TryCatchMgr::appendCatch( [ exception [, catchBody ]] ) ```
* Append single exception with code-body
* ```exception``` _string_  or constant
* ```catchBody``` _string_|_string[]_ code
* Return _static_
* Throws _InvalidArgumentException_

```TryCatchMgr::setCatch( catch ) ```
* Append exceptions with code-body
* ```catch``` _string[]_
  * _string_ (Exception)
  * _string_ (Exception), catchBody
* Return _static_
* Throws _InvalidArgumentException_

---

#### Example

```
<?php

$code = FcnFrameMgr::init()
    ->setName( 'someFunction' )
    ->setBody(
        TryCatchMgr::init()
            ->setBody( ' /* here comes some code.... */' )
            ->setCatch(
                [
                    [ CatchMgr::INVALIDARGUMENTEXCEPTION, ' /* here comes some code.... */' ],
                    CatchMgr::factory( 'LogicException', ' /* here comes some code.... */' ),
                    [ CatchMgr::RUNTIMEEXCEPTION, ' /* here comes some code.... */' ],
                    CatchMgr::EXCEPTION
               ]
            ->toArray()
    )
    ->toString();

```

Result :

```
    public function someFunction()
    {
        try {
             /* here comes some code.... */
        }
        catch( InvalidArgumentException $e ) {
             /* here comes some code.... */
        }
        catch( LogicException $e ) {
             /* here comes some code.... */
        }
        catch( RuntimeException $e ) {
             /* here comes some code.... */
        }
        catch( Exception $e ) {
        }
    }
```

---

<small>Return to [README] - [Summary]</small>

[Common methods]:CommonMethods.md
[README]:../README.md
[Summary]:Summary.md
