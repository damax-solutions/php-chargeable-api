# Concepts

API authentication is not part of this library. The identity behind the API call must be resolved (authenticated) by your code.

The main objective is to quickly check available balance for the wallet and act accordingly:

- deny access with _402_ status code,
- or withdraw relevant amount of credit and grant access.

It is up to your code *how* to deposit to relevant user's wallet. See [usage](usage.md) examples for more info.

### Credit

Non-negative integer value. Could be represented as amount of cents on user's balance or whatever suits your design.

### Identity

[Identity](../src/Identity/Identity.php) of authenticated user created by [IdentityFactory](../src/Identity/IdentityFactory.php).
Before charging [Credit](../src/Credit.php) user must be successfully authenticated.

### Product

[Product](../src/Product/Product.php) describes the amount of [Credit](../src/Credit.php) you charge for specific API endpoint.
There must be at least one product defined. Resolved through product [Resolver](../src/Product/Resolver.php) based on incoming request.

### Wallet

Created by [WalletFactory](../src/Wallet/WalletFactory.php) based on provided [Identity](../src/Identity/Identity.php).
You can deposit, withdraw or fetch available [Credit](../src/Credit.php) from the [Wallet](../src/Wallet/Wallet.php).

### Store

[Store](../src/Store/Store.php) charges [Identity](../src/Identity/Identity.php) for the _price_ of resolved [Product](../src/Product/Product.php) returning a purchase [Receipt](../src/Store/Receipt.php).

## Next

Read next how to [configure](configuration.md) paid APIs.
