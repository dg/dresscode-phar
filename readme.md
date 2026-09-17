DressCode
=========

DressCode is a checker and fixer of PHP code style built on a lossless syntax tree: every token, whitespace and comment of a source file is kept, rules edit the tree and the printer reproduces the file byte for byte.

This repository is the **distribution** of DressCode: a phar with every dependency inside, so that `composer require` adds nothing to the dependencies of your project. **Development happens in [dg/dresscode](https://github.com/dg/dresscode)**, where the issues and the documentation are; the phar here is built from its tags.

**Status: in development.** Nothing is usable yet; the API, names and behavior are all subject to change.


Installation
------------

```shell
composer require --dev dresscode/dresscode
```

Requires PHP 8.4 or newer with the phar extension. Then:

```shell
vendor/bin/dresscode check
vendor/bin/dresscode fix
```

Without Composer, download `dresscode.phar` from the [releases](https://github.com/dg/dresscode/releases), verify its signature and run it with `php dresscode.phar`.


Plugins
-------

A rule, preset or analysis of your own is written against the API of DressCode and PhpSyntax. This package makes those classes available to your code: the `bootstrap.php` it autoloads serves `DressCode\`, `PhpSyntax\` and the two libraries the API is made of (`Nette\Schema`, `PHPStan\PhpDocParser`) from the phar, and nothing else.
