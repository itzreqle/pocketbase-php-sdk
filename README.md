# PocketBase PHP SDK

This PHP SDK provides an easy way to interact with the PocketBase API, allowing you to manage collections, records, and authentication directly through PHP.

## Features

- Authentication with PocketBase API using tokens
- CRUD operations on records in specified collections
- Dynamic interaction with multiple collections
- Supports token generation using username and password
- Optional filtering and pagination support
- User management (verification, password reset, email change)
- OAuth2 provider management
- Customizable request handling

## Requirements

- PHP 7.4 or higher
- [cURL](https://www.php.net/manual/en/book.curl.php) extension enabled in PHP
- A valid PocketBase instance URL

## Installation

1. Clone this repository or download the source files:
   ```bash
   git clone https://github.com/itzreqle/pocketbase-php-sdk.git
   ```
   
2. Install the required dependencies using Composer:
   ```bash
   composer require vlucas/phpdotenv
   ```

3. Include the `PocketBase.php` and `PocketBaseUtils.php` files in your project.

4. Create a `.env` file in your project root with the following environment variables:
   ```
   POCKETBASE_BASE_URL=https://your-pocketbase-instance.com
   POCKETBASE_COLLECTION=your_collection_name
   POCKETBASE_API_TOKEN=your_api_token
   ```

## Usage

### 1. Initialize the SDK

You can instantiate the `PocketBase` class by providing the PocketBase instance URL, collection name, and token. If these values are not provided, they will be loaded from the `.env` file.

```php
require_once 'PocketBase.php';
require_once 'PocketBaseUtils.php';

$pocketbase = new PocketBase();
$pbUtils = new PocketBaseUtils();
```

### 2. Authentication

```php
// Password authentication
$result = $pbUtils->authWithPassword('user@example.com', 'password123');

// OAuth2 authentication
$result = $pbUtils->authWithOAuth2Flow(['provider' => 'google']);

// Refresh authentication
$result = $pbUtils->authRefresh();
```

### 3. CRUD Operations

```php
// Get all records
$queryParams = ['filter' => 'status=active'];
$response = $pocketbase->getAllRecords($queryParams, 1, 20);
print_r($response);

// Get record by ID
$recordId = 'your-record-id';
$response = $pocketbase->getRecordById($recordId);
print_r($response);

// Create a new record
$data = [
    'name' => 'New Item',
    'description' => 'A description of the item.'
];
$response = $pocketbase->createRecord($data);
print_r($response);

// Update a record
$recordId = 'your-record-id';
$data = [
    'name' => 'Updated Item Name'
];
$response = $pocketbase->updateRecord($recordId, $data);
print_r($response);

// Delete a record
$recordId = 'your-record-id';
$response = $pocketbase->deleteRecord($recordId);
print_r($response);
```

### 4. User Management

```php
// Request email verification
$pbUtils->requestVerification('user@example.com');

// Confirm email verification
$pbUtils->confirmVerification('verification_token');

// Request password reset
$pbUtils->requestPasswordReset('user@example.com');

// Confirm password reset
$pbUtils->confirmPasswordReset('reset_token', 'new_password', 'new_password_confirm');
```

### 5. Generate and Set Token

```php
// Generate a new token
$username = 'your-username';
$password = 'your-password';
$response = $pocketbase->generateToken($username, $password);
print_r($response);

// Manually set a new token
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

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Support

If you encounter any problems or have any questions, please open an issue on the [GitHub repository](https://github.com/itzreqle/pocketbase-php-sdk/issues).

## License

This project is licensed under the MIT License.
