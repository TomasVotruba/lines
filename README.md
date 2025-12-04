# Lines of code and PHP Features

CLI tool for quick size measure of PHP project, and real used PHP features.

Zero dependencies. Runs anywhere.

<br>

## What are killer features?

* install anywhere - PHP 7.2? PHPUnit 6? Symfony 3? Not a problem, this package **has zero dependencies and works on PHP 7.2+**
* get quick overview of your project size - no details, no complexity, just lines of code
* get easy **JSON output** for further processing
* measure **used PHP features in your project** - how much PHP 8.0-features used? How many attributes? How many arrow function? How many union types?

<br>

## Install

The package is scoped and downgraded to PHP 7.2. So you can install it anywhere with any set of dependencies:

```bash
composer require tomasvotruba/lines --dev
```

<br>

## 1. Measure Lines and Size

```bash
vendor/bin/lines measure
```

By default, we measure the root directory. To narrow it down, provide explicit path:

```bash
vendor/bin/lines mesaure src
```

For short output:

```bash
vendor/bin/lines measure --short
```

For json output, just add `--json`:

```bash
vendor/bin/lines measure --json
```

Also, you can combine them (very handy for blog posts and tweets):

```bash
vendor/bin/lines measure --short --json
```

<br>

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

<br>

### Longest files

Are you looking for top 10 longest files?

```bash
vendor/bin/lines measure --longest
```

↓

```bash
  Longest files                                 line count
  src/Measurements.php ............................... 320
  src/Console/OutputFormatter/TextOutputFormatter.php  136
  src/NodeVisitor/StructureNodeVisitor.php ........... 124
  src/Console/Command/MeasureCommand.php .............. 98
  src/Analyser.php .................................... 92
```

<br>

### Scan package in `/vendor`

This tool measures *your code*, not the 3rd party libraries. It skips `/vendor` directory by default to avoid false positives. If you want to measure vendor files too, use `--allow-vendor` option:

```bash
 vendor/bin/lines measure vendor/rector/rector --allow-vendor
```

<br>

## 2. Feature Counter

Two codebases using PHP 8.4 in `composer.json`, are not the same codebases. One has zero type param/return/property declarations, other has promoted properties. Reveal their real value by counting PHP feature they actually use.

```bash
vendor/bin/lines features src
```

This command:

* scans your codebase,
* count PHP feature being used from which PHP version,
* gives you quick overview of how modern the codebase really is


↓

```bash
 ------------- ----------------------------------------------- ------------
  PHP version   Feature count
 ------------- ----------------------------------------------- ------------
  7.0           Parameter types                                      2 793
  7.0           Return types                                         1 736
  7.0           Strict declares                                        492
  7.0           Space ship <=> operator                                  0
 ------------- ----------------------------------------------- ------------
  7.1           Nullable type (?type)                                  333
  7.1           Void return type                                       317
  7.1           Class constant visibility                              557
 ------------- ----------------------------------------------- ------------
  7.2           Object type                                             14
 ------------- ----------------------------------------------- ------------
  7.3           Coalesce ?? operator                                    69
 ------------- ----------------------------------------------- ------------
  7.4           Typed properties                                       156
  7.4           Arrow functions                                         38
  7.4           Coalesce assign (??=)                                    0
 ------------- ----------------------------------------------- ------------
  8.0           Named arguments                                         10
  8.0           Union types                                            147
  8.0           Match expression                                         1
  8.0           Nullsafe method call/property fetch                      0
  8.0           Attributes                                               0
  8.0           Throw expression                                       111
  8.0           Promoted properties                                    596
 ------------- ----------------------------------------------- ------------
  8.1           First-class callables                                    8
  8.1           Readonly property                                        3
  8.1           Intersection types                                       0
  8.1           Enums                                                    0
 ------------- ----------------------------------------------- ------------
  8.2           Readonly class                                         182
 ------------- ----------------------------------------------- ------------
  8.3           Typed class constants                                    0
 ------------- ----------------------------------------------- ------------
  8.4           Property hooks                                           0
 ------------- ----------------------------------------------- ------------
```

<br>

That's it. Happy coding!


