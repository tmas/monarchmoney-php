<?php

declare(strict_types=1);

namespace MonarchMoney\Concerns;

use DateTimeImmutable;
use MonarchMoney\Exception\RequestFailedException;
use MonarchMoney\GraphQL\Queries\AccountQueries;

trait ManagesAccounts
{
    /**
     * Gets the list of accounts configured in the Monarch Money account.
     *
     * @return array<string, mixed>
     */
    public function getAccounts(): array
    {
        return $this->client->query('GetAccounts', AccountQueries::GET_ACCOUNTS);
    }

    /**
     * Retrieves a list of available account types and their subtypes.
     *
     * @return array<string, mixed>
     */
    public function getAccountTypeOptions(): array
    {
        return $this->client->query('GetAccountTypeOptions', AccountQueries::GET_ACCOUNT_TYPE_OPTIONS);
    }

    /**
     * Retrieves the daily balance for all accounts starting from $startDate.
     *
     * @param string|null $startDate ISO formatted datestring (YYYY-MM-DD). If null, last 31 days.
     * @return array<string, mixed>
     */
    public function getRecentAccountBalances(?string $startDate = null): array
    {
        if ($startDate === null) {
            $startDate = (new DateTimeImmutable('-31 days'))->format('Y-m-d');
        }

        return $this->client->query(
            'GetAccountRecentBalances',
            AccountQueries::GET_RECENT_ACCOUNT_BALANCES,
            ['startDate' => $startDate]
        );
    }

    /**
     * Retrieves snapshots of the net values of all accounts of a given type.
     *
     * @param string $startDate ISO datestring (YYYY-MM-DD)
     * @param string $timeframe Either "year" or "month"
     * @return array<string, mixed>
     * @throws RequestFailedException
     */
    public function getAccountSnapshotsByType(string $startDate, string $timeframe): array
    {
        if (!in_array($timeframe, ['year', 'month'], true)) {
            throw new RequestFailedException("Unknown timeframe \"{$timeframe}\"");
        }

        return $this->client->query(
            'GetSnapshotsByAccountType',
            AccountQueries::GET_SNAPSHOTS_BY_ACCOUNT_TYPE,
            ['startDate' => $startDate, 'timeframe' => $timeframe]
        );
    }

    /**
     * Retrieves the daily net value of all accounts.
     *
     * @param string|null $startDate ISO datestring (YYYY-MM-DD)
     * @param string|null $endDate ISO datestring (YYYY-MM-DD)
     * @param string|null $accountType Filter by account type
     * @return array<string, mixed>
     */
    public function getAggregateSnapshots(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $accountType = null
    ): array {
        if ($startDate === null) {
            $today = new DateTimeImmutable();
            $startDate = $today->modify('-150 years')->format('Y-m-01');
        }

        return $this->client->query(
            'GetAggregateSnapshots',
            AccountQueries::GET_AGGREGATE_SNAPSHOTS,
            [
                'filters' => [
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'accountType' => $accountType,
                ],
            ]
        );
    }

    /**
     * Get the holdings information for a brokerage or similar type of account.
     *
     * @param string $accountId The account ID
     * @return array<string, mixed>
     */
    public function getAccountHoldings(string $accountId): array
    {
        $today = (new DateTimeImmutable())->format('Y-m-d');

        return $this->client->query(
            'Web_GetHoldings',
            AccountQueries::GET_HOLDINGS,
            [
                'input' => [
                    'accountIds' => [$accountId],
                    'endDate' => $today,
                    'includeHiddenHoldings' => true,
                    'startDate' => $today,
                ],
            ]
        );
    }

    /**
     * Gets historical account snapshot data for the requested account.
     *
     * @param string $accountId Monarch account ID
     * @return array<array<string, mixed>>
     */
    public function getAccountHistory(string $accountId): array
    {
        $accountDetails = $this->client->query(
            'AccountDetails_getAccount',
            AccountQueries::GET_ACCOUNT_HISTORY,
            ['id' => $accountId]
        );

        $accountName = $accountDetails['account']['displayName'] ?? '';
        $accountBalanceHistory = $accountDetails['snapshots'] ?? [];

        foreach ($accountBalanceHistory as &$snapshot) {
            $snapshot['accountId'] = $accountId;
            $snapshot['accountName'] = $accountName;
        }

        return $accountBalanceHistory;
    }

    /**
     * Creates a new manual account.
     *
     * @param string $type Account group type (e.g., loan, other_liability, other_asset)
     * @param string $subtype Account sub type (e.g., auto, commercial, mortgage)
     * @param bool $includeInNetWorth Include in net worth calculation
     * @param string $name Account name
     * @param float $balance Initial balance
     * @return array<string, mixed>
     */
    public function createManualAccount(
        string $type,
        string $subtype,
        bool $includeInNetWorth,
        string $name,
        float $balance = 0.0
    ): array {
        return $this->client->query(
            'Web_CreateManualAccount',
            AccountQueries::CREATE_MANUAL_ACCOUNT,
            [
                'input' => [
                    'type' => $type,
                    'subtype' => $subtype,
                    'includeInNetWorth' => $includeInNetWorth,
                    'name' => $name,
                    'displayBalance' => $balance,
                ],
            ]
        );
    }

    /**
     * Updates the details of an account.
     *
     * @param string $id Account ID
     * @param array<string, mixed> $updates Array of updates. Supported keys:
     *   - name: string
     *   - displayBalance: float
     *   - type: string
     *   - subtype: string
     *   - includeInNetWorth: bool
     *   - hideFromList: bool
     *   - hideTransactionsFromReports: bool
     * @return array<string, mixed>
     */
    public function updateAccount(string $id, array $updates): array
    {
        $input = ['id' => $id];

        $allowedKeys = [
            'name',
            'displayBalance',
            'type',
            'subtype',
            'includeInNetWorth',
            'hideFromList',
            'hideTransactionsFromReports',
        ];

        foreach ($allowedKeys as $key) {
            if (array_key_exists($key, $updates)) {
                $input[$key] = $updates[$key];
            }
        }

        return $this->client->query(
            'Common_UpdateAccount',
            AccountQueries::UPDATE_ACCOUNT,
            ['input' => $input]
        );
    }

    /**
     * Deletes an account.
     *
     * @param string $id Account ID
     * @return array<string, mixed>
     */
    public function deleteAccount(string $id): array
    {
        return $this->client->query(
            'Common_DeleteAccount',
            AccountQueries::DELETE_ACCOUNT,
            ['id' => $id]
        );
    }

    /**
     * Requests Monarch to refresh account balances and transactions.
     *
     * @param array<string> $accountIds Account IDs to refresh
     * @return bool True if request was successful
     * @throws RequestFailedException
     */
    public function requestAccountsRefresh(array $accountIds): bool
    {
        $response = $this->client->query(
            'Common_ForceRefreshAccountsMutation',
            AccountQueries::FORCE_REFRESH_ACCOUNTS,
            [
                'input' => [
                    'accountIds' => $accountIds,
                ],
            ]
        );

        if (!($response['forceRefreshAccounts']['success'] ?? false)) {
            throw new RequestFailedException(
                json_encode($response['forceRefreshAccounts']['errors'] ?? []) ?: 'Unknown error'
            );
        }

        return true;
    }

    /**
     * Checks on the status of a prior request to refresh account balances.
     *
     * @param array<string>|null $accountIds Account IDs to check. If null, checks all.
     * @return bool True if refresh is complete, false if still in progress
     * @throws RequestFailedException
     */
    public function isAccountsRefreshComplete(?array $accountIds = null): bool
    {
        $response = $this->client->query(
            'ForceRefreshAccountsQuery',
            AccountQueries::FORCE_REFRESH_ACCOUNTS_QUERY
        );

        if (!isset($response['accounts'])) {
            throw new RequestFailedException('Unable to request status of refresh');
        }

        $accounts = $response['accounts'];

        if ($accountIds !== null) {
            $accounts = array_filter(
                $accounts,
                fn(array $account): bool => in_array($account['id'], $accountIds, true)
            );
        }

        foreach ($accounts as $account) {
            if ($account['hasSyncInProgress'] ?? false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Convenience method for forcing an accounts refresh and waiting for completion.
     *
     * @param array<string>|null $accountIds Account IDs to refresh. If null, refreshes all.
     * @param int $timeout Timeout in seconds
     * @param int $delay Delay between checks in seconds
     * @return bool True if all accounts refreshed within timeout
     */
    public function requestAccountsRefreshAndWait(
        ?array $accountIds = null,
        int $timeout = 300,
        int $delay = 10
    ): bool {
        if ($accountIds === null) {
            $accountData = $this->getAccounts();
            $accountIds = array_column($accountData['accounts'] ?? [], 'id');
        }

        $this->requestAccountsRefresh($accountIds);

        $start = time();
        $refreshed = false;

        while (!$refreshed && (time() <= ($start + $timeout))) {
            sleep($delay);
            $refreshed = $this->isAccountsRefreshComplete($accountIds);
        }

        return $refreshed;
    }
}
