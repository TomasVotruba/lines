# Lines

CLI tool for quick size measure of PHP project, runs anywhere

## What are killer features?

* install anywhere - PHP 7.2? PHPUnit 6? Symfony 3? Not a problem, this package **has zero dependencies and works on PHP 7.2+**
* get quick overview of your project size - no details, no complexity, just lines of code
* get easy JSON output for further processing
* we keep it simple, so you can enjoy reading - for more complex operation use static analysis like PHPStan

<br>

## Install

The package is scoped and downgraded to PHP 7.2. So you can install it anywhere with any set of dependencies:

```bash
composer require tomasvotruba/lines --dev
```

## Usage

```bash
vendor/bin/lines measure src
```

For short output:

```bash
vendor/bin/lines measure src --short
```

For json output, just add `--json`:

```bash
vendor/bin/lines measure src --json
```

Also, you can combine them (very handy for blog posts and tweets):

```bash
vendor/bin/lines measure src --short --json
```

## The Measured Items

For the text output, you'll get data like these:

```bash
  Filesystem                                         count
  Directories ......................................... 32
  Files .............................................. 160

  Lines of code                           count / relative
  Code ................................... 15 521 / 70.9 %
  Comments ................................ 6 372 / 29.1 %
  Total .................................. 21 893 /  100 %

  Structure                                          count
  Namespaces .......................................... 32
  Classes ............................................ 134
   * Constants ........................................ 91
   * Methods ....................................... 1 114
  Interfaces .......................................... 20
  Traits ............................................... 4
  Enums ................................................ 1
  Functions ........................................... 36
  Global constants ..................................... 0

  Methods                                 count / relative
  Non-static .............................. 1 058 /   95 %
  Static ..................................... 56 /    5 %

  Public .................................... 875 / 78.5 %
  Protected .................................. 90 /  8.1 %
  Private ................................... 149 / 13.4 %
```

Or in a json format:

```json
{
    "filesystem": {
        "directories": 10,
        "files": 15
    },
    "lines_of_code": {
        "code": 1064,
        "code_relative": 95.4,
        "comments": 51,
        "comments_relative": 4.6,
        "total": 1115
    },
    "structure": {
        "namespaces": 11,
        "classes": 14,
        "class_methods": 88,
        "class_constants": 0,
        "interfaces": 1,
        "traits": 0,
        "enums": 0,
        "functions": 5,
        "global_constants": 3
    },
    "methods_access": {
        "non_static": 82,
        "non_static_relative": 93.2,
        "static": 6,
        "static_relative": 6.8
    },
    "methods_visibility": {
        "public": 70,
        "public_relative": 79.5,
        "protected": 2,
        "protected_relative": 2.3,
        "private": 16,
        "private_relative": 18.2
    }
}
```
