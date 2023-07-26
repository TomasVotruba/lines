# Lines

`lines` is a tool for quickly measuring the size and analyzing the structure of a PHP project.

## Install

The package is scoped and downgraded to PHP 7.2. So you can install it anywhere with any set of dependencies:

```bash
composer require tomasvotruba/lines --dev
```

## Usage

@todo json

```bash
vendor/bin/lines analyze src --json
```

Or via text output:

```bash
vendor/bin/lines analyze src

Directories                                          3
Files                                               10

Size
    Lines of Code (LOC)                             1882
    Comment Lines of Code (CLOC)                     255 (13.5%)
    Non-Comment Lines of Code (NCLOC)               1627 (86.4%)
    Logical Lines of Code (LLOC)                     377 (20.0%)
        Classes                                        351 (93.1%)
            Average Class Length                          35
                Minimum Class Length                         0
                Maximum Class Length                       172
            Average Method Length                          2
                Minimum Method Length                        1
                Maximum Method Length                      117
            Functions                                        0 (0.0%)
                Average Function Length                        0
                Not in classes or functions                     26 (6.9%)

Structure
    Namespaces                                         3
    Interfaces                                         1
    Traits                                             0
    Classes                                            9
        Abstract Classes                                 0 (0.0%)
        Concrete Classes                                 9 (100.0%)
    Methods                                          130
        Scope
            Non-Static Methods                           130 (100.0%)
            Static Methods                                 0 (0.0%)
        Visibility
            Public Methods                               103 (79.2%)
            Non-Public Methods                            27 (20.7%)
    Functions                                          0
        Named Functions                                  0 (0.0%)
        Anonymous Functions                              0 (0.0%)
    Constants                                          0
        Global Constants                                 0 (0.0%)
        Class Constants                                  0 (0.0%)
```


## How to Read these Results?

* https://matthiasnoback.nl/2019/09/using-phploc-for-quick-code-quality-estimation-part-1/
* https://matthiasnoback.nl/2019/09/using-phploc-for-quick-code-quality-estimation-part-2/
