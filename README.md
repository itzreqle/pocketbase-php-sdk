# PocketBase PHP SDK

Welcome to the PocketBase PHP SDK! This SDK simplifies the process of interacting with the PocketBase API, allowing you to manage collections, records, and authentication directly through PHP.

## 🚀 Features

- **Authentication:** Securely connect with the PocketBase API using tokens.
- **CRUD Operations:** Create, read, update, and delete records in specified collections.
- **Dynamic Collections:** Easily interact with multiple collections.
- **Token Generation:** Generate tokens using username and password.
- **Filtering & Pagination:** Optional support for filtering and paginating results.
- **User Management:** Handle user verification, password resets, and email changes.
- **OAuth2 Provider Management:** Integrate with various OAuth2 providers.
- **Customizable Requests:** Tailor your request handling to fit your needs.

## 📋 Requirements

Before you begin, ensure you have the following:

- PHP 7.4 or higher
- [cURL](https://www.php.net/manual/en/book.curl.php) extension enabled in PHP
- A valid PocketBase instance URL

## 🔧 Installation

Follow these steps to install the PocketBase PHP SDK:

1. **Clone the repository or download the source files:**
   ```bash
   git clone https://github.com/itzreqle/pocketbase-php-sdk.git
   ```

2. **Install required dependencies using Composer:**
   ```bash
   composer require vlucas/phpdotenv
   ```

3. **Include the necessary files in your project:**
   ```php
   require_once 'init.php';
   require_once 'auth.php';
   require_once 'utils.php';
   ```

4. **Create a `.env` file in your project root with the following environment variables:**
   ```
   POCKETBASE_BASE_URL=https://your-pocketbase-instance.com
   POCKETBASE_COLLECTION=your_collection_name
   POCKETBASE_API_TOKEN=your_api_token
   ```

## 📚 Usage

### 1. Initialize the SDK

To start using the SDK, instantiate the `PocketBase` class:

```php
$pb = new PocketBase();
$pbAuth = new PocketBaseAuth();
$pbUtils = new PocketBaseUtils();
```

### 2. Authentication

Authenticate users with various methods:

```php
// Password authentication
$result = $pbAuth->authWithPassword('user@example.com', 'password123');

// OAuth2 authentication
$result = $pbAuth->authWithOAuth2Flow(['provider' => 'google']);

// Refresh authentication
$result = $pbAuth->authRefresh();
```

### 3. CRUD Operations

Manage your records with ease:

```php
// Get all records
$queryParams = ['filter' => 'status=active'];
$response = $pb->getAllRecords($queryParams, 1, 20);
print_r($response);

// Get record by ID
$recordId = 'your-record-id';
$response = $pb->getRecordById($recordId);
print_r($response);

// Create a new record
$data = [
    'name' => 'New Item',
    'description' => 'A description of the item.'
];
$response = $pb->createRecord($data);
print_r($response);

// Update a record
$recordId = 'your-record-id';
$data = ['name' => 'Updated Item Name'];
$response = $pb->updateRecord($recordId, $data);
print_r($response);

// Delete a record
$recordId = 'your-record-id';
$response = $pb->deleteRecord($recordId);
print_r($response);
```

### 4. User Management

Manage user accounts seamlessly:

```php
// Create a new user
$response = $pbUtils->createUser('username', 'email@email.com', 'password', 'name', 'description');

// Update a user
$updateResponse = $pbUtils->updateUser('user_id_here', [
    'name' => 'New Name',
    'description' => 'Updated description'
]);

// Request email verification
$pbAuth->requestVerification('user@example.com');

// Confirm email verification
$pbAuth->confirmVerification('verification_token');

// Request password reset
$pbAuth->requestPasswordReset('user@example.com');

// Confirm password reset
$pbAuth->confirmPasswordReset('reset_token', 'new_password', 'new_password_confirm');
```

### 5. Generate and Set Token

Manage authentication tokens easily:

```php
// Generate a new token
$username = 'your-username';
$password = 'your-password';
$response = $pb->generateToken($username, $password);
print_r($response);

// Manually set a new token
$pb->setToken('your-new-token');
```

## ⚠️ Error Handling

All API responses are returned as associative arrays, including the status code and decoded JSON body. For example:

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

In case of errors, the SDK will provide an error message along with the HTTP status code.

## 🤝 Contributing

We welcome contributions! Feel free to submit a Pull Request to enhance the SDK.

## ❓ Support

If you encounter any issues or have questions, please open an issue on the [GitHub repository](https://github.com/itzreqle/pocketbase-php-sdk/issues).

## 📄 License

This project is licensed under the MIT License.
