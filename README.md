# ExtendsFramework\Logger
[![Build Status](https://travis-ci.org/extendsframework/extends-logger.svg?branch=master)](https://travis-ci.org/extendsframework/extends-logger)
[![Coverage Status](https://coveralls.io/repos/github/extendsframework/extends-logger/badge.svg?branch=master)](https://coveralls.io/github/extendsframework/extends-logger?branch=master)
[![License](https://poser.pugx.org/extendsframework/extends-logger/license)](https://packagist.org/packages/extendsframework/extends-logger)
[![Latest Stable Version](https://poser.pugx.org/extendsframework/extends-logger/v/stable)](https://packagist.org/packages/extendsframework/extends-logger)
[![Total Downloads](https://poser.pugx.org/extendsframework/extends-logger/downloads)](https://packagist.org/packages/extendsframework/extends-logger)

This repository provides a logger to decorate, filter, prioritize and write log messages. 

## Installation

You can install ExtendsFramework\Logger into your project using [Composer](https://getcomposer.org).
 
```bash
$ composer require extendsframework/extends-logger
```

## Example

```php
<?php
declare(strict_types=1);

require 'vendor/autoload.php';

use ExtendsFramework\Logger\Writer\File\FileWriter;
use ExtendsFramework\Logger\Logger;

(new Logger())
    ->addWriter(new FileWriter('./'))
    ->log('Hello world!');

// Example message written to log: 2018-01-04T19:14:58+00:00 CRIT (2): Hello world!
```

## Documentation

The documentation for ExtendsFramework\Logger is available on the
[Github Wiki](https://github.com/extendsframework/extends-logger/wiki).

## Issues

Bug reports and feature requests can be submitted on the
[Github Issue Tracker](https://github.com/extendsframework/extends-logger/issues).
