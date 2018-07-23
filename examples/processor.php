<?php

declare(strict_types=1);

use Damax\ChargeableApi\Bridge\Symfony\EventDispatcher\Event\PurchaseFinished;
use Damax\ChargeableApi\Bridge\Symfony\EventDispatcher\Events;
use Damax\ChargeableApi\Bridge\Symfony\EventDispatcher\NotificationStore;
use Damax\ChargeableApi\Identity\FixedIdentityFactory;
use Damax\ChargeableApi\Product\ChainResolver;
use Damax\ChargeableApi\Product\FixedProductResolver;
use Damax\ChargeableApi\Store\StoreProcessor;
use Damax\ChargeableApi\Store\WalletStore;
use Damax\ChargeableApi\Wallet\InMemoryWalletFactory;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/../vendor/autoload.php';

// Creates fixed identity.
$identityFactory = new FixedIdentityFactory('john.doe');

// Resolves to single product.
$productResolver = new ChainResolver([new FixedProductResolver('Services', 10)]);

// Defines amount of credits in wallet for identities.
$walletFactory = new InMemoryWalletFactory(['john.doe' => 100, 'jane.doe' => 150]);

// Notifies about purchases.
$store = new NotificationStore(new WalletStore($walletFactory), $dispatcher = new EventDispatcher());

// Creates processor.
$processor = new StoreProcessor($store, $identityFactory, $productResolver);

$dispatcher->addListener(Events::PURCHASE_FINISHED, function (PurchaseFinished $event): void {
    $product = $event->product();

    // User 'john.doe' paid 10 credits for 'Services' product.
    echo sprintf("User '%s' paid %d credits for '%s' product.\n", $event->identity(), $product->price()->toInteger(), $product->name());
});

// Run.
$processor->processRequest(Request::createFromGlobals());
