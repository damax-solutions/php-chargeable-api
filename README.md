# Damax Chargeable API

[![Build Status](https://travis-ci.org/lakiboy/damax-chargeable-api.svg?branch=master)](https://travis-ci.org/lakiboy/damax-chargeable-api) [![Coverage Status](https://coveralls.io/repos/lakiboy/damax-chargeable-api/badge.svg?branch=master&service=github)](https://coveralls.io/github/lakiboy/damax-chargeable-api?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/lakiboy/damax-chargeable-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/lakiboy/damax-chargeable-api/?branch=master)

Charge credit for API calls. Provides integration with [Symfony Framework](https://github.com/symfony/symfony).

## Features

- Withdraw credit from user's wallet for specific URLs of your API.
- Deny access to the service if user has insufficient funds.
- Specify different prices for various endpoints.
- Use console commands to review user's balance, deposit and withdraw credit.
- Support for various wallets: _Redis_, _MongoDB_, wallet with _fixed_ credit amount or implement your own.
- Subscribe to API purchase events to notify your application and act accordingly.

## Concepts

API authentication is not part of this library. The identity behind the API call must be resolved (authenticated) by your code.

See [example](examples/processor.php).

#### Identity

[Identity](src/Identity/Identity.php) of authenticated user created by [IdentityFactory](src/Identity/IdentityFactory.php).
Before charging credit user must be successfully authenticated.

#### Product

[Product](src/Product/Product.php) describes the amount of credit you charge for specific API endpoint.
There must be at least one product defined. Resolved through product [Resolver](src/Product/Resolver.php) based on incoming request.

#### Wallet

Created by [WalletFactory](src/Wallet/WalletFactory.php) based on provided [Identity](src/Identity/Identity.php).
You can deposit, withdraw or fetch available balance from the [Wallet](src/Wallet/Wallet.php).

#### Store

[Store](src/Store/Store.php) charges [Identity](src/Identity/Identity.php) for the _price_ of resolved [Product](src/Product/Product.php) returning a purchase [Receipt](src/Store/Receipt.php).

## Documentation

Topics:

- [Development](doc/development.md)
