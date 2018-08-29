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
