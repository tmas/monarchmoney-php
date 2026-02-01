# Monarch Money PHP

A PHP library for interacting with the Monarch Money API. This is a PHP port of the excellent [monarchmoney](https://github.com/hammem/monarchmoney) Python library by hammem.

## Credits

This library is based on the [monarchmoney](https://github.com/hammem/monarchmoney) Python library created by [hammem](https://github.com/hammem). The GraphQL queries and API structure are derived from that project.

## Security Warning

**IMPORTANT: This library is designed for personal use with your OWN Monarch Money account.**

**DO NOT:**
- Build applications that collect or store other users' Monarch Money credentials
- Request or handle login credentials from users
- Create services that act as intermediaries for Monarch Money authentication

This library intentionally does not include login functionality. You must obtain your own session token from your browser.

**If you are building a service for others, you should work with Monarch Money directly to establish a proper OAuth integration or official API partnership.**

## Requirements

- PHP 8.1 or higher
- Composer

## Installation

```bash
composer require tmas/monarchmoney-php
```

## Getting Your Session Token

Since this library does not handle authentication, you need to obtain your session token manually:

1. Log in to [Monarch Money](https://app.monarchmoney.com) in your browser
2. Open Developer Tools (F12 or right-click -> Inspect)
3. Go to the **Network** tab
4. Refresh the page or navigate within the app
5. Click on any request to `api.monarchmoney.com`
6. In the **Headers** section, find the `Authorization` header
7. Copy the token value (after "Token ")

The token looks something like: `eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...`

## Usage

```php
<?php

require 'vendor/autoload.php';

use MonarchMoney\MonarchMoney;

// Initialize with your session token
$monarch = new MonarchMoney('your-session-token-here');

// Get all accounts
$accounts = $monarch->getAccounts();
print_r($accounts);

// Get transactions
$transactions = $monarch->getTransactions(
    limit: 50,
    startDate: '2024-01-01',
    endDate: '2024-01-31'
);

// Get budgets
$budgets = $monarch->getBudgets('2024-01-01', '2024-01-31');

// Create a tag
$tag = $monarch->createTransactionTag('Vacation', '#19D2A5');

// Update a transaction
$monarch->updateTransaction('transaction-id', [
    'categoryId' => 'category-id',
    'notes' => 'Updated via API',
]);
```

## Available Methods

### Accounts

| Method | Description |
|--------|-------------|
| `getAccounts()` | Get all accounts |
| `getAccountTypeOptions()` | Get available account types and subtypes |
| `getRecentAccountBalances(?string $startDate)` | Get daily balances for all accounts |
| `getAccountSnapshotsByType(string $startDate, string $timeframe)` | Get snapshots by account type |
| `getAggregateSnapshots(?string $startDate, ?string $endDate, ?string $accountType)` | Get aggregate daily snapshots |
| `getAccountHoldings(string $accountId)` | Get holdings for investment accounts |
| `getAccountHistory(string $accountId)` | Get historical balance data |
| `createManualAccount(string $type, string $subtype, bool $includeInNetWorth, string $name, float $balance)` | Create a manual account |
| `updateAccount(string $id, array $updates)` | Update account details |
| `deleteAccount(string $id)` | Delete an account |
| `requestAccountsRefresh(array $accountIds)` | Request account refresh |
| `isAccountsRefreshComplete(?array $accountIds)` | Check if refresh is complete |
| `requestAccountsRefreshAndWait(?array $accountIds, int $timeout, int $delay)` | Refresh and wait for completion |

### Transactions

| Method | Description |
|--------|-------------|
| `getTransactions(...)` | Get transactions with filters |
| `getTransactionsSummary(array $filters)` | Get transaction summary statistics |
| `getTransactionDetails(string $id)` | Get detailed transaction info |
| `getTransactionSplits(string $id)` | Get split transaction details |
| `getRecurringTransactions(string $startDate, string $endDate)` | Get recurring transactions |
| `createTransaction(...)` | Create a new transaction |
| `updateTransaction(string $id, array $updates)` | Update a transaction |
| `deleteTransaction(string $id)` | Delete a transaction |
| `updateTransactionSplits(string $id, array $splitData)` | Update transaction splits |

### Categories

| Method | Description |
|--------|-------------|
| `getTransactionCategories()` | Get all categories |
| `getTransactionCategoryGroups()` | Get all category groups |
| `createTransactionCategory(...)` | Create a new category |
| `deleteTransactionCategory(string $id, ?string $moveToCategoryId)` | Delete a category |
| `deleteTransactionCategories(array $ids, ?string $moveToCategoryId)` | Delete multiple categories |

### Tags

| Method | Description |
|--------|-------------|
| `getTransactionTags()` | Get all tags |
| `createTransactionTag(string $name, string $color)` | Create a new tag |
| `setTransactionTags(string $transactionId, array $tagIds)` | Set tags on a transaction |

### Budgets

| Method | Description |
|--------|-------------|
| `getBudgets(?string $startDate, ?string $endDate)` | Get budget data |
| `setBudgetAmount(float $amount, ?string $categoryId, ?string $categoryGroupId, ...)` | Set budget amount |
| `getCashflow(?string $startDate, ?string $endDate)` | Get cashflow data |
| `getCashflowSummary(?string $startDate, ?string $endDate)` | Get cashflow summary |

### Institutions

| Method | Description |
|--------|-------------|
| `getInstitutions()` | Get linked institutions |
| `getSubscriptionDetails()` | Get subscription information |

## Error Handling

The library throws specific exceptions:

```php
use MonarchMoney\Exception\AuthenticationException;
use MonarchMoney\Exception\RequestFailedException;
use MonarchMoney\Exception\MonarchMoneyException;

try {
    $monarch = new MonarchMoney('invalid-token');
    $accounts = $monarch->getAccounts();
} catch (AuthenticationException $e) {
    // Handle authentication errors (401, 403)
    echo "Auth error: " . $e->getMessage();
} catch (RequestFailedException $e) {
    // Handle API request errors
    echo "Request failed: " . $e->getMessage();
} catch (MonarchMoneyException $e) {
    // Handle other library errors
    echo "Error: " . $e->getMessage();
}
```

## Custom Queries

If you need to make custom GraphQL queries:

```php
$client = $monarch->getClient();

$result = $client->query(
    'MyCustomQuery',
    'query MyCustomQuery { ... }',
    ['variable' => 'value']
);
```

## License

MIT License - see [LICENSE](LICENSE) for details.

## Disclaimer

This library is not affiliated with, endorsed by, or connected to Monarch Money in any way. Use at your own risk. The API is unofficial and may change without notice.
