# fapi-client

## Installation

The recommended way to install is via Composer:

```
composer require smartselling/fapi-client
```

## Example of use:

```
$fapi = new FAPIClient('username', 'API token');
$fapi->invoice->getAll(); // returns all invoices
$fapi->invoice->get(123); // returns invoice #123
$fapi->invoice->search(['client' => 123]); // returns invoices of client #123
$fapi->client->searchOne(['email' => 'johndoe@example.com']); // returns client by email
```

## Documentation
You can find API documentation [here](https://web.fapi.cz/api-doc/index.html]).
