<?php

require_once 'init.php';

class PocketBaseUtils extends PocketBase
{
    /**
     * Create a new user in the specified collection.
     *
     * @param string $username The username of the new user.
     * @param string $email The email of the new user.
     * @param string $password The password for the new user.
     * @param string|null $name Optional name of the user.
     * @param string|null $description Optional description of the user.
     * @param string|null $avatar Optional avatar URL of the user.
     * @return array Response from the API with the created user record.
     */
    public function createUser($username, $email, $password, $name = null, $description = null, $avatar = null)
    {
        $data = [
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'passwordConfirm' => $password, // Confirm password must match
            'name' => $name,
            'avatar' => $avatar,
            'description' => $description
        ];

        // Send request to create a new user record
        return $this->sendRequest('POST', "api/collections/{$this->collection}/records", $data);
    }

    /**
     * Update an existing user in the specified collection.
     *
     * @param string $id The ID of the user to update.
     * @param array $data An associative array of user attributes to update.
     * @return array Response from the API with the updated user record.
     */
    public function updateUser($id, array $data)
    {
        // Ensure that the ID is provided and valid
        if (empty($id)) {
            throw new InvalidArgumentException('User ID must be provided for updating a user.');
        }

        // Send request to update the user record
        return $this->sendRequest('PATCH', "api/collections/{$this->collection}/records/$id", $data);
    }

    /**
     * Delete a user from the specified collection.
     *
     * @param string $id The ID of the user to delete.
     * @return array Response from the API confirming deletion.
     */
    public function deleteUser($id)
    {
        // Ensure that the ID is provided and valid
        if (empty($id)) {
            throw new InvalidArgumentException('User ID must be provided for deleting a user.');
        }

        // Send DELETE request to remove a user record
        return $this->sendRequest('DELETE', "api/collections/{$this->collection}/records/$id");
    }
}
