<?php

declare(strict_types=1);

namespace MonarchMoney\Concerns;

use DateTimeImmutable;
use MonarchMoney\Exception\RequestFailedException;
use MonarchMoney\GraphQL\Queries\BudgetQueries;

trait ManagesBudgets
{
    /**
     * Get your budgets and corresponding actual amounts from the account.
     *
     * @param string|null $startDate Start date (YYYY-MM-DD), defaults to last month
     * @param string|null $endDate End date (YYYY-MM-DD), defaults to next month
     * @return array<string, mixed>
     * @throws RequestFailedException
     */
    public function getBudgets(?string $startDate = null, ?string $endDate = null): array
    {
        if ($startDate === null && $endDate === null) {
            $today = new DateTimeImmutable();
            $startDate = $today->modify('first day of last month')->format('Y-m-d');
            $endDate = $today->modify('last day of next month')->format('Y-m-d');
        } elseif ($startDate === null || $endDate === null) {
            throw new RequestFailedException(
                'You must specify both a startDate and endDate, not just one of them.'
            );
        }

        return $this->client->query(
            'Common_GetJointPlanningData',
            BudgetQueries::GET_BUDGETS,
            [
                'startDate' => $startDate,
                'endDate' => $endDate,
            ]
        );
    }

    /**
     * Updates the budget amount for the given category.
     *
     * @param float $amount Budget amount (can be negative for over-budget, zero to clear)
     * @param string|null $categoryId Category ID (cannot be provided with categoryGroupId)
     * @param string|null $categoryGroupId Category group ID (cannot be provided with categoryId)
     * @param string|null $startDate Start date (YYYY-MM-DD), defaults to first of current month
     * @param string $timeframe Timeframe (currently only "month" is valid)
     * @param bool $applyToFuture Apply to all future timeframes
     * @return array<string, mixed>
     * @throws RequestFailedException
     */
    public function setBudgetAmount(
        float $amount,
        ?string $categoryId = null,
        ?string $categoryGroupId = null,
        ?string $startDate = null,
        string $timeframe = 'month',
        bool $applyToFuture = false
    ): array {
        if (($categoryId === null) === ($categoryGroupId === null)) {
            throw new RequestFailedException(
                'You must specify either a categoryId OR categoryGroupId; not both'
            );
        }

        if ($startDate === null) {
            $startDate = (new DateTimeImmutable('first day of this month'))->format('Y-m-d');
        }

        return $this->client->query(
            'Common_UpdateBudgetItem',
            BudgetQueries::SET_BUDGET_AMOUNT,
            [
                'input' => [
                    'startDate' => $startDate,
                    'timeframe' => $timeframe,
                    'categoryId' => $categoryId,
                    'categoryGroupId' => $categoryGroupId,
                    'amount' => $amount,
                    'applyToFuture' => $applyToFuture,
                ],
            ]
        );
    }

    /**
     * Gets cashflow data for the account.
     *
     * @param string|null $startDate Start date (YYYY-MM-DD), defaults to first of current month
     * @param string|null $endDate End date (YYYY-MM-DD), defaults to last of current month
     * @return array<string, mixed>
     * @throws RequestFailedException
     */
    public function getCashflow(?string $startDate = null, ?string $endDate = null): array
    {
        if ($startDate === null && $endDate === null) {
            $today = new DateTimeImmutable();
            $startDate = $today->modify('first day of this month')->format('Y-m-d');
            $endDate = $today->modify('last day of this month')->format('Y-m-d');
        } elseif ($startDate === null || $endDate === null) {
            throw new RequestFailedException(
                'You must specify both a startDate and endDate, not just one of them.'
            );
        }

        return $this->client->query(
            'Web_GetCashFlowPage',
            BudgetQueries::GET_CASHFLOW,
            [
                'filters' => [
                    'search' => '',
                    'categories' => [],
                    'accounts' => [],
                    'tags' => [],
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                ],
            ]
        );
    }

    /**
     * Gets cashflow summary for the account.
     *
     * @param string|null $startDate Start date (YYYY-MM-DD), defaults to first of current month
     * @param string|null $endDate End date (YYYY-MM-DD), defaults to last of current month
     * @return array<string, mixed>
     * @throws RequestFailedException
     */
    public function getCashflowSummary(?string $startDate = null, ?string $endDate = null): array
    {
        if ($startDate === null && $endDate === null) {
            $today = new DateTimeImmutable();
            $startDate = $today->modify('first day of this month')->format('Y-m-d');
            $endDate = $today->modify('last day of this month')->format('Y-m-d');
        } elseif ($startDate === null || $endDate === null) {
            throw new RequestFailedException(
                'You must specify both a startDate and endDate, not just one of them.'
            );
        }

        return $this->client->query(
            'Web_GetCashFlowPage',
            BudgetQueries::GET_CASHFLOW_SUMMARY,
            [
                'filters' => [
                    'search' => '',
                    'categories' => [],
                    'accounts' => [],
                    'tags' => [],
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                ],
            ]
        );
    }
}
