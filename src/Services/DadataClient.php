<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class DadataClient
{
    private Client $client;

    public function __construct(string $baseUrl, string $token, int $timeout = 10)
    {
        $headers = [
            "Content-Type" => "application/json",
            "Accept" => "application/json",
            "Authorization" => "Token " . $token,
        ];
        $this->client = new Client([
            "base_uri" => $baseUrl,
            "headers" => $headers,
            "timeout" => $timeout
        ]);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function suggest($name, $query, $count = 5, $kwargs = [])
    {
        $url = "suggest/$name";
        $data = ["query" => $query, "count" => $count];
        $data += $kwargs;
        $response = $this->client->post($url, [
            "json" => $data
        ]);

        $response_data = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return $response_data["suggestions"];
    }
}
