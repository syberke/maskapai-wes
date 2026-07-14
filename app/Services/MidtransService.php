<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        $this->configureMidtrans();
    }

    private function configureMidtrans(): void
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
        Config::$clientKey = config('midtrans.client_key');
    }

    public function createSnapToken(array $payload): string
    {
        try {
            return Snap::getSnapToken($payload);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Token Error: ' . $e->getMessage());
            throw new \RuntimeException('Failed to create payment token. Please try again.');
        }
    }

    public function verifyCallbackSignature(array $data): bool
    {
        $serverKey = config('midtrans.server_key');
        $signatureKey = hash('sha512', $data['order_id'] . $data['status_code'] . $data['gross_amount'] . $serverKey);

        return $signatureKey === $data['signature_key'];
    }

    public function getTransactionStatus(string $orderId): array
    {
        try {
            $response = Http::withBasicAuth(config('midtrans.server_key'), '')
                ->get("https://api.midtrans.com/v2/{$orderId}/status");

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Midtrans Status Check Error: ' . $e->getMessage());
            return [];
        }
    }
}