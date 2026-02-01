<?php

declare(strict_types=1);

namespace MonarchMoney\Concerns;

use MonarchMoney\GraphQL\Queries\TagQueries;

trait ManagesTags
{
    /**
     * Gets all the tags configured in the account.
     *
     * @return array<string, mixed>
     */
    public function getTransactionTags(): array
    {
        return $this->client->query('GetHouseholdTransactionTags', TagQueries::GET_TAGS);
    }

    /**
     * Creates a new transaction tag.
     *
     * @param string $name Tag name
     * @param string $color Tag color (six-digit RGB hexadecimal including #, e.g., "#19D2A5")
     * @return array<string, mixed>
     */
    public function createTransactionTag(string $name, string $color): array
    {
        return $this->client->query(
            'Common_CreateTransactionTag',
            TagQueries::CREATE_TAG,
            [
                'input' => [
                    'name' => $name,
                    'color' => $color,
                ],
            ]
        );
    }

    /**
     * Sets the tags on a transaction.
     *
     * @param string $transactionId Transaction ID
     * @param array<string> $tagIds Tag IDs to set (overwrites existing, empty array removes all)
     * @return array<string, mixed>
     */
    public function setTransactionTags(string $transactionId, array $tagIds): array
    {
        return $this->client->query(
            'Web_SetTransactionTags',
            TagQueries::SET_TRANSACTION_TAGS,
            [
                'input' => [
                    'transactionId' => $transactionId,
                    'tagIds' => $tagIds,
                ],
            ]
        );
    }
}
