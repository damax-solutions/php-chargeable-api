# Installation

This page explains how to install and configure _Chargeable API_ with _Symfony_.

## Composer

Install packages with composer:

```bash
$ composer require damax/chargeable-api # symfony/security-bundle - Make sure security component is installed.
```

You need to choose appropriate wallet implementation:

```bash
$ # For Redis
$ composer require predis/predis snc/redis-bundle

$ # For MongoDB
$ composer require mongodb/mongodb
```  

## Bundles

With introduction of _symfony/flex_ you don't have to worry about enabling relevant bundles, but make sure below is present in your configuration.

```php
// Symfony v4.0 example, but v3.x is also supported.
Damax\ChargeableApi\Bridge\Symfony\Bundle\DamaxChargeableApiBundle::class => ['all' => true],

// For Redis
Snc\RedisBundle\SncRedisBundle::class => ['all' => true],
```

## Configuration

By default all routes are processed for payment. You can configure endpoints for paid APIs with below:

```yaml
damax_chargeable_api:
    listener:
        matcher: ^/api/services/ # Regex
```

As all paid endpoints require authentication make sure it is covered by _Security_ firewall.

## Next

Read next about library [concepts](concepts.md) or skip right to [configuration](configuration.md).
