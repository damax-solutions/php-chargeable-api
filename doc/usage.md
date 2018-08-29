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

## Next

If you wish to contribute take a look how to [run the code locally](development.md) in Docker.
