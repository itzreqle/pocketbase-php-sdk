<?php

require 'vendor/autoload.php'; 

class PocketBase
{
    private $baseUrl;      // Base URL for the PocketBase instance
    private $collection;   // Current collection being interacted with
    private $token;        // API token for authentication

    /**
     * Constructor to initialize the PocketBase instance.
     *
     * @param string|null $baseUrl The base URL of the PocketBase instance. If not provided, loaded from the environment.
     * @param string|null $collection The collection to work with. If not provided, loaded from the environment.
     * @param string|null $token Optional token. If not provided, loaded from the environment.
     */
    public function __construct($baseUrl = null, $collection = null, $token = null)
    {
        // Load .env file to access environment variables
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();

        // Load base URL, collection, and token from environment variables if not provided
        $this->baseUrl = $baseUrl ?: rtrim($_ENV['POCKETBASE_BASE_URL'], '/');
        $this->collection = $collection ?: $_ENV['POCKETBASE_COLLECTION'];
        $this->token = $token ?: $_ENV['POCKETBASE_API_TOKEN'] ?? getenv('POCKETBASE_API_TOKEN');

        // Inform the developer if the environment variables are not set
        if (!$this->baseUrl || !$this->collection) {
            die("Base URL or Collection not found! Please ensure POCKETBASE_BASE_URL and POCKETBASE_COLLECTION are set in your .env file.\n");
        }

        // Check if token is available and provide a clear message to the user
        if (!$this->token) {
            die("Token not found! Please ensure POCKETBASE_API_TOKEN is set in your .env file.\n");
        }
    }

    /**
     * Function to send cURL requests with token.
     *
     * @param string $method The HTTP method (GET, POST, PATCH, DELETE).
     * @param string $endpoint The API endpoint to send the request to.
     * @param array|null $data The data to send with the request (for POST and PATCH).
     * @param array $queryParams Optional query parameters for the request.
     * @return array The response status code and the decoded JSON response.
     */
    private function sendRequest($method, $endpoint, $data = null, $queryParams = [])
    {
        // Construct the full URL for the request
        $url = "{$this->baseUrl}/api/collections/{$this->collection}/$endpoint";

        // Append query parameters if any are provided
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        $curl = curl_init($url);  // Initialize cURL session

        // Set headers for the request
        $headers = ['Content-Type: application/json']; // Set content type to JSON
        if ($this->token) {
            $headers[] = 'Authorization: Bearer ' . $this->token; // Add token to headers if available
        }

        // Set cURL options
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method); // Set HTTP method
        if ($data) {
            $jsonData = json_encode($data); // Encode data as JSON
            curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData); // Set request body for POST/PATCH
            $headers[] = 'Content-Length: ' . strlen($jsonData); // Set content length
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Return response as a string
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); // Set the request headers

        // Execute the cURL request and fetch the response
        $response = curl_exec($curl);

        // Check if cURL executed successfully
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl); // Get cURL error message
            curl_close($curl);
            return ['statusCode' => 500, 'response' => ['error' => "cURL error: $error_msg"]]; // Handle cURL errors gracefully
        }

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE); // Get HTTP response code
        curl_close($curl); // Close cURL session

        // Return the response status code and the decoded JSON
        return ['statusCode' => $httpCode, 'response' => json_decode($response, true)];
    }

    /**
     * Set a new token for authentication.
     *
     * @param string $token The new token to set.
     */
    public function setToken($token)
    {
        $this->token = $token; // Update the token for future requests
    }

    /**
     * Generate and print a token for authentication using username and password.
     *
     * @param string $username The username (identity) for authentication.
     * @param string $password The password for authentication.
     * @return array The generated token and user data, or an error message.
     */
    public function generateToken($username, $password)
    {
        // Corrected endpoint for admin authentication (not tied to a collection)
        $endpoint = "{$this->baseUrl}/api/admins/auth-with-password";

        // Data to be sent in the request
        $data = [
            'identity' => $username,  // PocketBase uses "identity" as the username field (this could be an email or username)
            'password' => $password
        ];

        // Initialize cURL directly for this request, no collection
        $curl = curl_init($endpoint);  // Initialize cURL session for the correct endpoint

        // Set headers for the request
        $headers = ['Content-Type: application/json']; // Set content type to JSON

        // Set cURL options
        curl_setopt($curl, CURLOPT_POST, true); // Set HTTP method to POST
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data)); // Set request body
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Return response as a string
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); // Set the request headers

        // Execute the cURL request and fetch the response
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE); // Get HTTP response code

        curl_close($curl); // Close cURL session

        // Decode the JSON response
        $decodedResponse = json_decode($response, true);

        // Check if the request was successful (HTTP 200 status)
        if ($httpCode === 200 && isset($decodedResponse['token'])) {
            $token = $decodedResponse['token']; // Extract the token from the response
            echo "Generated Token: " . $token . "\n"; // Print the token

            // Optionally, store the token in the class for future use
            $this->setToken($token);

            return $decodedResponse; // Return the full response (token and user data)
        } else {
            // Handle error response
            echo "Error generating token: " . json_encode($decodedResponse) . "\n";
            return $decodedResponse; // Return error response if token generation failed
        }
    }

    /**
     * Get all records from the current collection with optional filtering and pagination.
     *
     * @param array $queryParams Optional query parameters for filtering.
     * @param int $page The page number to fetch (default is 1).
     * @param int $perPage Number of records per page (default is 20).
     * @return array Response from the API with records.
     */
    public function getAllRecords($queryParams = [], $page = 1, $perPage = 20)
    {
        // Add pagination parameters to the query
        $queryParams['page'] = $page;
        $queryParams['perPage'] = $perPage;

        return $this->sendRequest('GET', 'records', null, $queryParams); // Send GET request for all records
    }

    /**
     * Get a record by its ID from the current collection with optional filtering.
     *
     * @param string $id The ID of the record to fetch.
     * @param array $queryParams Optional query parameters for filtering.
     * @return array Response from the API with the record.
     */
    public function getRecordById($id, $queryParams = [])
    {
        return $this->sendRequest('GET', "records/$id", null, $queryParams); // Send GET request for a specific record
    }

    /**
     * Create a new record in the current collection.
     *
     * @param array $data The data for the new record.
     * @return array Response from the API with the created record.
     */
    public function createRecord($data)
    {
        return $this->sendRequest('POST', 'records', $data); // Send POST request to create a record
    }

    /**
     * Update an existing record in the current collection.
     *
     * @param string $id The ID of the record to update.
     * @param array $data The updated data for the record.
     * @return array Response from the API with the updated record.
     */
    public function updateRecord($id, $data)
    {
        return $this->sendRequest('PATCH', "records/$id", $data); // Send PATCH request to update a record
    }

    /**
     * Delete a record from the current collection.
     *
     * @param string $id The ID of the record to delete.
     * @return array Response from the API confirming deletion.
     */
    public function deleteRecord($id)
    {
        return $this->sendRequest('DELETE', "records/$id"); // Send DELETE request to remove a record
    }
}
