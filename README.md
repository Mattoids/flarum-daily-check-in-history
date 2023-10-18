# Check In History

![License](https://img.shields.io/badge/license-LPL-1.02-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/mattoid/daily-check-in-history.svg)](https://packagist.org/packages/mattoid/daily-check-in-history) [![Total Downloads](https://img.shields.io/packagist/dt/mattoid/daily-check-in-history.svg)](https://packagist.org/packages/mattoid/daily-check-in-history)

A [Flarum](http://flarum.org) extension. daily check in history

## Installation

Install with composer:

```sh
composer require mattoid/daily-check-in-history:"*"
```

## Updating

```sh
composer update mattoid/daily-check-in-history:"*"
php flarum migrate
php flarum cache:clear
```

## Links

- [Packagist](https://packagist.org/packages/mattoid/daily-check-in-history)
- [GitHub](https://github.com/mattoid/daily-check-in-history)
- [Discuss](https://discuss.flarum.org/d/PUT_DISCUSS_SLUG_HERE)


## Backend Boilerplate Generation: Generates different types of backend classes and/or extenders, ready to be used.
- `flarum-cli make backend api-controller [PATH]`
- `flarum-cli make backend api-serializer [PATH]`
- `flarum-cli make backend api-serializer-attributes [PATH]`
- `flarum-cli make backend command [PATH]`
- `flarum-cli make backend event-listener [PATH]`
- `flarum-cli make backend handler [PATH]`
- `flarum-cli make backend integration-test [PATH]`
- `flarum-cli make backend job [PATH]`
- `flarum-cli make backend migration [PATH]`
- `flarum-cli make backend model [PATH]`
- `flarum-cli make backend policy [PATH]`
- `flarum-cli make backend repository [PATH]`
- `flarum-cli make backend route [PATH]`
- `flarum-cli make backend service-provider [PATH]`
- `flarum-cli make backend validator [PATH]`

## Frontend Boilerplate Generation: Generate frontend components/classes, ready to be used.
- `flarum-cli make frontend component [PATH]`
- `flarum-cli make frontend modal [PATH]`
- `flarum-cli make frontend model [PATH]`

## Code Updates: These commands help update extensions for newer versions of Flarum.
- `flarum-cli update js-imports [PATH]`: Adds admin/forum/common namespaces to all JS imports from flarum core.
