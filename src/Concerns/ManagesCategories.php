<?php

declare(strict_types=1);

namespace MonarchMoney\Concerns;

use DateTimeImmutable;
use MonarchMoney\Exception\RequestFailedException;
use MonarchMoney\GraphQL\Queries\CategoryQueries;

trait ManagesCategories
{
    /**
     * Gets all the categories configured in the account.
     *
     * @return array<string, mixed>
     */
    public function getTransactionCategories(): array
    {
        return $this->client->query('GetCategories', CategoryQueries::GET_CATEGORIES);
    }

    /**
     * Gets all the category groups configured in the account.
     *
     * @return array<string, mixed>
     */
    public function getTransactionCategoryGroups(): array
    {
        return $this->client->query('ManageGetCategoryGroups', CategoryQueries::GET_CATEGORY_GROUPS);
    }

    /**
     * Creates a new transaction category.
     *
     * @param string $groupId Category group ID
     * @param string $name Category name
     * @param string $icon Unicode string or emoji for the icon
     * @param bool $rolloverEnabled Whether rollover is enabled
     * @param string $rolloverType Budget rollover type
     * @param string|null $rolloverStartMonth Rollover start month (YYYY-MM-DD), defaults to first of current month
     * @return array<string, mixed>
     */
    public function createTransactionCategory(
        string $groupId,
        string $name,
        string $icon = "\u{2753}",
        bool $rolloverEnabled = false,
        string $rolloverType = 'monthly',
        ?string $rolloverStartMonth = null
    ): array {
        if ($rolloverStartMonth === null) {
            $rolloverStartMonth = (new DateTimeImmutable('first day of this month'))->format('Y-m-d');
        }

        return $this->client->query(
            'Web_CreateCategory',
            CategoryQueries::CREATE_CATEGORY,
            [
                'input' => [
                    'group' => $groupId,
                    'name' => $name,
                    'icon' => $icon,
                    'rolloverEnabled' => $rolloverEnabled,
                    'rolloverType' => $rolloverType,
                    'rolloverStartMonth' => $rolloverStartMonth,
                ],
            ]
        );
    }

    /**
     * Deletes a transaction category.
     *
     * @param string $id Category ID
     * @param string|null $moveToCategoryId Category to move transactions to
     * @return bool True if deleted successfully
     * @throws RequestFailedException
     */
    public function deleteTransactionCategory(string $id, ?string $moveToCategoryId = null): bool
    {
        $variables = ['id' => $id];

        if ($moveToCategoryId !== null) {
            $variables['moveToCategoryId'] = $moveToCategoryId;
        }

        $response = $this->client->query(
            'Web_DeleteCategory',
            CategoryQueries::DELETE_CATEGORY,
            $variables
        );

        if (!($response['deleteCategory']['deleted'] ?? false)) {
            throw new RequestFailedException(
                json_encode($response['deleteCategory']['errors'] ?? []) ?: 'Unknown error'
            );
        }

        return true;
    }

    /**
     * Deletes a list of transaction categories.
     *
     * @param array<string> $ids Category IDs
     * @param string|null $moveToCategoryId Category to move transactions to
     * @return array<string, bool|RequestFailedException> Results for each category ID
     */
    public function deleteTransactionCategories(array $ids, ?string $moveToCategoryId = null): array
    {
        $results = [];

        foreach ($ids as $id) {
            try {
                $results[$id] = $this->deleteTransactionCategory($id, $moveToCategoryId);
            } catch (RequestFailedException $e) {
                $results[$id] = $e;
            }
        }

        return $results;
    }
}
