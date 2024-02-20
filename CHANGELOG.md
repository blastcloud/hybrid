# Changelog
All notable changes to this project will be documented in this file.

## [2.1.0] - 2024-02-19
- Add support back for PHPUnit 9.6 and PHP 8.1. Now includes support for the following combinations:

| PHP | PHPUnit     |
| - |-------------|
| 8.1 | 9.6, 10     |
| 8.2 | 9.6, 10, 11 |
| 8.3 | 9.6, 10, 11 |

## [2.0.2] - 2024-02-03
- Add support for PHP 8.3
- Add support for HTTP Client 7
- Add support for PHPUnit 11
- Change macros type hints to support using both Hybrid and Guzzler at once

## [2.0] - 2023-08--03
- Adding support for PHPUnit 10
- Updating dependencies
- Dropping support for PHP 8.0, as PHPUnit 10 only supports 8.1 and up
- Adding support for HttpClient 6

## [1.1.3] - 2022-12-27
- Add support for PHP 8.2, remove all versions below 8.0
- Updated dependencies
- Corrected code with deprecation warnings

## [1.1.2] - 2020-12-04
- Updating dependencies
- Support for PHP 8

## [1.1.1] - 2020-03-09
- Update to support PHPUnit 9
- Drop support for PHPUnit below 8.2
- Drop support for PHP 7.1

## [1.1.0] - 2020-01-10
- Updating CI to test on 7.4
  - This will be the last release supporting PHP 7.1
- Added new methods: withoutQuery, withQueryKey, and withQueryKeys

## [1.0.3] - 2019-12-03
- Security dependency update. PR provided by Github helpbot

## [1.0.2] - 2019-10-03
- Updated to the latest version of `blastcloud/chassis`.
- Fix for `InvokedRecorder` being removed from PHPUnit 8.4  Type hinting was simply removed from the `expects()` method. Will have to rely on users simply reading the documentation.

## [1.0.1] - 2019-09-06
- Fix for capitalization issue on `macros.php`

## [1.0.0] - 2019-09-06
- Initial Release
