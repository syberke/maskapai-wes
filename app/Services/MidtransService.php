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
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized = (bool) config('midtrans.is_sanitized');
        Config::$is3ds = (bool) config('midtrans.is_3ds');
        Config::$clientKey = config('midtrans.client_key');
    }

    public function createSnapToken(array $payload): string
    {
        try {
            return Snap::getSnapToken($payload);
        } catch (\Throwable $exception) {
            Log::error('Midtrans Snap Token Error', ['message' => $exception->getMessage()]);
            throw new \RuntimeException('Failed to create payment token. Please try again.', previous: $exception);
        }
    }

    public function verifyCallbackSignature(array $data): bool
    {
        $required = ['order_id', 'status_code', 'gross_amount', 'signature_key'];

        foreach ($required as $field) {
            if (! isset($data[$field]) || ! is_scalar($data[$field])) {
                return false;
            }
        }

        $serverKey = (string) config('midtrans.server_key');

        if ($serverKey === '') {
            return false;
        }

        $expected = hash(
            'sha512',
            (string) $data['order_id']
            . (string) $data['status_code']
            . (string) $data['gross_amount']
            . $serverKey
        );

        return hash_equals($expected, (string) $data['signature_key']);
    }

    public function getTransactionStatus(string $orderId): array
    {
        $baseUrl = config('midtrans.is_production')
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';

        try {
            $response = Http::withBasicAuth((string) config('midtrans.server_key'), '')
                ->acceptJson()
                ->timeout(15)
                ->retry(2, 300)
                ->get($baseUrl . '/v2/' . rawurlencode($orderId) . '/status');

            if ($response->failed()) {
                Log::warning('Midtrans Status Check Failed', [
                    'order_id' => $orderId,
                    'http_status' => $response->status(),
                    'response' => $response->json(),
                ]);

                return [];
            }

            return $response->json() ?? [];
        } catch (\Throwable $exception) {
            Log::error('Midtrans Status Check Error', [
                'order_id' => $orderId,
                'message' => $exception->getMessage(),
            ]);

            return [];
        }
    }
}
