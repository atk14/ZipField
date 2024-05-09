Change Log
==========

All notable changes to this project will be documented in this file.

## [0.2.5] - 2024-05-09

* 11c83c1 - Added special checks for CZ and SK ZIP codes

## [0.2.4] - 2023-07-13

- Added patterns for EG, GB, MA, MD, NO, NZ, PE, SM, TN, TR and ZA postal codes

## [0.2.3] - 2023-07-13

- Added patterns for US, RU, IN, UA, AR, BR, MX, CA, CH, CN, DK, IL and JP postal codes
- Package is compatible with PHP>=5.6.0

## [0.2.2] - 2023-01-05

* a96bb3a - Fix for PHP8.1

## [0.2.1] - 2019-11-20

- error messages can be overridden by option in constructor
- format hints can be overridden by option in constructor

## [0.2] - 2019-11-19

- Added patterns for all EU countries
- Method ZipField::is_valid_for() does not unset the code when it's not valid
- ZipField works expectable with option "null_empty_output" = false

## [0.1] - 2019-11-06

First tagged release
