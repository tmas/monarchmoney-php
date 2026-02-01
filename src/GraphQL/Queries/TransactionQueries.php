<?php

declare(strict_types=1);

namespace MonarchMoney\GraphQL\Queries;

class TransactionQueries
{
    public const GET_TRANSACTIONS_SUMMARY = <<<'GRAPHQL'
        query GetTransactionsPage($filters: TransactionFilterInput) {
            aggregates(filters: $filters) {
                summary {
                    ...TransactionsSummaryFields
                    __typename
                }
                __typename
            }
        }

        fragment TransactionsSummaryFields on TransactionsSummary {
            avg
            count
            max
            maxExpense
            sum
            sumIncome
            sumExpense
            first
            last
            __typename
        }
        GRAPHQL;

    public const GET_TRANSACTIONS = <<<'GRAPHQL'
        query GetTransactionsList($offset: Int, $limit: Int, $filters: TransactionFilterInput, $orderBy: TransactionOrdering) {
            allTransactions(filters: $filters) {
                totalCount
                results(offset: $offset, limit: $limit, orderBy: $orderBy) {
                    id
                    ...TransactionOverviewFields
                    __typename
                }
                __typename
            }
            transactionRules {
                id
                __typename
            }
        }

        fragment TransactionOverviewFields on Transaction {
            id
            amount
            pending
            date
            hideFromReports
            plaidName
            notes
            isRecurring
            reviewStatus
            needsReview
            attachments {
                id
                extension
                filename
                originalAssetUrl
                publicId
                sizeBytes
                __typename
            }
            isSplitTransaction
            createdAt
            updatedAt
            category {
                id
                name
                __typename
            }
            merchant {
                name
                id
                transactionsCount
                __typename
            }
            account {
                id
                displayName
                __typename
            }
            tags {
                id
                name
                color
                order
                __typename
            }
            __typename
        }
        GRAPHQL;

    public const CREATE_TRANSACTION = <<<'GRAPHQL'
        mutation Common_CreateTransactionMutation($input: CreateTransactionMutationInput!) {
            createTransaction(input: $input) {
                errors {
                    ...PayloadErrorFields
                    __typename
                }
                transaction {
                    id
                }
                __typename
            }
        }

        fragment PayloadErrorFields on PayloadError {
            fieldErrors {
                field
                messages
                __typename
            }
            message
            code
            __typename
        }
        GRAPHQL;

    public const DELETE_TRANSACTION = <<<'GRAPHQL'
        mutation Common_DeleteTransactionMutation($input: DeleteTransactionMutationInput!) {
            deleteTransaction(input: $input) {
                deleted
                errors {
                    ...PayloadErrorFields
                    __typename
                }
                __typename
            }
        }

        fragment PayloadErrorFields on PayloadError {
            fieldErrors {
                field
                messages
                __typename
            }
            message
            code
            __typename
        }
        GRAPHQL;

    public const GET_TRANSACTION_DETAILS = <<<'GRAPHQL'
        query GetTransactionDrawer($id: UUID!, $redirectPosted: Boolean) {
            getTransaction(id: $id, redirectPosted: $redirectPosted) {
                id
                amount
                pending
                isRecurring
                date
                originalDate
                hideFromReports
                needsReview
                reviewedAt
                reviewedByUser {
                    id
                    name
                    __typename
                }
                plaidName
                notes
                hasSplitTransactions
                isSplitTransaction
                isManual
                splitTransactions {
                    id
                    ...TransactionDrawerSplitMessageFields
                    __typename
                }
                originalTransaction {
                    id
                    ...OriginalTransactionFields
                    __typename
                }
                attachments {
                    id
                    publicId
                    extension
                    sizeBytes
                    filename
                    originalAssetUrl
                    __typename
                }
                account {
                    id
                    ...TransactionDrawerAccountSectionFields
                    __typename
                }
                category {
                    id
                    __typename
                }
                goal {
                    id
                    __typename
                }
                merchant {
                    id
                    name
                    transactionCount
                    logoUrl
                    recurringTransactionStream {
                        id
                        __typename
                    }
                    __typename
                }
                tags {
                    id
                    name
                    color
                    order
                    __typename
                }
                needsReviewByUser {
                    id
                    __typename
                }
                __typename
            }
            myHousehold {
                users {
                    id
                    name
                    __typename
                }
                __typename
            }
        }

        fragment TransactionDrawerSplitMessageFields on Transaction {
            id
            amount
            merchant {
                id
                name
                __typename
            }
            category {
                id
                name
                __typename
            }
            __typename
        }

        fragment OriginalTransactionFields on Transaction {
            id
            date
            amount
            merchant {
                id
                name
                __typename
            }
            __typename
        }

        fragment TransactionDrawerAccountSectionFields on Account {
            id
            displayName
            logoUrl
            id
            mask
            subtype {
                display
                __typename
            }
            __typename
        }
        GRAPHQL;

    public const GET_TRANSACTION_SPLITS = <<<'GRAPHQL'
        query TransactionSplitQuery($id: UUID!) {
            getTransaction(id: $id) {
                id
                amount
                category {
                    id
                    name
                    __typename
                }
                merchant {
                    id
                    name
                    __typename
                }
                splitTransactions {
                    id
                    merchant {
                        id
                        name
                        __typename
                    }
                    category {
                        id
                        name
                        __typename
                    }
                    amount
                    notes
                    __typename
                }
                __typename
            }
        }
        GRAPHQL;

    public const UPDATE_TRANSACTION_SPLITS = <<<'GRAPHQL'
        mutation Common_SplitTransactionMutation($input: UpdateTransactionSplitMutationInput!) {
            updateTransactionSplit(input: $input) {
                errors {
                    ...PayloadErrorFields
                    __typename
                }
                transaction {
                    id
                    hasSplitTransactions
                    splitTransactions {
                        id
                        merchant {
                            id
                            name
                            __typename
                        }
                        category {
                            id
                            name
                            __typename
                        }
                        amount
                        notes
                        __typename
                    }
                    __typename
                }
                __typename
            }
        }

        fragment PayloadErrorFields on PayloadError {
            fieldErrors {
                field
                messages
                __typename
            }
            message
            code
            __typename
        }
        GRAPHQL;

    public const UPDATE_TRANSACTION = <<<'GRAPHQL'
        mutation Web_TransactionDrawerUpdateTransaction($input: UpdateTransactionMutationInput!) {
            updateTransaction(input: $input) {
                transaction {
                    id
                    amount
                    pending
                    date
                    hideFromReports
                    needsReview
                    reviewedAt
                    reviewedByUser {
                        id
                        name
                        __typename
                    }
                    plaidName
                    notes
                    isRecurring
                    category {
                        id
                        __typename
                    }
                    goal {
                        id
                        __typename
                    }
                    merchant {
                        id
                        name
                        __typename
                    }
                    __typename
                }
                errors {
                    ...PayloadErrorFields
                    __typename
                }
                __typename
            }
        }

        fragment PayloadErrorFields on PayloadError {
            fieldErrors {
                field
                messages
                __typename
            }
            message
            code
            __typename
        }
        GRAPHQL;

    public const GET_RECURRING_TRANSACTIONS = <<<'GRAPHQL'
        query Web_GetUpcomingRecurringTransactionItems($startDate: Date!, $endDate: Date!, $filters: RecurringTransactionFilter) {
            recurringTransactionItems(
                startDate: $startDate
                endDate: $endDate
                filters: $filters
            ) {
                stream {
                    id
                    frequency
                    amount
                    isApproximate
                    merchant {
                        id
                        name
                        logoUrl
                        __typename
                    }
                    __typename
                }
                date
                isPast
                transactionId
                amount
                amountDiff
                category {
                    id
                    name
                    __typename
                }
                account {
                    id
                    displayName
                    logoUrl
                    __typename
                }
                __typename
            }
        }
        GRAPHQL;
}
