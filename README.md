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

## The Measured Items

For the text output, you'll get data like these:

```bash
 ------------------------------ ---------------------
  Metric                                       Count
 ------------------------------ ---------------------
  Directories                                      9
  Files                                           14
 ------------------------------ ---------------------

 ------------------------------ ---------- ----------
  Lines of code                     Count   Relative
 ------------------------------ ---------- ----------
  Code                              1 059     95.8 %
  Comments                             46      4.2 %
  Total                             1 105      100 %
 ------------------------------ ---------- ----------

 ------------------------------ ---------------------
  Structure                                    Count
 ------------------------------ ---------------------
  Namespaces                                      10
  Classes                                         13
   * Constants                                     0
   * Methods                                      78
  Interfaces                                       1
  Traits                                           0
  Enums                                            0
  Functions                                        3
  Global constants                                 3
 ------------------------------ ---------------------

 ------------------------------ ---------- ----------
  Methods                           Count   Relative
 ------------------------------ ---------- ----------
  Non-static                           74     94.9 %
  Static                                4      5.1 %
 ------------------------------ ---------- ----------
  Public                               60     76.9 %
  Protected                             4      5.1 %
  Private                              14     17.9 %
 ------------------------------ ---------- ----------
```

Or in a json format:

```json
{
    "filesystem": {
        "directories": 9,
        "files": 14
    },
    "lines_of_code": {
        "code": 1059,
        "code_relative": 95.8,
        "comments": 46,
        "comments_relative": 4.2,
        "total": 1105
    },
    "structure": {
        "namespaces": 10,
        "classes": 13,
        "class_methods": 78,
        "class_constants": 0,
        "interfaces": 1,
        "traits": 0,
        "enums": 0,
        "functions": 3,
        "global_constants": 3
    },
    "methods": {
        "non_static": 74,
        "non_static_relative": 94.9,
        "static": 4,
        "static_relative": 5.1,
        "public": 60,
        "public_relative": 76.9,
        "protected": 4,
        "protected_relative": 5.1,
        "private": 14,
        "private_relative": 17.9
    }
}
```
