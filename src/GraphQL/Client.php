<?php

declare(strict_types=1);

namespace MonarchMoney\GraphQL;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use MonarchMoney\Exception\AuthenticationException;
use MonarchMoney\Exception\RequestFailedException;

class Client
{
    private const BASE_URL = 'https://api.monarch.com';
    private const GRAPHQL_ENDPOINT = '/graphql';

    private HttpClient $httpClient;
    private string $token;
    private int $timeout;

    public function __construct(string $token, int $timeout = 10)
    {
        $this->token = $token;
        $this->timeout = $timeout;
        $this->httpClient = new HttpClient([
            'base_uri' => self::BASE_URL,
            'timeout' => $this->timeout,
        ]);
    }

    /**
     * Execute a GraphQL query or mutation.
     *
     * @param string $operationName The operation name
     * @param string $query The GraphQL query string
     * @param array<string, mixed> $variables The variables for the query
     * @return array<string, mixed> The response data
     * @throws AuthenticationException
     * @throws RequestFailedException
     */
    public function query(string $operationName, string $query, array $variables = [], bool $verbose = true): array
    {
        $payload = [
            'operationName' => $operationName,
            'query' => $query,
            'variables' => (object) $variables,
	];

        if ($verbose) {
            echo json_encode($payload);
        }

        try {
            $response = $this->httpClient->post(self::GRAPHQL_ENDPOINT, [
                'headers' => $this->getHeaders(),
                'json' => $payload,
            ]);

            $body = json_decode((string) $response->getBody(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new RequestFailedException('Invalid JSON response from API');
            }

            if (isset($body['errors']) && !empty($body['errors'])) {
                $errorMessage = $body['errors'][0]['message'] ?? 'Unknown GraphQL error';
                throw new RequestFailedException($errorMessage);
            }

            return $body['data'] ?? [];
        } catch (GuzzleException $e) {
            $statusCode = $e->getCode();

            if ($statusCode === 401 || $statusCode === 403) {
                throw new AuthenticationException('Authentication failed. Please check your token.', $statusCode, $e);
            }

            throw new RequestFailedException($e->getMessage(), $statusCode, $e);
        }
    }

    /**
     * Get the headers for API requests.
     *
     * @return array<string, string>
     */
    private function getHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Client-Platform' => 'web',
            'User-Agent' => 'MonarchMoneyPHP (https://github.com/tmas/monarchmoney-php)',
            'Authorization' => 'Token ' . $this->token,
        ];
    }

    /**
     * Set the request timeout in seconds.
     */
    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
        $this->httpClient = new HttpClient([
            'base_uri' => self::BASE_URL,
            'timeout' => $this->timeout,
        ]);
    }

    /**
     * Get the current timeout setting.
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }
}
