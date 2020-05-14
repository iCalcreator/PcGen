[comment]: # (This file is part of PcGen, PHP Code Generation support package. Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved, licence GPL 3.0)
# PcGen

###### the PHP Code Generation support package

* create PHP class / interface / trait code<br>
  * with namespace, use, extends, implements 
  * with constuctor and factory methods 
  * with constants and properties with opt. getter(+iterator) and setter methods
  * allow insert of (pre-produced, logic) code 

* create docBlocks
  * in compliance with [phpdoc]

* create PHP function/method frame (shell) code
  * with arguments and closure use variables
  * with property(/variable) set code
  * with method return code
  * allow insert of (pre-produced, logic) code 

* create code for single or chained function/method invoke(s)
  
* create define variable property/variable/constant code
  * with PHP primitive value, array, closure or callback 

* create code for variable/property value assign from
  * variable/property value
  * (scalar) fixedSourceValue
  * PHP expression
  * constant
  * function/method invoke(s)
    
* create code for function/method return of 
  * variable/property value
  * (scalar) fixedSourceValue
  * PHP expression
  * constant  
  * function/method invoke(s)
   
More info in the PcGen [Summary].

--- 
###### Misc

The target PHP version code is, for now, the current PHP version. 

Using a PHP reserved name as _name_ (ex FQCN/className) will thow an InvalidArgumentException. 

You may need to readjust result output code style and indents.


###### Tests

Tests are executed in ```DISPLAY``` mode, to alter, update _PHP_ const in top of ```phpunit.xml```.  


###### Support

For support use [github.com PcGen]. Non-emergence support issues are, unless sponsored, fixed in due time.


###### Sponsorship

Donation using <a href="https://paypal.me/kigkonsult?locale.x=en_US" rel="nofollow">paypal.me/kigkonsult</a> are appreciated. 
For invoice, <a href="mailto:ical@kigkonsult.se">please e-mail</a>.

###### INSTALL

``` php
composer require kigkonsult/pcgen:dev-master
```

Composer, in your `composer.json`:

``` json
{
    "require": {
        "kigkonsult/pcgen": "dev-master"
    }
}
```

Otherwise , download and acquire..

``` php
namespace Kigkonsult\PcGen;
...
include 'pathToSource/Kigkonsult/PcGen/autoload.php';
```


###### License

This project is licensed under the GPLv3 License

[Composer]:https://getcomposer.org/
[github.com PcGen]:https://github.com/iCalcreator/PcGen
[phpdoc]:https://phpdoc.org
[Summary]:docs/Summary.md
