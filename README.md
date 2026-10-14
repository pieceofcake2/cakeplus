# Cakeplus (CakePHP 2 Plugin)

[![GitHub License](https://img.shields.io/github/license/pieceofcake2/cakeplus?label=License)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/pieceofcake2/cakeplus?label=Packagist)](https://packagist.org/packages/pieceofcake2/cakeplus)
![PHP](https://img.shields.io/packagist/dependency-v/pieceofcake2/cakeplus/php?logo=php&logoColor=%23FFFFFF&label=PHP&labelColor=%23777BB4&color=%23FFFFFF)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%205-brightgreen?label=PHPStan)](https://github.com/pieceofcake2/cakeplus/actions/workflows/CI.yml)
![CakePHP](https://img.shields.io/packagist/dependency-v/pieceofcake2/cakeplus/pieceofcake2/cakephp?logo=cakephp&logoColor=%23FFFFFF&label=CakePHP&labelColor=%23D33C43&color=%23FFFFFF)
[![CI](https://img.shields.io/github/actions/workflow/status/pieceofcake2/cakeplus/CI.yml?label=CI)](https://github.com/pieceofcake2/cakeplus/actions/workflows/CI.yml)
[![Codecov](https://img.shields.io/codecov/c/gh/pieceofcake2/cakeplus?label=Coverage)](https://codecov.io/gh/pieceofcake2/cakeplus)

__This is forked for CakePHP2.__

Cakeplus is cakephp plugin and provides some functions for CakePHP.

## Installation

```
composer require pieceofcake2/cakeplus
```

## Feature

We will provide functions as follow.

### Component

#### HtmlEscape

- Execute Html Escape and nl2br to Array Data
  (Option: you can set no escape list in Array Data)

### Behavior

#### AddValidationRule

- Check number of Multi byte character.
- Check difference between 2 fields.
- Check Japanese Hiragana only input.
- Check Japanese Katakana only input.
- Check Japanese Zenkaku only input.
- Check space and Multibyte space only input.

#### ValidationErrorI18n

- set validation error messages with gettext __()

#### ValidationPatterns

- Summarize multiple validation pattern for writing validation define easily.

### Helper

#### FormScreen

- Auto create hidden tag
