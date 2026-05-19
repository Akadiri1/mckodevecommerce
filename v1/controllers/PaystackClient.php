<?php
/**
 * Paystack REST Client (No SDK)
 * Provides Initialize and Verify transaction methods via cURL with exponential backoff.
 */
class PaystackClient {

    private const BASE_URL = 'https://api.paystack.co';
    private string $secretKey;
    private int $maxRetries = 3;

    public function __construct(string $secretKey) {
        if (empty($secretKey)) {
            throw new InvalidArgumentException("Paystack Secret Key cannot be empty.");
        }
        $this->secretKey = $secretKey;
    }

    private function _request($endpoint, $method = 'GET', $body = []) {
        $url = self::BASE_URL . $endpoint;

        for ($attempt = 0; $attempt < $this->maxRetries; $attempt++) {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);

            $headers = [
                'Authorization: Bearer ' . $this->secretKey,
                'Content-Type: application/json',
            ];

            if ($method === 'POST') {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
            }

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response  = curl_exec($ch);
            $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                error_log("Paystack cURL Error (Attempt " . ($attempt + 1) . "): " . $curlError);
            } else {
                $data = json_decode($response, true);

                if ($httpCode === 200 && isset($data['status']) && $data['status'] === true) {
                    return $data;
                }

                $message = $data['message'] ?? 'Unknown Paystack API Error';
                error_log("Paystack API Error (Attempt " . ($attempt + 1) . ", HTTP $httpCode): " . $message);
            }

            if ($attempt === $this->maxRetries - 1) {
                throw new Exception(
                    "Paystack API call failed after {$this->maxRetries} attempts. Last message: " . ($message ?? $curlError)
                );
            }

            sleep(pow(2, $attempt)); // Exponential backoff: 1s, 2s, 4s
        }

        throw new Exception("Paystack API call failed unexpectedly.");
    }

    /**
     * Initialize a new transaction.
     * @param int    $amount      Amount in smallest unit (kobo for NGN).
     * @param string $email       Customer email.
     * @param string $reference   Optional unique reference.
     * @param string $callbackUrl Optional redirect URL after payment.
     * @param array  $metadata    Optional metadata.
     */
    public function initializeTransaction($amount, $email, $reference = null, $callbackUrl = null, $metadata = []) {
        if ($amount <= 0) {
            throw new InvalidArgumentException("Amount must be greater than zero.");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email address provided.");
        }

        $body = [
            'amount'   => $amount,
            'email'    => $email,
            'currency' => 'NGN',
            'metadata' => $metadata,
        ];

        if ($reference)   $body['reference']    = $reference;
        if ($callbackUrl) $body['callback_url']  = $callbackUrl;

        return $this->_request('/transaction/initialize', 'POST', $body);
    }

    /**
     * Verify a transaction by reference.
     */
    public function verifyTransaction($reference) {
        if (empty($reference)) {
            throw new InvalidArgumentException("Transaction reference cannot be empty.");
        }
        return $this->_request("/transaction/verify/{$reference}", 'GET');
    }

    /**
     * Process and validate an incoming Paystack webhook.
     * Performs HMAC-SHA512 signature verification.
     */
    public function processWebhook(): array {
        $input = @file_get_contents("php://input");
        if (!$input) {
            throw new Exception("No raw request body received.");
        }

        $signature          = $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ?? '';
        $expected_signature = hash_hmac('sha512', $input, $this->secretKey);

        if (empty($signature) || !hash_equals($expected_signature, $signature)) {
            error_log("Paystack Webhook Security Alert: Invalid signature received.");
            throw new Exception("Signature verification failed.");
        }

        $event = json_decode($input, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON payload received.");
        }

        return $event;
    }
}
