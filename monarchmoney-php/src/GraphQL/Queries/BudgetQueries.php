<?php

declare(strict_types=1);

namespace MonarchMoney\GraphQL\Queries;

class BudgetQueries
{
    public const GET_BUDGETS = <<<'GRAPHQL'
        query Common_GetJointPlanningData($startDate: Date!, $endDate: Date!) {
            budgetSystem
            budgetData(startMonth: $startDate, endMonth: $endDate) {
                ...BudgetDataFields
                __typename
            }
            categoryGroups {
                ...BudgetCategoryGroupFields
                __typename
            }
            goalsV2 {
                ...BudgetDataGoalsV2Fields
                __typename
            }
        }

        fragment BudgetDataMonthlyAmountsFields on BudgetMonthlyAmounts {
            month
            plannedCashFlowAmount
            plannedSetAsideAmount
            actualAmount
            remainingAmount
            previousMonthRolloverAmount
            rolloverType
            cumulativeActualAmount
            rolloverTargetAmount
            __typename
        }

        fragment BudgetMonthlyAmountsByCategoryFields on BudgetCategoryMonthlyAmounts {
            category {
                id
                __typename
            }
            monthlyAmounts {
                ...BudgetDataMonthlyAmountsFields
                __typename
            }
            __typename
        }

        fragment BudgetMonthlyAmountsByCategoryGroupFields on BudgetCategoryGroupMonthlyAmounts {
            categoryGroup {
                id
                __typename
            }
            monthlyAmounts {
                ...BudgetDataMonthlyAmountsFields
                __typename
            }
            __typename
        }

        fragment BudgetMonthlyAmountsForFlexExpenseFields on BudgetFlexMonthlyAmounts {
            budgetVariability
            monthlyAmounts {
                ...BudgetDataMonthlyAmountsFields
                __typename
            }
            __typename
        }

        fragment BudgetDataTotalsByMonthFields on BudgetTotals {
            actualAmount
            plannedAmount
            previousMonthRolloverAmount
            remainingAmount
            __typename
        }

        fragment BudgetTotalsByMonthFields on BudgetMonthTotals {
            month
            totalIncome {
                ...BudgetDataTotalsByMonthFields
                __typename
            }
            totalExpenses {
                ...BudgetDataTotalsByMonthFields
                __typename
            }
            totalFixedExpenses {
                ...BudgetDataTotalsByMonthFields
                __typename
            }
            totalNonMonthlyExpenses {
                ...BudgetDataTotalsByMonthFields
                __typename
            }
            totalFlexibleExpenses {
                ...BudgetDataTotalsByMonthFields
                __typename
            }
            __typename
        }

        fragment BudgetRolloverPeriodFields on BudgetRolloverPeriod {
            id
            startMonth
            endMonth
            startingBalance
            targetAmount
            frequency
            type
            __typename
        }

        fragment BudgetCategoryFields on Category {
            id
            name
            icon
            order
            budgetVariability
            excludeFromBudget
            isSystemCategory
            updatedAt
            group {
                id
                type
                budgetVariability
                groupLevelBudgetingEnabled
                __typename
            }
            rolloverPeriod {
                ...BudgetRolloverPeriodFields
                __typename
            }
            __typename
        }

        fragment BudgetDataFields on BudgetData {
            monthlyAmountsByCategory {
                ...BudgetMonthlyAmountsByCategoryFields
                __typename
            }
            monthlyAmountsByCategoryGroup {
                ...BudgetMonthlyAmountsByCategoryGroupFields
                __typename
            }
            monthlyAmountsForFlexExpense {
                ...BudgetMonthlyAmountsForFlexExpenseFields
                __typename
            }
            totalsByMonth {
                ...BudgetTotalsByMonthFields
                __typename
            }
            __typename
        }

        fragment BudgetCategoryGroupFields on CategoryGroup {
            id
            name
            order
            type
            budgetVariability
            updatedAt
            groupLevelBudgetingEnabled
            categories {
                ...BudgetCategoryFields
                __typename
            }
            rolloverPeriod {
                id
                type
                startMonth
                endMonth
                startingBalance
                frequency
                targetAmount
                __typename
            }
            __typename
        }

        fragment BudgetDataGoalsV2Fields on GoalV2 {
            id
            name
            archivedAt
            completedAt
            priority
            imageStorageProvider
            imageStorageProviderId
            plannedContributions(startMonth: $startDate, endMonth: $endDate) {
                id
                month
                amount
                __typename
            }
            monthlyContributionSummaries(startMonth: $startDate, endMonth: $endDate) {
                month
                sum
                __typename
            }
            __typename
        }
        GRAPHQL;

    public const SET_BUDGET_AMOUNT = <<<'GRAPHQL'
        mutation Common_UpdateBudgetItem($input: UpdateOrCreateBudgetItemMutationInput!) {
            updateOrCreateBudgetItem(input: $input) {
                budgetItem {
                    id
                    budgetAmount
                    __typename
                }
                __typename
            }
        }
        GRAPHQL;

    public const GET_CASHFLOW = <<<'GRAPHQL'
        query Web_GetCashFlowPage($filters: TransactionFilterInput) {
            byCategory: aggregates(filters: $filters, groupBy: ["category"]) {
                groupBy {
                    category {
                        id
                        name
                        group {
                            id
                            type
                            __typename
                        }
                        __typename
                    }
                    __typename
                }
                summary {
                    sum
                    __typename
                }
                __typename
            }
            byCategoryGroup: aggregates(filters: $filters, groupBy: ["categoryGroup"]) {
                groupBy {
                    categoryGroup {
                        id
                        name
                        type
                        __typename
                    }
                    __typename
                }
                summary {
                    sum
                    __typename
                }
                __typename
            }
            byMerchant: aggregates(filters: $filters, groupBy: ["merchant"]) {
                groupBy {
                    merchant {
                        id
                        name
                        logoUrl
                        __typename
                    }
                    __typename
                }
                summary {
                    sumIncome
                    sumExpense
                    __typename
                }
                __typename
            }
            summary: aggregates(filters: $filters, fillEmptyValues: true) {
                summary {
                    sumIncome
                    sumExpense
                    savings
                    savingsRate
                    __typename
                }
                __typename
            }
        }
        GRAPHQL;

    public const GET_CASHFLOW_SUMMARY = <<<'GRAPHQL'
        query Web_GetCashFlowPage($filters: TransactionFilterInput) {
            summary: aggregates(filters: $filters, fillEmptyValues: true) {
                summary {
                    sumIncome
                    sumExpense
                    savings
                    savingsRate
                    __typename
                }
                __typename
            }
        }
        GRAPHQL;
}
