# Usage

Read various tips and tricks about using this library.

## Console

Use _CLI_ commands to test library's setup. Quickly deposit or withdraw credit from an account.

Retrieve balance of identity:

```bash
$ ./bin/console damax:chargeable-api:wallet:balance john.doe@domain.abc
```

Deposit credit:

```bash
$ ./bin/console damax:chargeable-api:wallet:deposit john.doe@domain.abc 500
```

Withdraw credit:

```bash
$ ./bin/console damax:chargeable-api:wallet:withdraw john.doe@domain.abc 10
```

## Custom product resolver

Let's say your API can return _json_ response or generate a _pdf_ document, but at bigger cost e.g. 10 credits:

```php
namespace App\Report;

use Damax\ChargeableApi\Product\Product;
use Damax\ChargeableApi\Product\ProductResolutionFailed;
use Damax\ChargeableApi\Product\Resolver;

final class PdfReportResolver implements Resolver
{
    public function resolve($request): Product
    {
        if ('application/pdf' === $request->headers->get('accept')) {
            return new Product('PDF report', 10);
        }

        throw ProductResolutionFailed::unresolved();
    }
}
```

Register product resolver in container:

```xml
<service id="App\Report\PdfReportResolver">
    <tag name="damax.chargeable_api.product_resolver" priority="4" />
</service>
```

Use the priority attribute when registering multiple resolvers.

## Communication with other systems

API application may need to notify other systems about user's balance change. Consider below example:

```php
namespace App\Payments\Listener;

use Damax\ChargeableApi\Bridge\Symfony\EventDispatcher\Event\PurchaseFinished;
use Damax\ChargeableApi\Bridge\Symfony\EventDispatcher\Events as ChargeEvents;
use Interop\Queue\PsrContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PurchaseListener implements EventSubscriberInterface
{
    private $context;
    private $queueName;

    public function __construct(PsrContext $context, string $queueName)
    {
        $this->context = $context;
        $this->queueName = $queueName;
    }

    public static function getSubscribedEvents(): array
    {
        return [ChargeEvents::PURCHASE_FINISHED => 'onPurchaseFinished'];
    }

    public function onPurchaseFinished(PurchaseFinished $event): void
    {
        $data = [
            'user_id' => (string) $event->identity(),
            'product' => $event->product()->name(),
            'amount' => $event->receipt()->price()->toInteger(),
        ];

        $this->context->createProducer()->send(
            $this->context->createQueue($this->queueName),
            $this->context->createMessage(serialize($data))
        );
    }
}
```

Register listener in container:

```xml
<service id="App\Payments\Listener\PurchaseListener">
    <argument type="service" id="enqueue.transport.context" />
    <argument>purchase_events</argument>
    <tag name="kernel.event_subscriber" />
</service>
```

## Next

If you wish to contribute take a look how to [run the code locally](development.md) in Docker.
