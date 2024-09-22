# PocketBase PHP SDK

This PHP SDK provides an easy way to interact with the PocketBase API, allowing you to manage collections, records, and authentication directly through PHP.

## Features
- Authentication with PocketBase API using tokens
- CRUD operations on records in specified collections
- Dynamic interaction with multiple collections
- Supports token generation using username and password
- Optional filtering and pagination support

## Requirements
- PHP 7.4 or higher
- Composer for autoloading dependencies
- [cURL](https://www.php.net/manual/en/book.curl.php) extension enabled in PHP
- A valid PocketBase instance URL

## Installation

1. Install the required dependencies using Composer:

```bash
composer require vlucas/phpdotenv
```

2. Place the `PocketBase.php` file into your project directory.

3. Create a `.env` file in your project root with the following environment variables:

```
POCKETBASE_BASE_URL=https://your-pocketbase-instance.com
POCKETBASE_COLLECTION=your_collection_name
POCKETBASE_API_TOKEN=your_api_token
```

## Usage

### 1. Initialize the SDK

You can instantiate the `PocketBase` class by providing the PocketBase instance URL, collection name, and token. If these values are not provided, they will be loaded from the `.env` file.

```php
require 'PocketBase.php';

$pocketbase = new PocketBase();
```

### 2. Get All Records

Retrieve all records from the current collection. You can also filter results and paginate them.

```php
$queryParams = ['filter' => 'status=active'];
$response = $pocketbase->getAllRecords($queryParams, 1, 20);

print_r($response);
```

### 3. Get Record by ID

Retrieve a specific record by its ID from the collection.

```php
$recordId = 'your-record-id';
$response = $pocketbase->getRecordById($recordId);

print_r($response);
```

### 4. Create a New Record

Add a new record to the collection by passing the data array.

```php
$data = [
    'name' => 'New Item',
    'description' => 'A description of the item.'
];

$response = $pocketbase->createRecord($data);

print_r($response);
```

### 5. Update a Record

Update an existing record by its ID with the new data.

```php
$recordId = 'your-record-id';
$data = [
    'name' => 'Updated Item Name'
];

$response = $pocketbase->updateRecord($recordId, $data);

print_r($response);
```

### 6. Delete a Record

Delete a record from the collection by its ID.

```php
$recordId = 'your-record-id';
$response = $pocketbase->deleteRecord($recordId);

print_r($response);
```

### 7. Generate a Token

Generate a new token using username and password authentication.

```php
$username = 'your-username';
$password = 'your-password';
$response = $pocketbase->generateToken($username, $password);

print_r($response);
```

### 8. Set a New Token

Manually set a new token for the SDK, for example, after generating a new token.

```php
$pocketbase->setToken('your-new-token');
```

## Error Handling

All responses from the API are returned as associative arrays, with the status code and the decoded JSON body.

Example response:

```php
[
    'statusCode' => 200,
    'response' => [
        'id' => 'abc123',
        'name' => 'Item Name',
        'created' => '2023-01-01T12:00:00Z'
    ]
]
```

In case of errors, the SDK will return an error message and the HTTP status code.

## License

This project is licensed under the MIT License.
