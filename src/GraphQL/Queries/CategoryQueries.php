<?php

declare(strict_types=1);

namespace MonarchMoney\GraphQL\Queries;

class CategoryQueries
{
    public const GET_CATEGORIES = <<<'GRAPHQL'
        query GetCategories {
            categories {
                ...CategoryFields
                __typename
            }
        }

        fragment CategoryFields on Category {
            id
            order
            name
            systemCategory
            isSystemCategory
            isDisabled
            updatedAt
            createdAt
            group {
                id
                name
                type
                __typename
            }
            __typename
        }
        GRAPHQL;

    public const GET_CATEGORY_GROUPS = <<<'GRAPHQL'
        query ManageGetCategoryGroups {
            categoryGroups {
                id
                name
                order
                type
                updatedAt
                createdAt
                __typename
            }
        }
        GRAPHQL;

    public const CREATE_CATEGORY = <<<'GRAPHQL'
        mutation Web_CreateCategory($input: CreateCategoryInput!) {
            createCategory(input: $input) {
                errors {
                    ...PayloadErrorFields
                    __typename
                }
                category {
                    id
                    ...CategoryFormFields
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
        fragment CategoryFormFields on Category {
            id
            order
            name
            systemCategory
            systemCategoryDisplayName
            budgetVariability
            isSystemCategory
            isDisabled
            group {
                id
                type
                groupLevelBudgetingEnabled
                __typename
            }
            rolloverPeriod {
                id
                startMonth
                startingBalance
                __typename
            }
            __typename
        }
        GRAPHQL;

    public const DELETE_CATEGORY = <<<'GRAPHQL'
        mutation Web_DeleteCategory($id: UUID!, $moveToCategoryId: UUID) {
            deleteCategory(id: $id, moveToCategoryId: $moveToCategoryId) {
                errors {
                    ...PayloadErrorFields
                    __typename
                }
                deleted
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
