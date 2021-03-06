# Damax Chargeable API

[![Build Status](https://travis-ci.org/damax-solutions/php-chargeable-api.svg?branch=master)](https://travis-ci.org/damax-solutions/php-chargeable-api) [![Coverage Status](https://coveralls.io/repos/damax-solutions/php-chargeable-api/badge.svg?branch=master&service=github)](https://coveralls.io/github/damax-solutions/php-chargeable-api?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/damax-solutions/php-chargeable-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/damax-solutions/php-chargeable-api/?branch=master)

Charge credit for API calls. Provides integration with [Symfony Framework](https://github.com/symfony/symfony).

See [example](examples/processor.php).

## Features

- Withdraw credit from user's wallet for specific URLs of your API.
- Deny access to the service if user has insufficient funds.
- Specify different prices for various endpoints.
- Use console commands to review user's balance, deposit and withdraw credit.
- Support for various wallets: _Redis_, _MongoDB_, wallet with _fixed_ credit amount or implement your own.
- Subscribe to API purchase events to notify your application and act accordingly.

## Documentation

Topics:

- [Installation](doc/installation.md)
- [Concepts](doc/concepts.md)
- [Configuration](doc/configuration.md)
- [Usage](doc/usage.md)

## Contribute

Install dependencies and run tests:

```bash
$ make
```
