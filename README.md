# CakePHP Decimal Plugin

[![CI](https://github.com/dereuromark/cakephp-decimal/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/dereuromark/cakephp-decimal/actions/workflows/ci.yml?query=branch%3Amaster)
[![codecov](https://codecov.io/gh/dereuromark/cakephp-decimal/branch/master/graph/badge.svg)](https://codecov.io/gh/dereuromark/cakephp-decimal)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg?style=flat)](https://phpstan.org/)
[![Latest Stable Version](https://poser.pugx.org/dereuromark/cakephp-decimal/v/stable.svg)](https://packagist.org/packages/dereuromark/cakephp-decimal)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.2-8892BF.svg)](https://php.net/)
[![License](https://poser.pugx.org/dereuromark/cakephp-decimal/license.svg)](LICENSE)
[![Total Downloads](https://poser.pugx.org/dereuromark/cakephp-decimal/d/total.svg)](https://packagist.org/packages/dereuromark/cakephp-decimal)
[![Coding Standards](https://img.shields.io/badge/cs-PSR--2--R-purple.svg?style=flat-square)](https://github.com/php-fig-rectified/fig-rectified-standards)

This is an alternative to
* the core Decimal type (using plain strings)

As value object you have a few advantages, especially on handling the values inside your business logic.

This branch is for use with **CakePHP 5.1+**. See [version map](https://github.com/dereuromark/cakephp-decimal/wiki#cakephp-version-map) for details.

## Requirements

- Uses [php-collective/decimal-object](https://github.com/php-collective/decimal-object) and as such requires bcmath extension.

## Installation
Require the plugin through Composer:
```
composer require dereuromark/cakephp-decimal
```

## Setup and Usage
See [Documentation](docs/).

## Demo

Live example see https://sandbox.dereuromark.de/sandbox/decimal-examples/forms
