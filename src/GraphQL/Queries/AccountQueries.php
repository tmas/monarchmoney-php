<?php

declare(strict_types=1);

namespace MonarchMoney\GraphQL\Queries;

class AccountQueries
{
    public const GET_ACCOUNTS = <<<'GRAPHQL'
        query GetAccounts {
            accounts {
                ...AccountFields
                __typename
            }
            householdPreferences {
                id
                accountGroupOrder
                __typename
            }
        }

        fragment AccountFields on Account {
            id
            displayName
            syncDisabled
            deactivatedAt
            isHidden
            isAsset
            mask
            createdAt
            updatedAt
            displayLastUpdatedAt
            currentBalance
            displayBalance
            includeInNetWorth
            hideFromList
            hideTransactionsFromReports
            includeBalanceInNetWorth
            includeInGoalBalance
            dataProvider
            dataProviderAccountId
            isManual
            transactionsCount
            holdingsCount
            manualInvestmentsTrackingMethod
            order
            logoUrl
            type {
                name
                display
                __typename
            }
            subtype {
                name
                display
                __typename
            }
            credential {
                id
                updateRequired
                disconnectedFromDataProviderAt
                dataProvider
                institution {
                    id
                    plaidInstitutionId
                    name
                    status
                    __typename
                }
                __typename
            }
            institution {
                id
                name
                primaryColor
                url
                __typename
            }
            __typename
        }
        GRAPHQL;

    public const GET_ACCOUNT_TYPE_OPTIONS = <<<'GRAPHQL'
        query GetAccountTypeOptions {
            accountTypeOptions {
                type {
                    name
                    display
                    group
                    possibleSubtypes {
                        display
                        name
                        __typename
                    }
                    __typename
                }
                subtype {
                    name
                    display
                    __typename
                }
                __typename
            }
        }
        GRAPHQL;

    public const GET_RECENT_ACCOUNT_BALANCES = <<<'GRAPHQL'
        query GetAccountRecentBalances($startDate: Date!) {
            accounts {
                id
                recentBalances(startDate: $startDate)
                __typename
            }
        }
        GRAPHQL;

    public const GET_SNAPSHOTS_BY_ACCOUNT_TYPE = <<<'GRAPHQL'
        query GetSnapshotsByAccountType($startDate: Date!, $timeframe: Timeframe!) {
            snapshotsByAccountType(startDate: $startDate, timeframe: $timeframe) {
                accountType
                month
                balance
                __typename
            }
            accountTypes {
                name
                group
                __typename
            }
        }
        GRAPHQL;

    public const GET_AGGREGATE_SNAPSHOTS = <<<'GRAPHQL'
        query GetAggregateSnapshots($filters: AggregateSnapshotFilters) {
            aggregateSnapshots(filters: $filters) {
                date
                balance
                __typename
            }
        }
        GRAPHQL;

    public const CREATE_MANUAL_ACCOUNT = <<<'GRAPHQL'
        mutation Web_CreateManualAccount($input: CreateManualAccountMutationInput!) {
            createManualAccount(input: $input) {
                account {
                    id
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

    public const UPDATE_ACCOUNT = <<<'GRAPHQL'
        mutation Common_UpdateAccount($input: UpdateAccountMutationInput!) {
            updateAccount(input: $input) {
                account {
                    ...AccountFields
                    __typename
                }
                errors {
                    ...PayloadErrorFields
                    __typename
                }
                __typename
            }
        }

        fragment AccountFields on Account {
            id
            displayName
            syncDisabled
            deactivatedAt
            isHidden
            isAsset
            mask
            createdAt
            updatedAt
            displayLastUpdatedAt
            currentBalance
            displayBalance
            includeInNetWorth
            hideFromList
            hideTransactionsFromReports
            includeBalanceInNetWorth
            includeInGoalBalance
            dataProvider
            dataProviderAccountId
            isManual
            transactionsCount
            holdingsCount
            manualInvestmentsTrackingMethod
            order
            icon
            logoUrl
            deactivatedAt
            type {
                name
                display
                group
                __typename
            }
            subtype {
                name
                display
                __typename
            }
            credential {
                id
                updateRequired
                disconnectedFromDataProviderAt
                dataProvider
                institution {
                    id
                    plaidInstitutionId
                    name
                    status
                    __typename
                }
                __typename
            }
            institution {
                id
                name
                primaryColor
                url
                __typename
            }
            __typename
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

    public const DELETE_ACCOUNT = <<<'GRAPHQL'
        mutation Common_DeleteAccount($id: UUID!) {
            deleteAccount(id: $id) {
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

    public const FORCE_REFRESH_ACCOUNTS = <<<'GRAPHQL'
        mutation Common_ForceRefreshAccountsMutation($input: ForceRefreshAccountsInput!) {
            forceRefreshAccounts(input: $input) {
                success
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

    public const FORCE_REFRESH_ACCOUNTS_QUERY = <<<'GRAPHQL'
        query ForceRefreshAccountsQuery {
            accounts {
                id
                hasSyncInProgress
                __typename
            }
        }
        GRAPHQL;

    public const GET_HOLDINGS = <<<'GRAPHQL'
        query Web_GetHoldings($input: PortfolioInput) {
            portfolio(input: $input) {
                aggregateHoldings {
                    edges {
                        node {
                            id
                            quantity
                            basis
                            totalValue
                            securityPriceChangeDollars
                            securityPriceChangePercent
                            lastSyncedAt
                            holdings {
                                id
                                type
                                typeDisplay
                                name
                                ticker
                                closingPrice
                                isManual
                                closingPriceUpdatedAt
                                __typename
                            }
                            security {
                                id
                                name
                                type
                                ticker
                                typeDisplay
                                currentPrice
                                currentPriceUpdatedAt
                                closingPrice
                                closingPriceUpdatedAt
                                oneDayChangePercent
                                oneDayChangeDollars
                                __typename
                            }
                            __typename
                        }
                        __typename
                    }
                    __typename
                }
                __typename
            }
        }
        GRAPHQL;

    public const GET_ACCOUNT_HISTORY = <<<'GRAPHQL'
        query AccountDetails_getAccount($id: UUID!, $filters: TransactionFilterInput) {
            account(id: $id) {
                id
                ...AccountFields
                ...EditAccountFormFields
                isLiability
                credential {
                    id
                    hasSyncInProgress
                    canBeForceRefreshed
                    disconnectedFromDataProviderAt
                    dataProvider
                    institution {
                        id
                        plaidInstitutionId
                        url
                        ...InstitutionStatusFields
                        __typename
                    }
                    __typename
                }
                institution {
                    id
                    plaidInstitutionId
                    url
                    ...InstitutionStatusFields
                    __typename
                }
                __typename
            }
            transactions: allTransactions(filters: $filters) {
                totalCount
                results(limit: 20) {
                    id
                    ...TransactionsListFields
                    __typename
                }
                __typename
            }
            snapshots: snapshotsForAccount(accountId: $id) {
                date
                signedBalance
                __typename
            }
        }

        fragment AccountFields on Account {
            id
            displayName
            syncDisabled
            deactivatedAt
            isHidden
            isAsset
            mask
            createdAt
            updatedAt
            displayLastUpdatedAt
            currentBalance
            displayBalance
            includeInNetWorth
            hideFromList
            hideTransactionsFromReports
            includeBalanceInNetWorth
            includeInGoalBalance
            dataProvider
            dataProviderAccountId
            isManual
            transactionsCount
            holdingsCount
            manualInvestmentsTrackingMethod
            order
            logoUrl
            type {
                name
                display
                group
                __typename
            }
            subtype {
                name
                display
                __typename
            }
            credential {
                id
                updateRequired
                disconnectedFromDataProviderAt
                dataProvider
                institution {
                    id
                    plaidInstitutionId
                    name
                    status
                    __typename
                }
                __typename
            }
            institution {
                id
                name
                primaryColor
                url
                __typename
            }
            __typename
        }

        fragment EditAccountFormFields on Account {
            id
            displayName
            deactivatedAt
            displayBalance
            includeInNetWorth
            hideFromList
            hideTransactionsFromReports
            dataProvider
            dataProviderAccountId
            isManual
            manualInvestmentsTrackingMethod
            isAsset
            invertSyncedBalance
            canInvertBalance
            type {
                name
                display
                __typename
            }
            subtype {
                name
                display
                __typename
            }
            __typename
        }

        fragment InstitutionStatusFields on Institution {
            id
            hasIssuesReported
            hasIssuesReportedMessage
            plaidStatus
            status
            balanceStatus
            transactionsStatus
            __typename
        }

        fragment TransactionsListFields on Transaction {
            id
            ...TransactionOverviewFields
            __typename
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
            dataProviderDescription
            attachments {
                id
                __typename
            }
            isSplitTransaction
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
            merchant {
                name
                id
                transactionsCount
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
}
