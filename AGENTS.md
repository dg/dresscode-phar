# To My Agents!

It is my fervent wish that this file guide every AI coding agent working with code in this repository.

## What this repository is

This is the **distribution** of DressCode, the package a project installs. It holds no source of the tool:
the code, the tests, the issues and the documentation live in the source repository `dg/dresscode`, and the
phar here is built from its tags.

The name `dresscode/dresscode` is the one this package is to carry, but Packagist still points it at the
source repository. The switch belongs to the first `dev-master` built here, before the tag 1.0, because
a workflow that fetches `dev-master` must not break in between.

A project installs this package and gets `dresscode.phar` with every dependency inside, so that a checker
of coding style adds nothing to what the project itself depends on. That is the whole point of the split:
the tool needs `nette/utils` 4 and `phpstan/phpdoc-parser` 2, and a project must never have to agree with it.

**Nothing in this repository fixes a bug of DressCode.** A change of behaviour is made in the source
repository and arrives here as a new phar. What is edited here is only the package around the phar.

## What is in it

- `dresscode.phar`, with its signature and its checksum beside it, once a build puts them here; today there is
  none. It is an artifact: never opened, never patched, never written by hand.
- `bin/dresscode`, the wrapper Composer links into `vendor/bin`, beside the phar itself, which `composer.json`
  lists as a bin too. The wrapper defines `__DRESSCODE_RUNNING__` and requires the phar, so the phar runs on
  the copy of DressCode it carries.
- `bootstrap.php`, autoloaded by Composer, which serves the API out of the phar to the code of the project
  (see below).
- `readme.md` and `license.md`, which are what a visitor of Packagist reads.

## The autoloader is the delicate part

`PharAutoloader` exists so that a plugin, a rule or a preset of a project can be written and tested against
the API with nothing but this package installed. Three properties of it are not negotiable:

- **It serves only the namespaces the API is made of**: `DressCode\`, `PhpSyntax\`, `Nette\Schema\`,
  `PHPStan\PhpDocParser\` and the prefixed `_DressCode\` the served classes need. Everything else in the
  phar stays inside it, so the phar can never shadow a class the project has of its own.
- **It steps aside when the phar itself runs.** `__DRESSCODE_RUNNING__` is defined by the wrapper before the
  phar is required; the autoloader then loads nothing, because inside the phar the classes are already there.
  Two copies of DressCode in one process are the normal case, not an accident.
- **It registers nothing globally beyond its own `spl_autoload_register`** and reads the maps of the phar
  lazily, on the first class it is asked for, so a project that never touches the API pays nothing.

A missing phar stream wrapper is an error with a sentence that names the extension, not a class that cannot
be found.

## Conventions

- The two PHP files follow the source repository: PHP 8.4 or newer, `declare(strict_types=1)`, tabs, the Nette
  coding standard. No checker runs here, so that is kept by hand.
- **Every tag of the source is built into a tag of the same name here, and the tags are the history.** The
  `master` of this repository is a single commit, force-pushed over the newest tag with every build, so that
  the repository grows by releases and not by builds; nothing is ever committed on top of an old build.
- `.gitattributes` keeps the development files out of the distributed archive and the phar out of the line
  ending translation. A new file that is not for the user of the package is marked `export-ignore`.

## Status

In development. Nothing is released, no phar is built yet, and the API, the names and the behaviour of
DressCode are all still moving.
