<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class KafkaService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('kafka.host');
    }

    public function publish(string $topic, array $message): bool
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/vnd.kafka.json.v2+json',
            'Accept' => 'application/vnd.kafka.v2+json',
        ])->post("{$this->baseUrl}/topics/{$topic}", [
            'records' => [
                ['value' => $message],
            ],
        ]);

        return $response->successful();
    }
}
