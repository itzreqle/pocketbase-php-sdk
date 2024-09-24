<?php

require_once 'init.php';

class PocketBaseAuth extends PocketBase
{
    /**
     * Authenticate with username/email and password for users.
     *
     * @param string $identity The username or email of the record to authenticate.
     * @param string $password The auth record password.
     * @param array $queryParams Optional query parameters for filtering.
     * @return array Response from the API with auth token and account data.
     */
    public function authWithPassword($identity, $password, $queryParams = [])
    {
        $data = [
            'identity' => $identity,  // PocketBase uses "identity" as the username/email field
            'password' => $password
        ];

        $endpoint = "api/collections/{$this->collection}/auth-with-password";
        return $this->sendRequest('POST', $endpoint, $data, $queryParams); // Send POST request for authentication
    }

    /**
     * Authenticate with OAuth2 for users.
     *
     * @param array $params OAuth2 parameters including provider, code, codeVerifier, redirectUrl, and optional createData.
     * @param array $queryParams Optional query parameters for filtering.
     * @return array Response from the API with auth token and account data.
     */
    public function authWithOAuth2($params, $queryParams = [])
    {
        // Check for required OAuth2 parameters
        $requiredParams = ['provider', 'code', 'codeVerifier', 'redirectUrl'];
        foreach ($requiredParams as $param) {
            if (!isset($params[$param])) {
                throw new Exception("Missing required OAuth2 parameter: $param");
            }
        }

        $endpoint = "api/collections/{$this->collection}/auth-with-oauth2";
        return $this->sendRequest('POST', $endpoint, $params, $queryParams); // Send POST request for OAuth2 authentication
    }

    /**
     * Helper method to handle the OAuth2 flow with a single call.
     *
     * @param array $params OAuth2 parameters including at least the 'provider'.
     * @return array Response from the API with auth token and account data.
     */
    public function authWithOAuth2Flow($params)
    {
        // Check for required 'provider' parameter
        if (!isset($params['provider'])) {
            throw new Exception("Missing required OAuth2 parameter: provider");
        }

        // Generate a code verifier for PKCE (Proof Key for Code Exchange)
        $codeVerifier = bin2hex(random_bytes(32));

        // Create a code challenge from the verifier
        $codeChallenge = rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');

        // Redirect URL should be set to your actual OAuth2 redirect endpoint
        $redirectUrl = "{$this->baseUrl}/api/oauth2-redirect";

        // NOTE: In a real implementation, you would typically:
        // 1. Redirect the user to the OAuth2 provider's authorization URL
        // 2. Handle the callback, exchanging the received code for tokens
        // 3. Call authWithOAuth2 with the received code and stored code verifier

        // For demonstration purposes, we're simulating receiving a code
        $code = "simulated_oauth2_code";

        // Use the authWithOAuth2 method with the simulated data
        return $this->authWithOAuth2([
            'provider' => $params['provider'],
            'code' => $code,
            'codeVerifier' => $codeVerifier,
            'redirectUrl' => $redirectUrl,
            // Add any additional parameters as needed
        ]);
    }

    /**
     * Auth refresh for users.
     * Returns a new auth response (token and record data) for an already authenticated record.
     * 
     * @param array $queryParams Optional query parameters for filtering.
     * @return array Response from the API with refreshed auth token and account data.
     */
    public function authRefresh($queryParams = [])
    {
        $endpoint = "api/collections/{$this->collection}/auth-refresh";
        return $this->sendRequest('POST', $endpoint, null, $queryParams); // Send POST request for auth refresh
    }

    /**
     * Sends users verification email request.
     * 
     * @param string $email The auth record email address to send the verification request.
     * @return array Response from the API.
     */
    public function requestVerification($email)
    {
        $data = ['email' => $email];
        $endpoint = "api/collections/{$this->collection}/request-verification";
        return $this->sendRequest('POST', $endpoint, $data); // Send POST request for verification
    }

    /**
     * Confirms users account verification request.
     * 
     * @param string $token The token from the verification request email.
     * @return array Response from the API.
     */
    public function confirmVerification($token)
    {
        $data = ['token' => $token];
        $endpoint = "api/collections/{$this->collection}/confirm-verification";
        return $this->sendRequest('POST', $endpoint, $data); // Send POST request to confirm verification
    }

    /**
     * Sends users password reset email request.
     * 
     * @param string $email The auth record email address to send the password reset request.
     * @return array Response from the API.
     */
    public function requestPasswordReset($email)
    {
        $data = ['email' => $email];
        $endpoint = "api/collections/{$this->collection}/request-password-reset";
        return $this->sendRequest('POST', $endpoint, $data); // Send POST request for password reset
    }

    /**
     * Confirms users password reset request and sets a new password.
     * 
     * @param string $token The token from the password reset request email.
     * @param string $password The new password to set.
     * @param string $passwordConfirm The new password confirmation.
     * @return array Response from the API.
     */
    public function confirmPasswordReset($token, $password, $passwordConfirm)
    {
        $data = [
            'token' => $token,
            'password' => $password,
            'passwordConfirm' => $passwordConfirm
        ];
        $endpoint = "api/collections/{$this->collection}/confirm-password-reset";
        return $this->sendRequest('POST', $endpoint, $data); // Send POST request to confirm password reset
    }

    /**
     * Sends users email change request.
     * 
     * @param string $newEmail The new email address to send the change email request.
     * @return array Response from the API.
     */
    public function requestEmailChange($newEmail)
    {
        $data = ['newEmail' => $newEmail];
        $endpoint = "api/collections/{$this->collection}/request-email-change";
        return $this->sendRequest('POST', $endpoint, $data); // Send POST request for email change
    }

    /**
     * Confirms users email change request.
     * 
     * @param string $token The token from the change email request email.
     * @param string $password The account password to confirm the email change.
     * @return array Response from the API.
     */
    public function confirmEmailChange($token, $password)
    {
        $data = [
            'token' => $token,
            'password' => $password
        ];
        $endpoint = "api/collections/{$this->collection}/confirm-email-change";
        return $this->sendRequest('POST', $endpoint, $data); // Send POST request to confirm email change
    }

    /**
     * Returns a public list with all allowed users authentication methods.
     * 
     * @param array $queryParams Optional query parameters for filtering.
     * @return array Response from the API with auth methods.
     */
    public function listAuthMethods($queryParams = [])
    {
        $endpoint = "api/collections/{$this->collection}/auth-methods";
        return $this->sendRequest('GET', $endpoint, null, $queryParams); // Send GET request for auth methods
    }

    /**
     * Returns a list with all OAuth2 providers linked to a single user.
     * 
     * @param string $userId ID of the auth record.
     * @param array $queryParams Optional query parameters for filtering.
     * @return array Response from the API with linked OAuth2 providers.
     */
    public function listExternalAuths($userId, $queryParams = [])
    {
        $endpoint = "api/collections/{$this->collection}/records/$userId/external-auths";
        return $this->sendRequest('GET', $endpoint, null, $queryParams); // Send GET request for external auths
    }

    /**
     * Unlink a single external OAuth2 provider from a user record.
     * 
     * @param string $userId ID of the auth record.
     * @param string $provider The name of the auth provider to unlink.
     * @return array Response from the API.
     */
    public function unlinkExternalAuth($userId, $provider)
    {
        $endpoint = "api/collections/{$this->collection}/records/$userId/external-auths/$provider";
        return $this->sendRequest('DELETE', $endpoint); // Send DELETE request to unlink external auth
    }
}
