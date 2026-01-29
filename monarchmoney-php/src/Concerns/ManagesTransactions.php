<?php

declare(strict_types=1);

namespace MonarchMoney\Concerns;

use MonarchMoney\Exception\RequestFailedException;
use MonarchMoney\GraphQL\Queries\TransactionQueries;

trait ManagesTransactions
{
    /**
     * Gets transaction data from the account.
     *
     * @param int $limit Maximum number of transactions to retrieve
     * @param int $offset Number of transactions to skip
     * @param string|null $startDate Earliest date (YYYY-MM-DD)
     * @param string|null $endDate Latest date (YYYY-MM-DD)
     * @param string $search Search string
     * @param array<string> $categoryIds Category IDs to filter
     * @param array<string> $accountIds Account IDs to filter
     * @param array<string> $tagIds Tag IDs to filter
     * @param bool|null $hasAttachments Filter by attachment presence
     * @param bool|null $hasNotes Filter by notes presence
     * @param bool|null $hiddenFromReports Filter by hidden from reports
     * @param bool|null $isSplit Filter by split transactions
     * @param bool|null $isRecurring Filter by recurring transactions
     * @param bool|null $importedFromMint Filter by Mint import
     * @param bool|null $syncedFromInstitution Filter by institution sync
     * @return array<string, mixed>
     * @throws RequestFailedException
     */
    public function getTransactions(
        int $limit = 100,
        int $offset = 0,
        ?string $startDate = null,
        ?string $endDate = null,
        string $search = '',
        array $categoryIds = [],
        array $accountIds = [],
        array $tagIds = [],
        ?bool $hasAttachments = null,
        ?bool $hasNotes = null,
        ?bool $hiddenFromReports = null,
        ?bool $isSplit = null,
        ?bool $isRecurring = null,
        ?bool $importedFromMint = null,
        ?bool $syncedFromInstitution = null
    ): array {
        $variables = [
            'offset' => $offset,
            'limit' => $limit,
            'orderBy' => 'date',
            'filters' => [
                'search' => $search,
                'categories' => $categoryIds,
                'accounts' => $accountIds,
                'tags' => $tagIds,
            ],
        ];

        if ($hasAttachments !== null) {
            $variables['filters']['hasAttachments'] = $hasAttachments;
        }

        if ($hasNotes !== null) {
            $variables['filters']['hasNotes'] = $hasNotes;
        }

        if ($hiddenFromReports !== null) {
            $variables['filters']['hideFromReports'] = $hiddenFromReports;
        }

        if ($isRecurring !== null) {
            $variables['filters']['isRecurring'] = $isRecurring;
        }

        if ($isSplit !== null) {
            $variables['filters']['isSplit'] = $isSplit;
        }

        if ($importedFromMint !== null) {
            $variables['filters']['importedFromMint'] = $importedFromMint;
        }

        if ($syncedFromInstitution !== null) {
            $variables['filters']['syncedFromInstitution'] = $syncedFromInstitution;
        }

        if ($startDate !== null && $endDate !== null) {
            $variables['filters']['startDate'] = $startDate;
            $variables['filters']['endDate'] = $endDate;
        } elseif ($startDate !== null || $endDate !== null) {
            throw new RequestFailedException(
                'You must specify both a startDate and endDate, not just one of them.'
            );
        }

        return $this->client->query(
            'GetTransactionsList',
            TransactionQueries::GET_TRANSACTIONS,
            $variables
        );
    }

    /**
     * Gets transactions summary from the account.
     *
     * @param array<string, mixed> $filters Optional filters
     * @return array<string, mixed>
     */
    public function getTransactionsSummary(array $filters = []): array
    {
        return $this->client->query(
            'GetTransactionsPage',
            TransactionQueries::GET_TRANSACTIONS_SUMMARY,
            ['filters' => (object) $filters]
        );
    }

    /**
     * Returns detailed information about a transaction.
     *
     * @param string $id Transaction ID
     * @param bool $redirectPosted Whether to redirect posted transactions
     * @return array<string, mixed>
     */
    public function getTransactionDetails(string $id, bool $redirectPosted = true): array
    {
        return $this->client->query(
            'GetTransactionDrawer',
            TransactionQueries::GET_TRANSACTION_DETAILS,
            [
                'id' => $id,
                'redirectPosted' => $redirectPosted,
            ]
        );
    }

    /**
     * Returns the transaction split information for a transaction.
     *
     * @param string $id Transaction ID
     * @return array<string, mixed>
     */
    public function getTransactionSplits(string $id): array
    {
        return $this->client->query(
            'TransactionSplitQuery',
            TransactionQueries::GET_TRANSACTION_SPLITS,
            ['id' => $id]
        );
    }

    /**
     * Fetches upcoming recurring transactions.
     *
     * @param string $startDate Start date (YYYY-MM-DD)
     * @param string $endDate End date (YYYY-MM-DD)
     * @return array<string, mixed>
     * @throws RequestFailedException
     */
    public function getRecurringTransactions(string $startDate, string $endDate): array
    {
        return $this->client->query(
            'Web_GetUpcomingRecurringTransactionItems',
            TransactionQueries::GET_RECURRING_TRANSACTIONS,
            [
                'startDate' => $startDate,
                'endDate' => $endDate,
            ]
        );
    }

    /**
     * Creates a transaction with the given parameters.
     *
     * @param string $date Transaction date (YYYY-MM-DD)
     * @param string $accountId Account ID
     * @param float $amount Transaction amount
     * @param string $merchantName Merchant name
     * @param string|null $categoryId Category ID
     * @param string|null $notes Transaction notes
     * @param bool $updateBalance Whether to update account balance
     * @return array<string, mixed>
     */
    public function createTransaction(
        string $date,
        string $accountId,
        float $amount,
        string $merchantName,
        ?string $categoryId = null,
        ?string $notes = null,
        bool $updateBalance = false
    ): array {
        $input = [
            'date' => $date,
            'accountId' => $accountId,
            'amount' => round($amount, 2),
            'merchantName' => $merchantName,
            'shouldUpdateBalance' => $updateBalance,
        ];

        if ($categoryId !== null) {
            $input['categoryId'] = $categoryId;
        }

        if ($notes !== null) {
            $input['notes'] = $notes;
        }

        return $this->client->query(
            'Common_CreateTransactionMutation',
            TransactionQueries::CREATE_TRANSACTION,
            ['input' => $input]
        );
    }

    /**
     * Updates a single existing transaction.
     *
     * @param string $id Transaction ID
     * @param array<string, mixed> $updates Array of updates. Supported keys:
     *   - categoryId: string
     *   - merchantName: string
     *   - goalId: string
     *   - amount: float
     *   - date: string (YYYY-MM-DD)
     *   - hideFromReports: bool
     *   - needsReview: bool
     *   - notes: string
     * @return array<string, mixed>
     */
    public function updateTransaction(string $id, array $updates): array
    {
        $input = ['id' => $id];

        if (array_key_exists('categoryId', $updates)) {
            $input['category'] = $updates['categoryId'];
        }

        if (array_key_exists('merchantName', $updates)) {
            $input['name'] = $updates['merchantName'];
        }

        if (array_key_exists('amount', $updates) && $updates['amount'] !== null && $updates['amount'] !== '') {
            $input['amount'] = $updates['amount'];
        }

        if (array_key_exists('date', $updates) && $updates['date'] !== null && $updates['date'] !== '') {
            $input['date'] = $updates['date'];
        }

        if (array_key_exists('hideFromReports', $updates)) {
            $input['hideFromReports'] = (bool) $updates['hideFromReports'];
        }

        if (array_key_exists('needsReview', $updates)) {
            $input['needsReview'] = (bool) $updates['needsReview'];
        }

        if (array_key_exists('goalId', $updates)) {
            $input['goalId'] = $updates['goalId'];
        }

        if (array_key_exists('notes', $updates)) {
            $input['notes'] = $updates['notes'];
        }

        return $this->client->query(
            'Web_TransactionDrawerUpdateTransaction',
            TransactionQueries::UPDATE_TRANSACTION,
            ['input' => $input]
        );
    }

    /**
     * Deletes the given transaction.
     *
     * @param string $id Transaction ID
     * @return bool True if deleted successfully
     * @throws RequestFailedException
     */
    public function deleteTransaction(string $id): bool
    {
        $response = $this->client->query(
            'Common_DeleteTransactionMutation',
            TransactionQueries::DELETE_TRANSACTION,
            [
                'input' => [
                    'transactionId' => $id,
                ],
            ]
        );

        if (!($response['deleteTransaction']['deleted'] ?? false)) {
            throw new RequestFailedException(
                json_encode($response['deleteTransaction']['errors'] ?? []) ?: 'Unknown error'
            );
        }

        return true;
    }

    /**
     * Creates, modifies, or deletes the splits for a given transaction.
     *
     * @param string $id Original transaction ID
     * @param array<array<string, mixed>> $splitData Splits data. Each split: ['merchantName' => '...', 'amount' => -12.34, 'categoryId' => '231']
     * @return array<string, mixed>
     */
    public function updateTransactionSplits(string $id, array $splitData): array
    {
        return $this->client->query(
            'Common_SplitTransactionMutation',
            TransactionQueries::UPDATE_TRANSACTION_SPLITS,
            [
                'input' => [
                    'transactionId' => $id,
                    'splitData' => $splitData,
                ],
            ]
        );
    }
}
