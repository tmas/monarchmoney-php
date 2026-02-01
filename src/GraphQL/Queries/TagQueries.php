<?php

declare(strict_types=1);

namespace MonarchMoney\GraphQL\Queries;

class TagQueries
{
    public const GET_TAGS = <<<'GRAPHQL'
        query GetHouseholdTransactionTags($search: String, $limit: Int, $bulkParams: BulkTransactionDataParams) {
            householdTransactionTags(
                search: $search
                limit: $limit
                bulkParams: $bulkParams
            ) {
                id
                name
                color
                order
                transactionCount
                __typename
            }
        }
        GRAPHQL;

    public const CREATE_TAG = <<<'GRAPHQL'
        mutation Common_CreateTransactionTag($input: CreateTransactionTagInput!) {
            createTransactionTag(input: $input) {
                tag {
                    id
                    name
                    color
                    order
                    transactionCount
                    __typename
                }
                errors {
                    message
                    __typename
                }
                __typename
            }
        }
        GRAPHQL;

    public const SET_TRANSACTION_TAGS = <<<'GRAPHQL'
        mutation Web_SetTransactionTags($input: SetTransactionTagsInput!) {
            setTransactionTags(input: $input) {
                errors {
                    ...PayloadErrorFields
                    __typename
                }
                transaction {
                    id
                    tags {
                        id
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
}
