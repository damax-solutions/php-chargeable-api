# Concepts

API authentication is not part of this library. The identity behind the API call must be resolved (authenticated) by your code.

#### Credit

Non-negative integer value. Could be represented as amount of cents on user's balance or whatever suits your design.

#### Identity

[Identity](src/Identity/Identity.php) of authenticated user created by [IdentityFactory](src/Identity/IdentityFactory.php).
Before charging [Credit](src/Credit.php) user must be successfully authenticated.

#### Product

[Product](src/Product/Product.php) describes the amount of [Credit](src/Credit.php) you charge for specific API endpoint.
There must be at least one product defined. Resolved through product [Resolver](src/Product/Resolver.php) based on incoming request.

#### Wallet

Created by [WalletFactory](src/Wallet/WalletFactory.php) based on provided [Identity](src/Identity/Identity.php).
You can deposit, withdraw or fetch available [Credit](src/Credit.php) from the [Wallet](src/Wallet/Wallet.php).

#### Store

[Store](src/Store/Store.php) charges [Identity](src/Identity/Identity.php) for the _price_ of resolved [Product](src/Product/Product.php) returning a purchase [Receipt](src/Store/Receipt.php).
