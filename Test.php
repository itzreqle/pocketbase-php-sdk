<?php

// Include the PocketBase class
require 'PocketBase.php';

// Initialize PocketBase class (token is automatically loaded from the environment)
$pocketBase = new PocketBase(collection: 'users');

// Generate and use the token for further authenticated requests
$pocketBase->generateToken('username', 'password');

// Fetch all records from the default collection
echo "Fetching all records:\n";
$allRecords = $pocketBase->getAllRecords();

print_r($allRecords["response"]["items"]); // Print all the records

// Define your filter for fetching records created after a specific date
$filter = 'created >= "2024-09-01"'; // Adjust the date as needed
$queryParams = ['filter' => $filter];

// Fetch all records with the specified filter
echo "\nFetching filtered records (created after 2024-09-01):\n";
$filteredRecords = $pocketBase->getAllRecords($queryParams);

print_r($filteredRecords["response"]["items"]); // Print filtered records
