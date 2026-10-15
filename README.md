# Cakeplus (CakePHP 2 Plugin)

[![GitHub License](https://img.shields.io/github/license/pieceofcake2/cakeplus?label=License)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/pieceofcake2/cakeplus?label=Packagist)](https://packagist.org/packages/pieceofcake2/cakeplus)
![PHP](https://img.shields.io/packagist/dependency-v/pieceofcake2/cakeplus/php?logo=php&logoColor=%23FFFFFF&label=PHP&labelColor=%23777BB4&color=%23FFFFFF)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%205-brightgreen?label=PHPStan)](https://github.com/pieceofcake2/cakeplus/actions/workflows/CI.yml)
![CakePHP](https://img.shields.io/packagist/dependency-v/pieceofcake2/cakeplus/pieceofcake2/cakephp?logo=cakephp&logoColor=%23FFFFFF&label=CakePHP&labelColor=%23D33C43&color=%23FFFFFF)
[![CI](https://img.shields.io/github/actions/workflow/status/pieceofcake2/cakeplus/CI.yml?label=CI)](https://github.com/pieceofcake2/cakeplus/actions/workflows/CI.yml)
[![Codecov](https://img.shields.io/codecov/c/gh/pieceofcake2/cakeplus?label=Coverage)](https://codecov.io/gh/pieceofcake2/cakeplus)

Cakeplus provides a component, behaviors and a helper for CakePHP 2 applications, especially
Japanese ones: HTML escaping of view data, multibyte and Japanese validation rules, translated
validation messages, reusable validation patterns and hidden form fields. This is a fork of
[ichikaway/cakeplus](https://github.com/ichikaway/cakeplus) for
[pieceofcake2/cakephp](https://github.com/pieceofcake2/cakephp).

## Documentation

- Website: https://pieceofcake2.github.io/cakeplus/
- Markdown: [docs/README.md](docs/README.md) (for AI tools and offline reading)

The Markdown documentation is also included in the Composer package, in the plugin's directory: `plugins/Cakeplus/docs/README.md` (the location depends on your `installer-paths`). Install the package with `--dev` if you only need it for development.

## Requirements

* PHP 8.0+
* pieceofcake2/cakephp 2.12+

## Installation

```bash
composer require pieceofcake2/cakeplus
```

## Contributing

See the [contribution guidelines](https://github.com/pieceofcake2/.github/blob/master/CONTRIBUTING.md).

## License

The MIT License. See [LICENSE](LICENSE).
