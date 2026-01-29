<?php

declare(strict_types=1);

namespace MonarchMoney;

use MonarchMoney\Concerns\ManagesAccounts;
use MonarchMoney\Concerns\ManagesBudgets;
use MonarchMoney\Concerns\ManagesCategories;
use MonarchMoney\Concerns\ManagesInstitutions;
use MonarchMoney\Concerns\ManagesTags;
use MonarchMoney\Concerns\ManagesTransactions;
use MonarchMoney\Exception\AuthenticationException;
use MonarchMoney\GraphQL\Client;

/**
 * Monarch Money API Client
 *
 * A PHP library for interacting with the Monarch Money API.
 * This library uses token-based authentication only - no login flow is provided.
 *
 * @see https://github.com/hammem/monarchmoney Python library this is based on
 */
class MonarchMoney
{
    use ManagesAccounts;
    use ManagesBudgets;
    use ManagesCategories;
    use ManagesInstitutions;
    use ManagesTags;
    use ManagesTransactions;

    private Client $client;
    private string $token;

    /**
     * Create a new MonarchMoney instance.
     *
     * @param string $token Session token obtained from browser dev tools
     * @param int $timeout Request timeout in seconds
     * @throws AuthenticationException If token is empty
     */
    public function __construct(string $token, int $timeout = 10)
    {
        if (empty($token)) {
            throw new AuthenticationException('A session token is required. See README for instructions on obtaining one.');
        }

        $this->token = $token;
        $this->client = new Client($token, $timeout);
    }

    /**
     * Get the current session token.
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Set the request timeout in seconds.
     */
    public function setTimeout(int $timeout): void
    {
        $this->client->setTimeout($timeout);
    }

    /**
     * Get the current timeout setting.
     */
    public function getTimeout(): int
    {
        return $this->client->getTimeout();
    }

    /**
     * Get the underlying GraphQL client.
     * Useful for making custom queries.
     */
    public function getClient(): Client
    {
        return $this->client;
    }
}
