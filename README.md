# CakePHP Decimal Plugin

[![CI](https://github.com/dereuromark/cakephp-decimal/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/dereuromark/cakephp-decimal/actions/workflows/ci.yml?query=branch%3Amaster)
[![Latest Stable Version](https://poser.pugx.org/dereuromark/cakephp-decimal/v/stable.svg)](https://packagist.org/packages/dereuromark/cakephp-decimal)
[![codecov](https://codecov.io/gh/dereuromark/cakephp-decimal/branch/master/graph/badge.svg)](https://codecov.io/gh/dereuromark/cakephp-decimal)
[![License](https://poser.pugx.org/dereuromark/cakephp-decimal/license)](https://packagist.org/packages/dereuromark/cakephp-decimal)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.1-8892BF.svg)](https://php.net/)

This is an alternative to
 * the core Decimal type (using plain strings)

As value object you have a few advantages, especially on handling the values inside your business logic.

This branch is for use with **CakePHP 4.2+**. See [version map](https://github.com/dereuromark/cakephp-decimal/wiki#cakephp-version-map) for details.

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
