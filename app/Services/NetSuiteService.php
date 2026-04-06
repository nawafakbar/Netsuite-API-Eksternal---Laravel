<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class NetSuiteService
{
    private $accountId;
    private $consumerKey;
    private $consumerSecret;
    private $tokenId;
    private $tokenSecret;
    private $baseUrl;
    private $cleanUrl;

    public function __construct()
    {
        $this->accountId      = env('NETSUITE_ACCOUNT_ID');
        $this->consumerKey    = env('NETSUITE_CONSUMER_KEY');
        $this->consumerSecret = env('NETSUITE_CONSUMER_SECRET');
        $this->tokenId        = env('NETSUITE_TOKEN_ID');
        $this->tokenSecret    = env('NETSUITE_TOKEN_SECRET');

        // URL bersih tanpa query params - dipakai untuk signature
        $this->cleanUrl = "https://{$this->accountId}.restlets.api.netsuite.com/app/site/hosting/restlet.nl";

        // URL lengkap dengan script & deploy
        $this->baseUrl  = $this->cleanUrl . "?script=" . env('NETSUITE_SCRIPT_ID') . "&deploy=" . env('NETSUITE_DEPLOY_ID');
    }

    private function generateAuthHeader($method, $extraParams = [])
    {
        $nonce     = bin2hex(random_bytes(16));
        $timestamp = time();

        $oauthParams = [
            'oauth_consumer_key'     => $this->consumerKey,
            'oauth_nonce'            => $nonce,
            'oauth_signature_method' => 'HMAC-SHA256',
            'oauth_timestamp'        => (string) $timestamp,
            'oauth_token'            => $this->tokenId,
            'oauth_version'          => '1.0',
        ];

        // Base params (script & deploy selalu ada)
        $baseParams = [
            'script' => env('NETSUITE_SCRIPT_ID'),
            'deploy' => env('NETSUITE_DEPLOY_ID'),
        ];

        // Gabung semua params untuk signature
        $allParams = array_merge($oauthParams, $baseParams, $extraParams);
        ksort($allParams);

        // Build signature base string
        $paramString = http_build_query($allParams, '', '&', PHP_QUERY_RFC3986);
        $baseString  = strtoupper($method) . '&'
                     . rawurlencode($this->cleanUrl) . '&'
                     . rawurlencode($paramString);

        // Signing key
        $signingKey = rawurlencode($this->consumerSecret) . '&' . rawurlencode($this->tokenSecret);
        $signature  = base64_encode(hash_hmac('sha256', $baseString, $signingKey, true));

        $oauthParams['oauth_signature'] = $signature;
        $oauthParams['realm']           = strtoupper($this->accountId);

        // Build header
        $headerParts = [];
        foreach ($oauthParams as $key => $value) {
            $headerParts[] = $key . '="' . rawurlencode($value) . '"';
        }

        return 'OAuth ' . implode(', ', $headerParts);
    }

    // ========== GET ==========
    public function get($type, $id = null)
    {
        $extraParams = ['type' => $type];
        if ($id) $extraParams['id'] = $id;

        $authHeader    = $this->generateAuthHeader('GET', $extraParams);
        $urlWithParams = $this->baseUrl . '&' . http_build_query($extraParams);

        $response = Http::withHeaders([
            'Authorization' => $authHeader,
            'Content-Type'  => 'application/json',
        ])->get($urlWithParams);

        return $response->json();
    }

    // ========== POST ==========
    public function post($type, $data)
    {
        $authHeader   = $this->generateAuthHeader('POST');
        $data['type'] = $type;

        $response = Http::withHeaders([
            'Authorization' => $authHeader,
            'Content-Type'  => 'application/json',
        ])->post($this->baseUrl, $data);

        return $response->json();
    }

    // ========== PUT ==========
    public function put($type, $id, $data)
    {
        $authHeader   = $this->generateAuthHeader('PUT');
        $data['type'] = $type;
        $data['id']   = $id;

        $response = Http::withHeaders([
            'Authorization' => $authHeader,
            'Content-Type'  => 'application/json',
        ])->put($this->baseUrl, $data);

        return $response->json();
    }

    // ========== DELETE ==========
    public function delete($type, $id)
    {
        $extraParams   = ['type' => $type, 'id' => $id];
        $authHeader    = $this->generateAuthHeader('DELETE', $extraParams);
        $urlWithParams = $this->baseUrl . '&' . http_build_query($extraParams);

        $response = Http::withHeaders([
            'Authorization' => $authHeader,
            'Content-Type'  => 'application/json',
        ])->delete($urlWithParams);

        return $response->json();
    }
}