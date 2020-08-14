[comment]: # (This file is part of PcGen, PHP Code Generation support package. Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence GPL 3.0)

#### CtrlStructMgr

The ```CtrlStructMgr``` manages control structure

with
* condition ( operand1 comparisonOperator operand2 )
  * operand : scalar, variable, class property or (class-)function
  * comparisonOperator : class constants exists
*  boolean condition :  variable/property, (class-)function
*  scalar value

* _if_/_elseif_/_else_  (else without condition), if default
* _while_
* _do-while_
* _switch_
* _case_     inside a _switch_ 'body', will automatically add 'break' after
* _default_  same, but no condition

The logic body is set using CtrlStructMgr::setBody()

###### CtrlStructMgr Methods

---
Inherited [Common methods]

---
```CtrlStructMgr::factory( operand [, compOp [, operand2 [, $exprType ]]] ) ```
* ```operand``` _string_ variable
* ```compOp``` _string_       default '==', constant exists
* ```operand2``` _string_ variable
* ```exprType``` _int_  default 'if', constant exists
* Return _static_
* Throws _InvalidArgumentException_
---

```CtrlStructMgr::toArray() ```

* Return _array_, result code rows (null-bytes removed) no trailing eol
* Throws _RuntimeException_

```CtrlStructMgr::toString() ```
* Return _array_, result code rows (null-bytes removed) no trailing eol
* Throws _RuntimeException_

---

```CtrlStructMgr::setExprType( exprType ) ```
* Set expression type
* ```exprType``` _int_  default 'if', constant exists
* Return _static_

```CtrlStructMgr::setIfExprType() ```<br>
```CtrlStructMgr::setElseExprType() ```<br>
```CtrlStructMgr::setElseIfExprType() ```<br>
```CtrlStructMgr::setSwitchExprType() ```<br>
```CtrlStructMgr::setCaseExprType() ```<br>
```CtrlStructMgr::setDefaultExprType() ```<br>
```CtrlStructMgr::setWhileExprType() ```<br>
```CtrlStructMgr::setDoWhileExprType() ```<br>
* Convenient ```CtrlStructMgr::setExprType()``` aliases 
* Return _static_

---

```CtrlStructMgr::setScalar( setScalar ) ```
* Set single cond. (boolean) scalar
* ```setScalar``` _bool_|_float_|_int_|_string_
* Return _static_
* Throws _InvalidArgumentException_

```CtrlStructMgr::setExpression( expression ) ```
* Set single cond. (boolean) PHP expression
* ```expression``` _string_
* Return _static_
* Throws _InvalidArgumentException_

```CtrlStructMgr::setSingleOp( singleOp ) ```
* Set cond. (boolean) as single variable (string), classVariable or function invoke
* ```singleOp``` _string_|[EntityMgr]|[FcnInvokeMgr]
* Return _static_
* Throws _InvalidArgumentException_

```CtrlStructMgr::setThisPropSingleOp( singleOp ) ```
* Set single operand as this class property
* Convenient CtrlStructMgr::setSingleOp() alias
* ```singleOp``` _string_
* Return _static_
* Throws _InvalidArgumentException_

```CtrlStructMgr::setThisFcnSingleOP( singleOp ) ```
* Set single operand as this class function call (no args)
* Convenient CtrlStructMgr::setSingleOp() alias
* ```singleOp``` _string_
* Return _static_
* Throws _InvalidArgumentException_

---

```CtrlStructMgr::setCompOP( compOP ) ```
* Set operand1/operand2 comparison operator
* ```compOP``` _string_ default '==', constants exists
* Return _static_
* Throws _InvalidArgumentException_

---

```CtrlStructMgr::setOperand1( operand ) ```
* Set first operand
* ```operand``` _bool_|_float_|_int_|_string_|[EntityMgr]|[FcnInvokeMgr]
* Return _static_
* Throws _InvalidArgumentException_

```CtrlStructMgr::setThisVarOperand1( operand ) ```
* Set first operand as this class property
* Convenient CtrlStructMgr::setOperand1() alias
* ```operand``` _string_
* Return _static_
* Throws _InvalidArgumentException_

---

```CtrlStructMgr::setOperand2( operand ) ```
* Set second operand
* ```operand``` _bool_|_float_|_int_|_string_|[EntityMgr]|[FcnInvokeMgr]
* Return _static_
* Throws _InvalidArgumentException_

```CtrlStructMgr::setThisVarOperand2( operand ) ```
* Set first operand as this class property
* Convenient CtrlStructMgr::setOperand2() alias
* ```operand``` _string_
* Return _static_
* Throws _InvalidArgumentException_

---

#### if Example

```
<?php
$code = FcnFrameMgr::init()
    ->setName( 'someFunction' )
    ->addArgument( 'value', FcnFrameMgr::STRING_T ) 
    ->setBody(
        CtrlStructMgr::factory(  'value', CtrlStructMgr::EQ, 1 )
            ->setBody( ' // this is if-body' )
            ->toArray(),
        CtrlStructMgr::factory(  'value', CtrlStructMgr::GT, 1 )
            ->setElseIfExprType()
            ->setBody( ' // this is elseIf-body' )
            ->toArray(),
        CtrlStructMgr::init()
            ->setElseExprType()
            ->setBody( ' // this is else-body' )
            ->toArray()
    )
    ->toString();
```

Result :

```
    public function someFunction( string $value )
    {
        if( $value == 1 ) {
             // this is if-body
        } // end if
        elseif( $value > 1 ) {
             // this is elseIf-body
        } // end elseif
        else {
             // this is else-body
        } // end else
    }
```

#### switch Example

```
$code = FcnFrameMgr::init()
    ->setName( 'someFunction' )
    ->addArgument( 'value', FcnFrameMgr::STRING_T )
    ->setBody(
        CtrlStructMgr::init()
            ->setSwitchExprType()
            ->setScalar(true )
            ->setBody(
                CtrlStructMgr::factory(
                    EntityMgr::factory( null, 'value' ),
                    CtrlStructMgr::EQ,
                    1
                )
                    ->setCaseExprType()
                    ->setBody( ' // this is case-body 1' )
                    ->toArray(),
                CtrlStructMgr::factory(
                    EntityMgr::factory( null, 'value' ),
                    CtrlStructMgr::GT,
                    1
                )
                    ->setCaseExprType()
                    ->setBody( ' // this is case-body 2' )
                    ->toArray(),
                CtrlStructMgr::init()
                    ->setDefaultExprType()
                    ->setBody( ' // this is default-body' )
                    ->toArray()
            ) // end setBody
        ->toArray()
    ) // end setBody
    ->toString();

```

Result :

```
    public function someFunction( string $value )
    {
        switch( true ) {
            case ( $value == 1 ) :
                     // this is case-body 1
                break;
            case ( $value > 1 ) :
                     // this is case-body 2
                break;
            default :
                     // this is default-body
                break;
        } // end switch
    }

```

---

<small>Return to [README] - [Summary]</small>

[Common methods]:CommonMethods.md
[EntityMgr]:EntityMgr.ms
[FcnInvokeMgr]:FcnInvokeMgr.md
[README]:../README.md
[Summary]:Summary.md
