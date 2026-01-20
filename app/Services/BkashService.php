<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class BkashService
{
    protected $baseUrl;
    protected $appKey;
    protected $appSecret;
    protected $username;
    protected $password;

    public function __construct()
    {
        $sandbox = config('bkash.sandbox', true);

        if ($sandbox) {
            $this->baseUrl = config('bkash.sandbox_base_url');
            $this->appKey = config('bkash.sandbox_app_key');
            $this->appSecret = config('bkash.sandbox_app_secret');
            $this->username = config('bkash.sandbox_username');
            $this->password = config('bkash.sandbox_password');
        } else {
            $this->baseUrl = config('bkash.production_base_url');
            $this->appKey = config('bkash.app_key');
            $this->appSecret = config('bkash.app_secret');
            $this->username = config('bkash.username');
            $this->password = config('bkash.password');
        }
    }

    /**
     * Get grant token from bKash
     */
    public function getToken()
    {
        // Check if token exists in cache
        $cachedToken = Cache::get('bkash_token');
        if ($cachedToken) {
            return $cachedToken;
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'username' => $this->username,
                'password' => $this->password,
            ])->post($this->baseUrl . '/tokenized/checkout/token/grant', [
                'app_key' => $this->appKey,
                'app_secret' => $this->appSecret,
            ]);

            $result = $response->json();

            if (isset($result['id_token'])) {
                // Cache token for 50 minutes (token expires in 1 hour)
                Cache::put('bkash_token', $result['id_token'], 50 * 60);
                return $result['id_token'];
            }

            Log::error('bKash Token Error: ', $result);
            return null;
        } catch (\Exception $e) {
            Log::error('bKash Token Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Refresh token
     */
    public function refreshToken($refreshToken)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'username' => $this->username,
                'password' => $this->password,
            ])->post($this->baseUrl . '/tokenized/checkout/token/refresh', [
                'app_key' => $this->appKey,
                'app_secret' => $this->appSecret,
                'refresh_token' => $refreshToken,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('bKash Refresh Token Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create payment
     */
    public function createPayment($amount, $invoiceNumber, $callbackUrl)
    {
        $token = $this->getToken();

        if (!$token) {
            return [
                'success' => false,
                'message' => 'Failed to get bKash token',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $token,
                'X-APP-Key' => $this->appKey,
            ])->post($this->baseUrl . '/tokenized/checkout/create', [
                'mode' => '0011',
                'payerReference' => $invoiceNumber,
                'callbackURL' => $callbackUrl,
                'amount' => strval($amount),
                'currency' => config('bkash.currency', 'BDT'),
                'intent' => 'sale',
                'merchantInvoiceNumber' => $invoiceNumber,
            ]);

            $result = $response->json();

            if (isset($result['bkashURL'])) {
                return [
                    'success' => true,
                    'paymentID' => $result['paymentID'],
                    'bkashURL' => $result['bkashURL'],
                    'data' => $result,
                ];
            }

            Log::error('bKash Create Payment Error: ', $result);
            return [
                'success' => false,
                'message' => $result['statusMessage'] ?? 'Failed to create payment',
                'data' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('bKash Create Payment Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Payment creation failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Execute payment after callback
     */
    public function executePayment($paymentID)
    {
        $token = $this->getToken();

        if (!$token) {
            return [
                'success' => false,
                'message' => 'Failed to get bKash token',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $token,
                'X-APP-Key' => $this->appKey,
            ])->post($this->baseUrl . '/tokenized/checkout/execute', [
                'paymentID' => $paymentID,
            ]);

            $result = $response->json();

            if (isset($result['transactionStatus']) && $result['transactionStatus'] === 'Completed') {
                return [
                    'success' => true,
                    'transactionId' => $result['trxID'],
                    'data' => $result,
                ];
            }

            Log::error('bKash Execute Payment Error: ', $result);
            return [
                'success' => false,
                'message' => $result['statusMessage'] ?? 'Payment execution failed',
                'data' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('bKash Execute Payment Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Payment execution failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Query payment status
     */
    public function queryPayment($paymentID)
    {
        $token = $this->getToken();

        if (!$token) {
            return [
                'success' => false,
                'message' => 'Failed to get bKash token',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $token,
                'X-APP-Key' => $this->appKey,
            ])->post($this->baseUrl . '/tokenized/checkout/payment/status', [
                'paymentID' => $paymentID,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('bKash Query Payment Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Refund payment
     */
    public function refundPayment($paymentID, $trxID, $amount, $reason = 'Customer request')
    {
        $token = $this->getToken();

        if (!$token) {
            return [
                'success' => false,
                'message' => 'Failed to get bKash token',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $token,
                'X-APP-Key' => $this->appKey,
            ])->post($this->baseUrl . '/tokenized/checkout/payment/refund', [
                'paymentID' => $paymentID,
                'trxID' => $trxID,
                'amount' => strval($amount),
                'reason' => $reason,
                'sku' => 'refund',
            ]);

            $result = $response->json();

            if (isset($result['transactionStatus']) && $result['transactionStatus'] === 'Completed') {
                return [
                    'success' => true,
                    'data' => $result,
                ];
            }

            return [
                'success' => false,
                'message' => $result['statusMessage'] ?? 'Refund failed',
                'data' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('bKash Refund Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Refund failed: ' . $e->getMessage(),
            ];
        }
    }
}
