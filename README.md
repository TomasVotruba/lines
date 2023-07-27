# Lines

This is a CLI tool for quick size measure of PHP project.

## What are killer features?

* install anywhere - PHP 7.2? PHPUnit 6? Symfony 3? Not a problem, this package has zero dependencies and works on PHP 7.2+
* get quick overview of your project size - no details, no complexity, just lines of code
* get easy JSON output for further processing
* we keep it simple, so you can enjoy reading - for more complex operation use static analysis

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

For json output, just add `--json`:

```bash
vendor/bin/lines measure src --json
```

<br>

## How to Read these Results?

* https://matthiasnoback.nl/2019/09/using-phploc-for-quick-code-quality-estimation-part-1/
* https://matthiasnoback.nl/2019/09/using-phploc-for-quick-code-quality-estimation-part-2/
