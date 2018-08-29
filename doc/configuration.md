# Configuration

## Listener

As stated in [installation](installation.md) document the first thing you need to configure is payment listener:

```yaml
damax_chargeable_api:
    listener:
        matcher: ^/api/services/
```

Charge payment for requests covered by above regex. You can omit this setting if all routes require payment.

It is possible to be more specific and process only certain _HTTP_ verbs or IP addresses:

```yaml
damax_chargeable_api:
    listener:
        matcher:
            path: ^/api/services/
            methods: [GET, POST]
            ips: [192.168.2.100, 192.168.2.105]
```

## Products

By default a product named _API_ is registered with price of 1 (one) credit.

A price of default product is configurable:

```yaml
damax_chargeable_api:
    product: 5
```

Or you can change the name of default product:

```yaml
damax_chargeable_api:
    product: Scoring
```

Register multiple products with different price points:

```yaml
damax_chargeable_api:
    product:
        - { name: Scoring Fast, price: 3, matcher: ^/api/scoring/fast }
        - { name: Scoring Full, price: 8, matcher: ^/api/scoring/full }
        - { name: API services, price: 1 } # remaining paid request
```

A `matcher` key can be configured in same way as in `listener` configuration.

## Identity

In most simplest form you can resolve to same identity for all requests:

```yaml
damax_chargeable_api:
    identity: john.doe@domain.abc
```

Above configuration is useful for testing purposes or when you have a single client.

Use authenticated user from security component:

```yaml
damax_chargeable_api:
    identity:
        type: security
```

This is also the default configuration.

You can register custom service by implementing [IdentityFactory](../src/Identity/IdentityFactory.php):

```yaml
damax_chargeable_api:
    identity:
        type: service
        factory_service_id: app.chargeable_api.identity_factory
```

## Wallet

For testing purposes `fixed` wallet is the best choice. This is in memory representation of users' balances:

```yaml
damax_chargeable_api:
    wallet:
        'john.doe@domain.abc': 10
        'jane.doe@domain.abc': 50
```

In other words, there is fixed amount of credits on user's balance for each payment request.

For _Redis_:

```yaml
damax_chargeable_api:
    wallet:
        type: redis
        wallet_key: wallet
        redis_client_id: snc_redis.default # service implementing Predis\ClientInterface
```

For _Mongo_:

```yaml
damax_chargeable_api:
    wallet:
        type: mongo
        mongo_client_id: MongoDB\Client
        db_name: project
        collection_name: user
```

Register custom service by implementing [WalletFactory](../src/Wallet/WalletFactory.php):

```yaml
damax_chargeable_api:
    wallet:
        type: service
        factory_service_id: app.chargeable_api.wallet_factory
```

## Next

Read next on [usage examples](usage.md).
