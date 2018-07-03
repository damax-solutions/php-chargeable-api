# Development

Build image:

```bash
$ docker build -t damax-chargeable-api .
```

Install dependencies:

```bash
$ docker run --rm -v $(pwd):/app -w /app damax-chargeable-api composer install
```

Fix php coding standards:

```bash
$ docker run --rm -v $(pwd):/app -w /app damax-chargeable-api composer cs
```

Running tests:

```bash
$ docker run --rm -v $(pwd):/app -w /app damax-chargeable-api composer test
$ docker run --rm -v $(pwd):/app -w /app damax-chargeable-api composer test-cc
```
