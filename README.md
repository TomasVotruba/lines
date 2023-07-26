# Lines

This is a CLI tool for quick size measure of PHP project.

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
